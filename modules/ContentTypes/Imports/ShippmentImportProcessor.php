<?php

namespace Modules\ContentTypes\Imports;

use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\ContentTypes\Entities\Address;
use Modules\ContentTypes\Entities\Shippment;
use Modules\ContentTypes\Entities\ShippmentImport;
use Modules\ContentTypes\Entities\ShippmentImportRow;
use Modules\ContentTypes\Entities\ShippmentService;
use Modules\ContentTypes\Jobs\ShippmentMatchTransportar;
use Modules\ContentTypes\Services\DestinationCityVerifier;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ShippmentImportProcessor
{
    private $destinationCityVerifier;

    public function __construct(DestinationCityVerifier $destinationCityVerifier)
    {
        $this->destinationCityVerifier = $destinationCityVerifier;
    }

    public function process(ShippmentImport $import): bool
    {
        $deadline = microtime(true) + 30;
        $handledInThisRun = 0;
        app()->setLocale(in_array($import->locale, ['ar', 'en']) ? $import->locale : 'en');
        $path = \Storage::disk($import->disk)->path($import->file_path);
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        try {
            $schemaVersion = $spreadsheet->getProperties()->getCustomPropertyValue('shipment_import_schema_version');
            if ($schemaVersion !== null && (string) $schemaVersion !== ShippmentImportDefinition::SCHEMA_VERSION) {
                throw ValidationException::withMessages([
                    'file' => [__('contenttypes::strings.shipment_import.errors.invalid_template_version')],
                ]);
            }

            $shipmentRows = $this->readRows($spreadsheet->getSheet(0), ShippmentImportDefinition::shipmentColumns());
            $addressRows = $this->readRows($spreadsheet->getSheet(1), ShippmentImportDefinition::deliveryAddressColumns());
            if (empty($shipmentRows)) {
                throw ValidationException::withMessages(['file' => [__('contenttypes::strings.shipment_import.errors.empty_file')]]);
            }
            if (count($shipmentRows) > 500 || count($addressRows) > 500) {
                throw ValidationException::withMessages(['file' => [__('contenttypes::strings.shipment_import.errors.too_many_rows')]]);
            }
            $addressesByKey = collect($addressRows)->groupBy(function ($row) {
                return trim((string) ($row['data']['shipment_import_key'] ?? ''));
            });

            $import->update(['total_rows' => count($shipmentRows)]);
            $duplicateKeys = collect($shipmentRows)->pluck('data.import_key')->map(function ($key) {
                return trim((string) $key);
            })->filter()->countBy()->filter(function ($count) {
                return $count > 1;
            })->keys()->all();

            foreach ($shipmentRows as $row) {
                if ($handledInThisRun > 0 && microtime(true) >= $deadline) {
                    return false;
                }
                $this->processRow($import, $row, $addressesByKey->get(trim((string) ($row['data']['import_key'] ?? '')), collect())->all(), $duplicateKeys);
                $handledInThisRun++;
            }

            $knownKeys = collect($shipmentRows)->pluck('data.import_key')->map(function ($key) {
                return trim((string) $key);
            })->filter()->unique();
            $orphanRows = collect($addressRows)->reject(function ($row) use ($knownKeys) {
                return $knownKeys->contains(trim((string) ($row['data']['shipment_import_key'] ?? '')));
            });
            if ($orphanRows->isNotEmpty()) {
                $import->error_message = __('contenttypes::strings.shipment_import.errors.orphan_addresses', [
                    'rows' => $orphanRows->pluck('row_number')->implode(', '),
                ]);
            }

            $import->status = $import->failed_count > 0 || $orphanRows->isNotEmpty()
                ? 'COMPLETED_WITH_ERRORS'
                : 'COMPLETED';
            $import->completed_at = now();
            $import->save();
            return true;
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    private function processRow(ShippmentImport $import, array $sourceRow, array $addressRows, array $duplicateKeys): void
    {
        $source = $sourceRow['data'];
        $key = trim((string) ($source['import_key'] ?? ''));
        $log = ShippmentImportRow::firstOrNew(['import_id' => $import->id, 'row_number' => $sourceRow['row_number']]);
        if ($log->exists && in_array($log->status, ['SUCCESS', 'FAILED'], true)) {
            $this->refreshCounters($import);
            return;
        }
        $log->fill(['import_key' => $key ?: null, 'operation' => empty($source['id']) ? 'CREATE' : 'UPDATE', 'status' => 'PENDING', 'payload' => ['shipment' => $source, 'delivery_addresses' => array_column($addressRows, 'data')], 'errors' => null]);
        $log->save();

        $tempFiles = [];
        try {
            if ($key !== '' && in_array($key, $duplicateKeys, true)) {
                throw ValidationException::withMessages(['import_key' => [__('contenttypes::strings.shipment_import.errors.duplicate_import_key')]]);
            }

            $shipment = $this->resolveShipment($import, $source);
            $this->validateAddressRows($source, $addressRows);
            $data = $this->mapData($source, $addressRows, $shipment);
            $this->validateReferences($source, $data, $shipment);
            app(ShippmentService::class)->validator('update', $data)->validate();
            $this->verifyDeliveryCities($data);
            $this->attachRemoteFiles($source, $data, $tempFiles, $shipment);

            $service = app(ShippmentService::class);
            $validator = $service->validator($shipment ? 'update' : 'create', $data);
            $validator->validate();

            $savedShipments = DB::transaction(function () use ($import, $shipment, $data) {
                if ($shipment) {
                    $ownedShipment = Shippment::whereKey($shipment->id)
                        ->where('company_user_id', $import->user_id)
                        ->lockForUpdate()
                        ->first();
                    if (! $ownedShipment) {
                        throw ValidationException::withMessages(['id' => [__('contenttypes::strings.shipment_import.errors.shipment_not_editable')]]);
                    }
                    $this->lockOwnedLoadingAddress($data, $ownedShipment->id);
                    $this->lockOwnedDeliveryAddresses($data, $ownedShipment->id);
                    $saved = app(ShippmentService::class)->findOrFail($ownedShipment->id)->update($data);
                    $this->assertShipmentOwner($saved, $import->user_id);
                    return collect([$saved]);
                }
                return collect(range(1, (int) $data['number_of_trucks']))->map(function () use ($data, $import) {
                    $saved = app(ShippmentService::class)->create($data);
                    $this->assertShipmentOwner($saved, $import->user_id);
                    return $saved;
                });
            });

            $log->update([
                'status' => 'SUCCESS',
                'shippment_id' => optional($savedShipments->first())->id,
                'shippment_ids' => $savedShipments->pluck('id')->all(),
                'errors' => null,
            ]);

            $savedShipments->where('sys_status', 'MATCHING')->each(function ($saved) {
                try {
                    ShippmentMatchTransportar::dispatch($saved);
                } catch (\Throwable $exception) {
                    // The shipment is already committed. A downstream queue failure must
                    // not mark the import row as failed and cause a duplicate on retry.
                    report($exception);
                }
            });
        } catch (ValidationException $exception) {
            $log->update(['status' => 'FAILED', 'errors' => $exception->errors()]);
        } catch (\Throwable $exception) {
            report($exception);
            $log->update(['status' => 'FAILED', 'errors' => ['row' => [__('contenttypes::strings.shipment_import.errors.unexpected_row_error')]]]);
        } finally {
            foreach ($tempFiles as $tempFile) {
                if (is_file($tempFile)) {
                    @unlink($tempFile);
                }
            }
            $this->refreshCounters($import);
        }
    }

    private function refreshCounters(ShippmentImport $import): void
    {
        $rows = ShippmentImportRow::where('import_id', $import->id)->get();
        $created = $rows->where('status', 'SUCCESS')->where('operation', 'CREATE')->sum(function ($row) {
            return count($row->shippment_ids ?: array_filter([$row->shippment_id]));
        });
        $import->update([
            'processed_rows' => $rows->whereIn('status', ['SUCCESS', 'FAILED'])->count(),
            'created_count' => $created,
            'updated_count' => $rows->where('status', 'SUCCESS')->where('operation', 'UPDATE')->count(),
            'failed_count' => $rows->where('status', 'FAILED')->count(),
        ]);
        $import->refresh();
    }

    private function resolveShipment(ShippmentImport $import, array $source): ?Shippment
    {
        if (empty($source['id'])) {
            return null;
        }

        if (! app(ShippmentService::class)->can('update')) {
            throw ValidationException::withMessages(['id' => [__('contenttypes::strings.shipment_import.errors.update_permission_required')]]);
        }

        Validator::make($source, ['id' => 'integer|min:1'])->validate();
        $query = Shippment::whereKey((int) $source['id'])
            ->where('company_user_id', $import->user_id);
        $shipment = $query->first();
        if (! $shipment) {
            throw ValidationException::withMessages(['id' => [__('contenttypes::strings.shipment_import.errors.shipment_not_editable')]]);
        }
        if (! in_array($shipment->sys_status, ShippmentImportDefinition::EDITABLE_STATUSES, true)) {
            throw ValidationException::withMessages(['id' => [__('contenttypes::strings.shipment_import.errors.shipment_status_not_editable', [
                'status' => $shipment->sys_status,
            ])]]);
        }
        if (Address::where('related_type', 'SHIPPMENT')
            ->where('related_id', $shipment->id)
            ->where('address_tag', 'SHIPPMENT_LOADING_POINT')
            ->count() > 1) {
            throw ValidationException::withMessages([
                'loading_address' => [__('contenttypes::strings.shipment_import.errors.multiple_loading_addresses')],
            ]);
        }

        return $shipment;
    }

    private function mapData(array $source, array $addressRows, ?Shippment $shipment): array
    {
        $delivery = collect($addressRows)->sortBy(function ($row) {
            return (int) ($row['data']['sequence'] ?? 0);
        })->values()->map(function ($row) use ($shipment) {
            $item = $row['data'];
            return [
                'id' => $this->validatedAddressId($item['address_id'] ?? null, $shipment),
                'primary_contact_company_name' => $item['company_name'] ?? null,
                'primary_contact_name' => $item['contact_name'] ?? null,
                'primary_contact_email' => $item['contact_email'] ?? null,
                'primary_contact_phone_number' => $this->stringValue($item['contact_phone'] ?? null),
                'lat_lng' => ($item['lat'] ?? '') . ',' . ($item['lng'] ?? ''),
                'lat' => $item['lat'] ?? null,
                'lng' => $item['lng'] ?? null,
                'full_address' => $item['full_address'] ?? null,
                'round_trip' => strtoupper(trim((string) ($item['round_trip'] ?? ''))),
                'public_notes' => $item['public_notes'] ?? null,
                'private_notes' => $item['private_notes'] ?? null,
                'removed' => 'N',
                '_sheet_row' => $row['row_number'],
            ];
        })->all();

        return [
            'from_city_id' => $source['from_city_id'] ?? null,
            'to_city_id' => $source['to_city_id'] ?? null,
            'loading_type' => trim((string) ($source['loading_type'] ?? '')),
            'commodity_id' => collect(preg_split('/\s*,\s*/', (string) ($source['commodity_ids'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))->unique()->values()->all(),
            'load_size_id' => $source['load_size_id'] ?? null,
            'load_type_id' => $source['load_type_id'] ?? null,
            'truck_type_id' => $source['truck_type_id'] ?? null,
            'total_weight' => $source['total_weight'] ?? null,
            'declared_order_value' => $source['declared_order_value'] ?? null,
            'number_of_pallets' => $source['number_of_pallets'] ?? null,
            'number_of_trucks' => $source['number_of_trucks'] ?? null,
            'temperature' => $source['temperature'] ?? null,
            'equipment_length' => $source['equipment_length'] ?? null,
            'equipment_width' => $source['equipment_width'] ?? null,
            'delivery_required_at' => $this->dateValue($source['delivery_required_at'] ?? null),
            'is_draft' => strtoupper(trim((string) ($source['is_draft'] ?? ''))) === 'YES' ? 'Y' : 'N',
            'is_rfq' => strtoupper(trim((string) ($source['is_rfq'] ?? ''))) === 'YES' ? 'Y' : 'N',
            'public_notes' => $source['public_notes'] ?? null,
            'private_notes' => $source['private_notes'] ?? null,
            'loading_address' => [
                'id' => $shipment && $shipment->loadingLocation ? $shipment->loadingLocation->id : null,
                'primary_contact_company_name' => $source['loading_company_name'] ?? null,
                'primary_contact_name' => $source['loading_contact_name'] ?? null,
                'primary_contact_email' => $source['loading_contact_email'] ?? null,
                'primary_contact_phone_number' => $this->stringValue($source['loading_contact_phone'] ?? null),
                'lat_lng' => ($source['loading_lat'] ?? '') . ',' . ($source['loading_lng'] ?? ''),
                'lat' => $source['loading_lat'] ?? null,
                'lng' => $source['loading_lng'] ?? null,
                'full_address' => $source['loading_full_address'] ?? null,
                'public_notes' => $source['loading_public_notes'] ?? null,
                'private_notes' => $source['loading_private_notes'] ?? null,
            ],
            'offloading_address' => $delivery,
        ];
    }

    private function validateReferences(array $source, array $data, ?Shippment $shipment): void
    {
        $rules = [
            'import_key' => 'required|string|max:100',
            'from_city_id' => 'required|integer|exists:cities,id,country_id,191',
            'to_city_id' => 'required|integer|exists:cities,id,country_id,191',
            'loading_type' => 'required|in:CITY_TO_CITY,CITY_TO_CITY_MULTIPLE_LOCATIONS',
            'commodity_ids' => 'required',
            'load_size_id' => ['required', Rule::exists('contents', 'id')->where(function ($query) { $query->where('type', 'load-size')->where('sys_status', 'ACTIVE')->whereNull('deleted_at'); })],
            'load_type_id' => ['required', Rule::exists('contents', 'id')->where(function ($query) { $query->where('type', 'load-type')->where('sys_status', 'ACTIVE')->whereNull('deleted_at'); })],
            'truck_type_id' => ['required', Rule::exists('contents', 'id')->where(function ($query) { $query->where('type', 'truck-type')->where('sys_status', 'ACTIVE')->whereNull('deleted_at'); })],
            'is_draft' => 'required|in:YES,NO',
            'is_rfq' => 'required|in:YES,NO',
            'number_of_trucks' => ($shipment ? 'nullable' : 'required') . '|integer|min:1|max:100',
        ];
        foreach (range(1, 4) as $index) {
            $rules['attachment_' . $index . '_url'] = 'nullable|url';
        }
        Validator::make($source, $rules, [], ShippmentImportDefinition::attributes(ShippmentImportDefinition::shipmentColumns()))->validate();

        Validator::make($data, [
            'commodity_id.*' => [Rule::exists('contents', 'id')->where(function ($query) { $query->where('type', 'commodity')->where('sys_status', 'ACTIVE')->whereNull('deleted_at'); })],
            'offloading_address' => 'required|array|min:1',
            'offloading_address.*.primary_contact_email' => 'required|email',
        ], [], [
            'commodity_id.*' => __('contenttypes::inputs.commodity_id.label'),
            'offloading_address' => __('contenttypes::strings.shippments.offloading_address'),
            'offloading_address.*.primary_contact_email' => __('contenttypes::inputs.address_email.label'),
        ])->after(function ($validator) use ($data) {
            $count = count($data['offloading_address'] ?? []);
            if (($data['loading_type'] ?? null) === 'CITY_TO_CITY' && $count !== 1) {
                $validator->errors()->add('offloading_address', __('contenttypes::strings.shipment_import.errors.single_address_required'));
            }
        })->validate();
    }

    private function validateAddressRows(array $source, array $addressRows): void
    {
        $sequences = [];
        foreach ($addressRows as $index => $row) {
            $address = $row['data'];
            Validator::make($address, [
                'shipment_import_key' => 'required|string|max:100',
                'shipment_id' => 'nullable|integer|min:1',
                'address_id' => 'nullable|integer|min:1',
                'sequence' => 'required|integer|min:1',
                'round_trip' => 'required|in:YES,NO',
            ], [], ShippmentImportDefinition::attributes(ShippmentImportDefinition::deliveryAddressColumns()))->validate();

            if (! empty($source['id']) && ! empty($address['shipment_id']) && (int) $source['id'] !== (int) $address['shipment_id']) {
                throw ValidationException::withMessages([
                    'offloading_address.' . $index . '.shipment_id' => [__('contenttypes::strings.shipment_import.errors.address_shipment_mismatch')],
                ]);
            }
            if (empty($source['id']) && ! empty($address['shipment_id'])) {
                throw ValidationException::withMessages([
                    'offloading_address.' . $index . '.shipment_id' => [__('contenttypes::strings.shipment_import.errors.new_address_shipment_id')],
                ]);
            }
            $sequence = (int) ($address['sequence'] ?? 0);
            if (in_array($sequence, $sequences, true)) {
                throw ValidationException::withMessages([
                    'offloading_address.' . $index . '.sequence' => [__('contenttypes::strings.shipment_import.errors.duplicate_address_sequence')],
                ]);
            }
            $sequences[] = $sequence;
        }
    }

    private function lockOwnedDeliveryAddresses(array $data, int $shipmentId): void
    {
        $ids = collect($data['offloading_address'] ?? [])->pluck('id')->filter()->map(function ($id) {
            return (int) $id;
        })->unique()->values();
        if ($ids->isEmpty()) {
            return;
        }

        $ownedCount = Address::whereIn('id', $ids)
            ->where('related_type', 'SHIPPMENT')
            ->where('related_id', $shipmentId)
            ->where('address_tag', 'SHIPPMENT_OFFLOADING_POINT')
            ->lockForUpdate()
            ->get(['id'])
            ->count();
        if ($ownedCount !== $ids->count()) {
            throw ValidationException::withMessages([
                'address_id' => [__('contenttypes::strings.shipment_import.errors.address_not_owned')],
            ]);
        }
    }

    private function lockOwnedLoadingAddress(array $data, int $shipmentId): void
    {
        $addresses = Address::where('related_type', 'SHIPPMENT')
            ->where('related_id', $shipmentId)
            ->where('address_tag', 'SHIPPMENT_LOADING_POINT')
            ->lockForUpdate()
            ->get(['id']);
        if ($addresses->count() > 1) {
            throw ValidationException::withMessages([
                'loading_address' => [__('contenttypes::strings.shipment_import.errors.multiple_loading_addresses')],
            ]);
        }
        $expectedId = $data['loading_address']['id'] ?? null;
        if ($expectedId && ! $addresses->contains('id', (int) $expectedId)) {
            throw ValidationException::withMessages([
                'loading_address' => [__('contenttypes::strings.shipment_import.errors.address_not_owned')],
            ]);
        }
    }

    private function assertShipmentOwner(Shippment $shipment, int $userId): void
    {
        if ((int) $shipment->company_user_id !== $userId) {
            throw new \RuntimeException('Shipment import ownership invariant failed.');
        }
    }

    private function verifyDeliveryCities(array &$data): void
    {
        foreach ($data['offloading_address'] as $index => &$address) {
            try {
                $valid = $this->destinationCityVerifier->verify((int) $data['to_city_id'], $address['lat'], $address['lng']);
            } catch (\Throwable $exception) {
                report($exception);
                throw ValidationException::withMessages([
                    'offloading_address.' . $index . '.lat_lng' => [__('contenttypes::strings.shippments.address_city_check_failed')],
                ]);
            }
            if (! $valid) {
                throw ValidationException::withMessages([
                    'offloading_address.' . $index . '.lat_lng' => [__('contenttypes::strings.shippments.address_outside_destination_city')],
                ]);
            }
            $address['destination_city_token'] = ShippmentService::destinationCityVerificationToken($data['to_city_id'], $address['lat'], $address['lng']);
        }
        unset($address);
    }

    private function validatedAddressId($id, ?Shippment $shipment)
    {
        if (empty($id)) {
            return null;
        }
        if (! $shipment || ! Address::whereKey($id)->where('related_type', 'SHIPPMENT')->where('related_id', $shipment->id)->where('address_tag', 'SHIPPMENT_OFFLOADING_POINT')->exists()) {
            throw ValidationException::withMessages(['address_id' => [__('contenttypes::strings.shipment_import.errors.address_not_owned')]]);
        }
        return (int) $id;
    }

    private function attachRemoteFiles(array $source, array &$data, array &$tempFiles, ?Shippment $shipment): void
    {
        foreach (range(1, 4) as $index) {
            $url = trim((string) ($source['attachment_' . $index . '_url'] ?? ''));
            if ($url === '') {
                continue;
            }
            if ($shipment) {
                $file = $shipment->{'ATTACHMENT_' . $index};
                $currentUrl = $file ? app('GraphManager')->src('original', 'files', $file->file_path) : null;
                if ($currentUrl && hash_equals($currentUrl, $url)) {
                    continue;
                }
            }
            $data['ATTACHMENT_' . $index] = $this->downloadPublicFile($url, $tempFiles);
        }
    }

    private function downloadPublicFile(string $url, array &$tempFiles): HttpUploadedFile
    {
        $parts = parse_url($url);
        if (! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) || empty($parts['host']) || isset($parts['user']) || ! $this->isPublicHost($parts['host'])) {
            throw ValidationException::withMessages(['attachments' => [__('contenttypes::strings.shipment_import.errors.unsafe_attachment_url')]]);
        }

        $googleDriveFileId = $this->googleDriveFileId($parts);
        $isGoogleDrive = $googleDriveFileId !== null;
        if ($this->isGoogleDriveHost($parts['host']) && ! $isGoogleDrive) {
            throw ValidationException::withMessages(['attachments' => [__('contenttypes::strings.shipment_import.errors.google_drive_file_not_public')]]);
        }

        $downloadUrl = $isGoogleDrive
            ? $this->googleDriveDownloadUrl($googleDriveFileId, $parts)
            : $url;
        $redirectOptions = false;
        if ($isGoogleDrive) {
            $redirectOptions = [
                'max' => 5,
                'strict' => true,
                'referer' => false,
                'on_redirect' => function ($request, $response, $uri) {
                    if (! $this->isAllowedGoogleDriveDownloadHost($uri->getHost())) {
                        throw ValidationException::withMessages([
                            'attachments' => [__('contenttypes::strings.shipment_import.errors.unsafe_attachment_url')],
                        ]);
                    }
                },
            ];
        }

        $response = Http::timeout(10)->withOptions([
            'connect_timeout' => 5,
            'allow_redirects' => $redirectOptions,
        ])->get($downloadUrl);
        $contentType = strtolower(trim(explode(';', (string) $response->header('Content-Type'))[0]));
        if ($isGoogleDrive && (! $response->successful() || $contentType === 'text/html')) {
            throw ValidationException::withMessages(['attachments' => [__('contenttypes::strings.shipment_import.errors.google_drive_file_not_public')]]);
        }
        if (! $response->successful() || strlen($response->body()) > 10 * 1024 * 1024) {
            throw ValidationException::withMessages(['attachments' => [__('contenttypes::strings.shipment_import.errors.attachment_download_failed')]]);
        }

        $name = $this->downloadedFileName($response, $parts, $contentType);
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (! in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'], true)) {
            throw ValidationException::withMessages(['attachments' => [__('contenttypes::strings.shipment_import.errors.attachment_type_invalid')]]);
        }

        $temp = tempnam(sys_get_temp_dir(), 'shippment-import-');
        file_put_contents($temp, $response->body());
        $tempFiles[] = $temp;

        return new HttpUploadedFile($temp, $name, $response->header('Content-Type'), null, true);
    }

    private function googleDriveFileId(array $parts): ?string
    {
        if (! $this->isGoogleDriveHost($parts['host'] ?? '')) {
            return null;
        }

        $path = (string) ($parts['path'] ?? '');
        if (preg_match('#^/file/d/([a-zA-Z0-9_-]+)(?:/|$)#', $path, $matches)) {
            return $matches[1];
        }

        parse_str((string) ($parts['query'] ?? ''), $query);
        if (in_array($path, ['/open', '/uc'], true) && preg_match('/^[a-zA-Z0-9_-]+$/', (string) ($query['id'] ?? ''))) {
            return (string) $query['id'];
        }

        return null;
    }

    private function googleDriveDownloadUrl(string $fileId, array $parts): string
    {
        parse_str((string) ($parts['query'] ?? ''), $query);
        $parameters = [
            'export' => 'download',
            'id' => $fileId,
            'confirm' => 't',
        ];
        if (! empty($query['resourcekey']) && preg_match('/^[a-zA-Z0-9_-]+$/', (string) $query['resourcekey'])) {
            $parameters['resourcekey'] = $query['resourcekey'];
        }

        return 'https://drive.usercontent.google.com/download?' . http_build_query($parameters);
    }

    private function downloadedFileName($response, array $parts, string $contentType): string
    {
        $disposition = (string) $response->header('Content-Disposition');
        if (preg_match("/filename\\*=UTF-8''([^;]+)/i", $disposition, $matches)) {
            return basename(rawurldecode(trim(trim($matches[1]), "\"'")));
        }
        if (preg_match('/filename="?([^";]+)"?/i', $disposition, $matches)) {
            return basename(trim($matches[1]));
        }

        $name = basename(urldecode($parts['path'] ?? 'attachment')) ?: 'attachment';
        if (pathinfo($name, PATHINFO_EXTENSION) !== '') {
            return $name;
        }

        $extensions = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        ];

        return isset($extensions[$contentType]) ? 'attachment.' . $extensions[$contentType] : $name;
    }

    private function isGoogleDriveHost(string $host): bool
    {
        return strtolower($host) === 'drive.google.com';
    }

    private function isAllowedGoogleDriveDownloadHost(string $host): bool
    {
        $host = strtolower($host);
        $googleUserContentSuffix = '.googleusercontent.com';

        return in_array($host, ['drive.google.com', 'drive.usercontent.google.com'], true)
            || substr($host, -strlen($googleUserContentSuffix)) === $googleUserContentSuffix;
    }

    private function isPublicHost(string $host): bool
    {
        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);
        return ! empty($ips) && collect($ips)->every(function ($ip) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
        });
    }

    private function readRows($sheet, array $columns): array
    {
        $expected = ShippmentImportDefinition::keys($columns);
        $actual = [];
        foreach ($expected as $index => $unused) {
            $actual[] = trim((string) $sheet->getCellByColumnAndRow($index + 1, 2)->getValue());
        }
        if ($actual !== $expected) {
            throw ValidationException::withMessages(['file' => [__('contenttypes::strings.shipment_import.errors.invalid_columns')]]);
        }

        $rows = [];
        for ($row = ShippmentImportDefinition::FIRST_DATA_ROW; $row <= $sheet->getHighestDataRow(); $row++) {
            $data = [];
            foreach ($expected as $index => $key) {
                $data[$key] = $sheet->getCellByColumnAndRow($index + 1, $row)->getValue();
            }
            if (collect($data)->filter(function ($value) { return $value !== null && trim((string) $value) !== ''; })->isEmpty()) {
                continue;
            }
            $rows[] = ['row_number' => $row, 'data' => $data];
        }
        return $rows;
    }

    private function dateValue($value)
    {
        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
        }
        return trim((string) $value);
    }

    private function stringValue($value): string
    {
        return is_float($value) ? rtrim(rtrim(sprintf('%.10F', $value), '0'), '.') : trim((string) $value);
    }
}
