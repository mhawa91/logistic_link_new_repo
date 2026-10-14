<?php

namespace Modules\ContentTypes\Imports;

class ShippmentImportDefinition
{
    public const SCHEMA_VERSION = '1';
    public const FIRST_DATA_ROW = 3;
    public const LAST_DATA_ROW = 502;
    public const EDITABLE_STATUSES = ['DRAFT', 'NO_MATCHING_CONTRACTS', 'RFQ_PENDING_TRANSPORTER_SELECTION'];

    public static function shipmentColumns(): array
    {
        $loading = __('contenttypes::strings.shippments.loading_address');

        return [
            self::column('import_key', __('contenttypes::strings.shipment_import.columns.import_key'), true, 18),
            self::column('id', __('contenttypes::strings.shipment_import.columns.shipment_id'), false, 12),
            self::column('from_city_id', __('contenttypes::inputs.from_city_id.label'), true, 18),
            self::column('to_city_id', __('contenttypes::inputs.to_city_id.label'), true, 18),
            self::column('loading_type', __('contenttypes::inputs.loading_type.label'), true, 30),
            self::column('commodity_ids', __('contenttypes::strings.shipment_import.columns.commodity_ids'), true, 22),
            self::column('load_size_id', __('contenttypes::inputs.load_size_id.label'), true, 18),
            self::column('load_type_id', __('contenttypes::inputs.load_type_id.label'), true, 18),
            self::column('truck_type_id', __('contenttypes::inputs.truck_type_id.label'), true, 18),
            self::column('total_weight', __('contenttypes::inputs.weight.label'), true, 16),
            self::column('declared_order_value', __('contenttypes::inputs.declared_order_value.label'), true, 20),
            self::column('number_of_pallets', __('contenttypes::inputs.number_of_pallets.label'), true, 17),
            self::column('number_of_trucks', __('contenttypes::inputs.number_of_trucks.label'), true, 17),
            self::column('temperature', __('contenttypes::inputs.temperature.label'), true, 18),
            self::column('equipment_length', __('contenttypes::inputs.equipment_length.label'), true, 18),
            self::column('equipment_width', __('contenttypes::inputs.equipment_width.label'), true, 18),
            self::column('delivery_required_at', __('contenttypes::inputs.delivery_required_at.label'), true, 20),
            self::column('is_draft', __('contenttypes::strings.shipment_import.columns.is_draft'), true, 14),
            self::column('is_rfq', __('contenttypes::strings.shipment_import.columns.is_rfq'), true, 14),
            self::column('public_notes', __('contenttypes::inputs.public_notes.label'), false, 28),
            self::column('private_notes', __('contenttypes::inputs.private_notes.label'), false, 28),
            self::column('loading_company_name', $loading . ' - ' . __('contenttypes::inputs.address_name.label'), true, 24),
            self::column('loading_contact_name', $loading . ' - ' . __('contenttypes::inputs.address_primary_contact.label'), true, 24),
            self::column('loading_contact_email', $loading . ' - ' . __('contenttypes::inputs.address_email.label'), true, 26),
            self::column('loading_contact_phone', $loading . ' - ' . __('contenttypes::inputs.address_phone_number.label'), true, 22),
            self::column('loading_lat', $loading . ' - ' . __('contenttypes::inputs.lat.label'), true, 18),
            self::column('loading_lng', $loading . ' - ' . __('contenttypes::inputs.lng.label'), true, 18),
            self::column('loading_full_address', $loading . ' - ' . __('contenttypes::inputs.full_address.label'), true, 34),
            self::column('loading_public_notes', $loading . ' - ' . __('contenttypes::inputs.public_notes.label'), false, 28),
            self::column('loading_private_notes', $loading . ' - ' . __('contenttypes::inputs.private_notes.label'), false, 28),
            self::column('attachment_1_url', __('contenttypes::strings.shipment_import.columns.attachment_1_url'), false, 32),
            self::column('attachment_2_url', __('contenttypes::strings.shipment_import.columns.attachment_2_url'), false, 32),
            self::column('attachment_3_url', __('contenttypes::strings.shipment_import.columns.attachment_3_url'), false, 32),
            self::column('attachment_4_url', __('contenttypes::strings.shipment_import.columns.attachment_4_url'), false, 32),
        ];
    }

    public static function deliveryAddressColumns(): array
    {
        return [
            self::column('shipment_import_key', __('contenttypes::strings.shipment_import.columns.shipment_import_key'), true, 20),
            self::column('shipment_id', __('contenttypes::strings.shipment_import.columns.shipment_id'), false, 14),
            self::column('address_id', __('contenttypes::strings.shipment_import.columns.address_id'), false, 14),
            self::column('sequence', __('contenttypes::strings.shipment_import.columns.sequence'), true, 12),
            self::column('company_name', __('contenttypes::inputs.address_name.label'), true, 24),
            self::column('contact_name', __('contenttypes::inputs.address_primary_contact.label'), true, 24),
            self::column('contact_email', __('contenttypes::inputs.address_email.label'), true, 26),
            self::column('contact_phone', __('contenttypes::inputs.address_phone_number.label'), true, 22),
            self::column('lat', __('contenttypes::inputs.lat.label'), true, 18),
            self::column('lng', __('contenttypes::inputs.lng.label'), true, 18),
            self::column('full_address', __('contenttypes::inputs.full_address.label'), true, 34),
            self::column('round_trip', __('contenttypes::inputs.round_trip.label'), true, 15),
            self::column('public_notes', __('contenttypes::inputs.public_notes.label'), false, 28),
            self::column('private_notes', __('contenttypes::inputs.private_notes.label'), false, 28),
        ];
    }

    public static function keys(array $columns): array
    {
        return array_column($columns, 'key');
    }

    public static function attributes(array $columns): array
    {
        return collect($columns)->mapWithKeys(function ($column) {
            return [$column['key'] => $column['label']];
        })->all();
    }

    private static function column(string $key, string $label, bool $required, int $width): array
    {
        return compact('key', 'label', 'required', 'width');
    }
}
