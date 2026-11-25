<?php

namespace Modules\ContentTypes\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\ContentTypes\Imports\ShippmentImportDefinition;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShippmentImportExport
{
    private $template;

    public function __construct(ShippmentImportTemplate $template)
    {
        $this->template = $template;
    }

    public function build(Collection $shipments): Spreadsheet
    {
        $addressCount = $shipments->sum(function ($shipment) {
            return $shipment->offloadingLocations->count();
        });

        if ($shipments->count() > 500 || $addressCount > 500) {
            throw new \LengthException(__('contenttypes::strings.shipment_import.errors.export_too_many_rows'));
        }

        $spreadsheet = $this->template->build();
        $spreadsheet->getProperties()->setTitle(__('contenttypes::strings.shipment_import.export_title'));

        $shipmentSheet = $spreadsheet->getSheet(0);
        $addressSheet = $spreadsheet->getSheet(1);
        $addressRow = ShippmentImportDefinition::FIRST_DATA_ROW;

        foreach ($shipments->values() as $index => $shipment) {
            $importKey = 'SHIPMENT-' . $shipment->id;
            $this->writeRow(
                $shipmentSheet,
                ShippmentImportDefinition::FIRST_DATA_ROW + $index,
                ShippmentImportDefinition::shipmentColumns(),
                $this->shipmentData($shipment, $importKey)
            );

            foreach ($shipment->offloadingLocations as $sequence => $address) {
                $this->writeRow(
                    $addressSheet,
                    $addressRow++,
                    ShippmentImportDefinition::deliveryAddressColumns(),
                    $this->addressData($shipment, $address, $importKey, $sequence + 1)
                );
            }
        }

        return $spreadsheet;
    }

    private function shipmentData($shipment, string $importKey): array
    {
        $loadingAddress = $shipment->loadingLocation;
        $data = [
            'import_key' => $importKey,
            'id' => $shipment->id,
            'from_city_id' => $shipment->from_city_id,
            'to_city_id' => $shipment->to_city_id,
            'loading_type' => $shipment->type,
            'commodity_ids' => $shipment->commodities->pluck('commodity_id')->unique()->implode(','),
            'load_size_id' => $shipment->load_size_id,
            'load_type_id' => $shipment->load_type_id,
            'truck_type_id' => $shipment->truck_type_id,
            'total_weight' => $shipment->total_weight,
            'declared_order_value' => $shipment->decalred_value,
            'number_of_pallets' => $shipment->number_of_pallets,
            'number_of_trucks' => $shipment->number_of_trucks ?: 1,
            'temperature' => $shipment->temperature,
            'equipment_length' => $shipment->equipment_length,
            'equipment_width' => $shipment->equipment_width,
            'delivery_required_at' => $shipment->delivery_required_at
                ? Carbon::parse($shipment->delivery_required_at)->format('Y-m-d')
                : null,
            'is_draft' => $shipment->sys_status === 'DRAFT' ? 'YES' : 'NO',
            'is_rfq' => $shipment->is_rfq === 'YES' ? 'YES' : 'NO',
            'public_notes' => $shipment->public_notes,
            'private_notes' => $shipment->private_notes,
            'loading_company_name' => optional($loadingAddress)->primary_contact_company_name,
            'loading_contact_name' => optional($loadingAddress)->primary_contact_name,
            'loading_contact_email' => optional($loadingAddress)->primary_contact_email,
            'loading_contact_phone' => optional($loadingAddress)->primary_contact_phone_number,
            'loading_lat' => optional($loadingAddress)->lat,
            'loading_lng' => optional($loadingAddress)->lng,
            'loading_full_address' => optional($loadingAddress)->full_address,
            'loading_public_notes' => optional($loadingAddress)->public_notes,
            'loading_private_notes' => optional($loadingAddress)->private_notes,
        ];

        foreach (range(1, 4) as $index) {
            $file = $shipment->getRelation('ATTACHMENT_' . $index);
            $data['attachment_' . $index . '_url'] = $file
                ? app('GraphManager')->src('original', 'files', $file->file_path)
                : null;
        }

        return $data;
    }

    private function addressData($shipment, $address, string $importKey, int $sequence): array
    {
        return [
            'shipment_import_key' => $importKey,
            'shipment_id' => $shipment->id,
            'address_id' => $address->id,
            'sequence' => $sequence,
            'company_name' => $address->primary_contact_company_name,
            'contact_name' => $address->primary_contact_name,
            'contact_email' => $address->primary_contact_email,
            'contact_phone' => $address->primary_contact_phone_number,
            'lat' => $address->lat,
            'lng' => $address->lng,
            'full_address' => $address->full_address,
            'round_trip' => in_array($address->round_trip, ['YES', 'NO'], true) ? $address->round_trip : 'NO',
            'public_notes' => $address->public_notes,
            'private_notes' => $address->private_notes,
        ];
    }

    private function writeRow(Worksheet $sheet, int $row, array $columns, array $data): void
    {
        $numericKeys = [
            'id', 'from_city_id', 'to_city_id', 'load_size_id', 'load_type_id', 'truck_type_id',
            'total_weight', 'declared_order_value', 'number_of_pallets', 'number_of_trucks',
            'temperature', 'equipment_length', 'equipment_width', 'loading_lat', 'loading_lng',
            'shipment_id', 'address_id', 'sequence', 'lat', 'lng',
        ];

        foreach ($columns as $index => $column) {
            $value = $data[$column['key']] ?? null;
            if ($value === null || $value === '') {
                continue;
            }

            $cell = Coordinate::stringFromColumnIndex($index + 1) . $row;
            if (in_array($column['key'], $numericKeys, true) && is_numeric($value)) {
                $sheet->setCellValueExplicit($cell, $value + 0, DataType::TYPE_NUMERIC);
                continue;
            }
            $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
        }
    }
}
