<?php

namespace Modules\ContentTypes\Exports;

use Modules\ContentManager\Entities\Content;
use Modules\SystemCore\Entities\City;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShippmentImportTemplate
{
    private const FIRST_DATA_ROW = 3;
    private const LAST_DATA_ROW = 502;

    private $locale;

    public function __construct()
    {
        $this->locale = in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'en';
    }

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle(__('contenttypes::strings.shipment_import.template_title'))
            ->setSubject(__('contenttypes::strings.shipment_import.template_description'))
            ->setCustomProperty('shipment_import_schema_version', '1');

        $shipments = $spreadsheet->getActiveSheet();
        $shipments->setTitle(__('contenttypes::strings.shipment_import.shipments_sheet'));
        $this->configureDataSheet($shipments, $this->shipmentColumns());
        $this->configureShipmentValidations($shipments, $this->shipmentColumns());

        $addresses = $spreadsheet->createSheet();
        $addresses->setTitle(__('contenttypes::strings.shipment_import.delivery_addresses_sheet'));
        $this->configureDataSheet($addresses, $this->deliveryAddressColumns());
        $this->addListValidation($addresses, $this->columnIndex($this->deliveryAddressColumns(), 'round_trip'), ['YES', 'NO']);

        $instructions = $spreadsheet->createSheet();
        $instructions->setTitle(__('contenttypes::strings.shipment_import.instructions_sheet'));
        $this->configureInstructionsSheet($instructions);

        $references = $spreadsheet->createSheet();
        $references->setTitle(__('contenttypes::strings.shipment_import.reference_sheet'));
        $this->configureReferenceSheet($references);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function shipmentColumns(): array
    {
        $loading = __('contenttypes::strings.shippments.loading_address');

        return [
            $this->column('import_key', __('contenttypes::strings.shipment_import.columns.import_key'), true, 18),
            $this->column('id', __('contenttypes::strings.shipment_import.columns.shipment_id'), false, 12),
            $this->column('from_city_id', __('contenttypes::inputs.from_city_id.label'), true, 18),
            $this->column('to_city_id', __('contenttypes::inputs.to_city_id.label'), true, 18),
            $this->column('loading_type', __('contenttypes::inputs.loading_type.label'), true, 30),
            $this->column('commodity_ids', __('contenttypes::strings.shipment_import.columns.commodity_ids'), true, 22),
            $this->column('load_size_id', __('contenttypes::inputs.load_size_id.label'), true, 18),
            $this->column('load_type_id', __('contenttypes::inputs.load_type_id.label'), true, 18),
            $this->column('truck_type_id', __('contenttypes::inputs.truck_type_id.label'), true, 18),
            $this->column('total_weight', __('contenttypes::inputs.weight.label'), true, 16),
            $this->column('declared_order_value', __('contenttypes::inputs.declared_order_value.label'), true, 20),
            $this->column('number_of_pallets', __('contenttypes::inputs.number_of_pallets.label'), true, 17),
            $this->column('number_of_trucks', __('contenttypes::inputs.number_of_trucks.label'), true, 17),
            $this->column('temperature', __('contenttypes::inputs.temperature.label'), true, 18),
            $this->column('equipment_length', __('contenttypes::inputs.equipment_length.label'), true, 18),
            $this->column('equipment_width', __('contenttypes::inputs.equipment_width.label'), true, 18),
            $this->column('delivery_required_at', __('contenttypes::inputs.delivery_required_at.label'), true, 20),
            $this->column('is_draft', __('contenttypes::strings.shipment_import.columns.is_draft'), true, 14),
            $this->column('is_rfq', __('contenttypes::strings.shipment_import.columns.is_rfq'), true, 14),
            $this->column('public_notes', __('contenttypes::inputs.public_notes.label'), false, 28),
            $this->column('private_notes', __('contenttypes::inputs.private_notes.label'), false, 28),
            $this->column('loading_company_name', $loading . ' - ' . __('contenttypes::inputs.address_name.label'), true, 24),
            $this->column('loading_contact_name', $loading . ' - ' . __('contenttypes::inputs.address_primary_contact.label'), true, 24),
            $this->column('loading_contact_email', $loading . ' - ' . __('contenttypes::inputs.address_email.label'), true, 26),
            $this->column('loading_contact_phone', $loading . ' - ' . __('contenttypes::inputs.address_phone_number.label'), true, 22),
            $this->column('loading_lat', $loading . ' - ' . __('contenttypes::inputs.lat.label'), true, 18),
            $this->column('loading_lng', $loading . ' - ' . __('contenttypes::inputs.lng.label'), true, 18),
            $this->column('loading_full_address', $loading . ' - ' . __('contenttypes::inputs.full_address.label'), true, 34),
            $this->column('loading_public_notes', $loading . ' - ' . __('contenttypes::inputs.public_notes.label'), false, 28),
            $this->column('loading_private_notes', $loading . ' - ' . __('contenttypes::inputs.private_notes.label'), false, 28),
            $this->column('attachment_1_url', __('contenttypes::strings.shipment_import.columns.attachment_1_url'), false, 32),
            $this->column('attachment_2_url', __('contenttypes::strings.shipment_import.columns.attachment_2_url'), false, 32),
            $this->column('attachment_3_url', __('contenttypes::strings.shipment_import.columns.attachment_3_url'), false, 32),
            $this->column('attachment_4_url', __('contenttypes::strings.shipment_import.columns.attachment_4_url'), false, 32),
        ];
    }

    private function deliveryAddressColumns(): array
    {
        return [
            $this->column('shipment_import_key', __('contenttypes::strings.shipment_import.columns.shipment_import_key'), true, 20),
            $this->column('shipment_id', __('contenttypes::strings.shipment_import.columns.shipment_id'), false, 14),
            $this->column('address_id', __('contenttypes::strings.shipment_import.columns.address_id'), false, 14),
            $this->column('sequence', __('contenttypes::strings.shipment_import.columns.sequence'), true, 12),
            $this->column('company_name', __('contenttypes::inputs.address_name.label'), true, 24),
            $this->column('contact_name', __('contenttypes::inputs.address_primary_contact.label'), true, 24),
            $this->column('contact_email', __('contenttypes::inputs.address_email.label'), true, 26),
            $this->column('contact_phone', __('contenttypes::inputs.address_phone_number.label'), true, 22),
            $this->column('lat', __('contenttypes::inputs.lat.label'), true, 18),
            $this->column('lng', __('contenttypes::inputs.lng.label'), true, 18),
            $this->column('full_address', __('contenttypes::inputs.full_address.label'), true, 34),
            $this->column('round_trip', __('contenttypes::inputs.round_trip.label'), true, 15),
            $this->column('public_notes', __('contenttypes::inputs.public_notes.label'), false, 28),
            $this->column('private_notes', __('contenttypes::inputs.private_notes.label'), false, 28),
        ];
    }

    private function column(string $key, string $label, bool $required, int $width): array
    {
        return compact('key', 'label', 'required', 'width');
    }

    private function configureDataSheet(Worksheet $sheet, array $columns): void
    {
        $sheet->setRightToLeft($this->locale === 'ar');
        $sheet->freezePane('A3');
        $sheet->getRowDimension(1)->setRowHeight(38);
        $sheet->getRowDimension(2)->setVisible(false);

        foreach ($columns as $index => $column) {
            $letter = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($letter . '1', $column['label'] . ($column['required'] ? ' *' : ''));
            $sheet->setCellValueExplicit($letter . '2', $column['key'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->getColumnDimension($letter)->setWidth($column['width']);

            $headerStyle = $sheet->getStyle($letter . '1');
            $headerStyle->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(
                $column['required'] ? 'FFE5533D' : 'FF526D82'
            );
            $headerStyle->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $headerStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFD8DEE8');
        }

        $lastColumn = Coordinate::stringFromColumnIndex(count($columns));
        $sheet->getStyle('A3:' . $lastColumn . self::LAST_DATA_ROW)
            ->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    }

    private function configureShipmentValidations(Worksheet $sheet, array $columns): void
    {
        $this->addListValidation($sheet, $this->columnIndex($columns, 'loading_type'), [
            'CITY_TO_CITY',
            'CITY_TO_CITY_MULTIPLE_LOCATIONS',
        ]);
        $this->addListValidation($sheet, $this->columnIndex($columns, 'is_draft'), ['YES', 'NO']);
        $this->addListValidation($sheet, $this->columnIndex($columns, 'is_rfq'), ['YES', 'NO']);

        $dateColumn = Coordinate::stringFromColumnIndex($this->columnIndex($columns, 'delivery_required_at'));
        $sheet->getStyle($dateColumn . self::FIRST_DATA_ROW . ':' . $dateColumn . self::LAST_DATA_ROW)
            ->getNumberFormat()->setFormatCode('yyyy-mm-dd');

        foreach (['loading_contact_phone', 'attachment_1_url', 'attachment_2_url', 'attachment_3_url', 'attachment_4_url'] as $key) {
            $column = Coordinate::stringFromColumnIndex($this->columnIndex($columns, $key));
            $sheet->getStyle($column . self::FIRST_DATA_ROW . ':' . $column . self::LAST_DATA_ROW)
                ->getNumberFormat()->setFormatCode('@');
        }
    }

    private function addListValidation(Worksheet $sheet, int $columnIndex, array $values): void
    {
        $column = Coordinate::stringFromColumnIndex($columnIndex);
        for ($row = self::FIRST_DATA_ROW; $row <= self::LAST_DATA_ROW; $row++) {
            $validation = $sheet->getCell($column . $row)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST)
                ->setErrorStyle(DataValidation::STYLE_STOP)
                ->setAllowBlank(false)
                ->setShowDropDown(true)
                ->setShowErrorMessage(true)
                ->setFormula1('"' . implode(',', $values) . '"');
        }
    }

    private function columnIndex(array $columns, string $key): int
    {
        foreach ($columns as $index => $column) {
            if ($column['key'] === $key) {
                return $index + 1;
            }
        }

        throw new \InvalidArgumentException('Unknown template column: ' . $key);
    }

    private function configureInstructionsSheet(Worksheet $sheet): void
    {
        $sheet->setRightToLeft($this->locale === 'ar');
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(105);
        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A1', __('contenttypes::strings.shipment_import.template_title'));
        $sheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF364A63');
        $sheet->getStyle('A1:B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->setCellValue('A3', '●');
        $sheet->setCellValue('B3', __('contenttypes::strings.shipment_import.required_legend'));
        $sheet->getStyle('A3')->getFont()->getColor()->setARGB('FFE5533D');
        $sheet->setCellValue('A4', '●');
        $sheet->setCellValue('B4', __('contenttypes::strings.shipment_import.optional_legend'));
        $sheet->getStyle('A4')->getFont()->getColor()->setARGB('FF526D82');

        $instructions = __('contenttypes::strings.shipment_import.instructions');
        foreach (array_values($instructions) as $index => $instruction) {
            $row = $index + 6;
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $instruction);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true)->getColor()->setARGB('FF364A63');
            $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getRowDimension($row)->setRowHeight(32);
        }
    }

    private function configureReferenceSheet(Worksheet $sheet): void
    {
        $sheet->setRightToLeft($this->locale === 'ar');
        $groups = [
            __('contenttypes::strings.shipment_import.references.cities') => City::where('country_id', 191)
                ->orderBy('name_' . $this->locale)
                ->get()
                ->map(function($item) {
                    return [$item->id, $item->{'name_' . $this->locale}];
                })->all(),
            __('contenttypes::strings.shipment_import.references.commodities') => $this->contentOptions('commodity'),
            __('contenttypes::strings.shipment_import.references.load_sizes') => $this->contentOptions('load-size'),
            __('contenttypes::strings.shipment_import.references.load_types') => $this->contentOptions('load-type'),
            __('contenttypes::strings.shipment_import.references.truck_types') => $this->contentOptions('truck-type'),
            __('contenttypes::strings.shipment_import.references.loading_types') => collect(config('contenttypes.loadingTypes'))
                ->reject(function($item) {
                    return $item['id'] === 'CITY_TO_CITY_ROUND_TRIP';
                })
                ->map(function($item) {
                    return [$item['id'], __($item['label'])];
                })->values()->all(),
            __('contenttypes::strings.shipment_import.references.yes_no') => [
                ['YES', __('contenttypes::strings.shipment_import.yes')],
                ['NO', __('contenttypes::strings.shipment_import.no')],
            ],
        ];

        $column = 1;
        foreach ($groups as $title => $rows) {
            $idColumn = Coordinate::stringFromColumnIndex($column);
            $nameColumn = Coordinate::stringFromColumnIndex($column + 1);
            $sheet->mergeCells($idColumn . '1:' . $nameColumn . '1');
            $sheet->setCellValue($idColumn . '1', $title);
            $sheet->setCellValue($idColumn . '2', __('contenttypes::strings.shipment_import.reference_id'));
            $sheet->setCellValue($nameColumn . '2', __('contenttypes::strings.shipment_import.reference_name'));
            $sheet->getColumnDimension($idColumn)->setWidth(20);
            $sheet->getColumnDimension($nameColumn)->setWidth(28);
            $sheet->getStyle($idColumn . '1:' . $nameColumn . '1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($idColumn . '1:' . $nameColumn . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF364A63');
            $sheet->getStyle($idColumn . '2:' . $nameColumn . '2')->getFont()->setBold(true);
            $sheet->getStyle($idColumn . '2:' . $nameColumn . '2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE9EEF5');

            foreach ($rows as $index => $row) {
                $sheet->setCellValueExplicit($idColumn . ($index + 3), (string) $row[0], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue($nameColumn . ($index + 3), $row[1]);
            }
            $column += 3;
        }

        $sheet->freezePane('A3');
    }

    private function contentOptions(string $type): array
    {
        return Content::where('base_type', 'CATEGORY')
            ->where('type', $type)
            ->where('sys_status', 'ACTIVE')
            ->with('translations')
            ->orderBy('ordering')
            ->get()
            ->map(function($item) {
                return [$item->id, $item->__('title', $this->locale, (string) $item->id)];
            })
            ->all();
    }
}
