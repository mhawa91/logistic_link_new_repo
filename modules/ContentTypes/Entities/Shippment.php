<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

use App\Models\User;
use Modules\ContentManager\Entities\Content;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\City;
use Modules\SystemCore\Entities\Country;
use DB;

class Shippment extends Model 
{
    use SoftDeletes;

    protected $table = 'lgl_shippments';

    protected $fillable = [
        'company_user_id',
        'transporter_user_id',
        'driver_id',
        'car_plate',
        'from_country_id',
        'from_city_id',
        'to_country_id',
        'to_city_id',
        'round_trip',
        'is_rfq',
        'total_weight',
        'decalred_value',
        'load_size_id',
        'load_type_id',
        'number_of_pallets',
        'number_of_trucks',
        'truck_type_id',
        'temperature',
        'equipment_length',
        'equipment_width',
        'delivery_required_at',
        'public_notes',
        'private_notes',
        'type',
        'contract_id',
        'contract_instance_id',
        'price',
        'pre_additional_location_price',
        'total_price',
        'updated_price',
        'updated_pre_additional_location_price',
        'updated_total_price',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
        'invoice_number',
        'invoice_date',
        'invoice_amount',
        'invoice_file'
    ];

    public function loadingLocation()
    {
        return $this->hasOne(Address::class, 'related_id', 'id')
        ->where('related_type', 'SHIPPMENT')
        ->where('address_tag', 'SHIPPMENT_LOADING_POINT');
    }

    public function offloadingLocations()
    {
        return $this->hasMany(Address::class, 'related_id', 'id')
        ->where('related_type', 'SHIPPMENT')
        ->where('address_tag', 'SHIPPMENT_OFFLOADING_POINT');
    }

    public function commodities()
    {
        return $this->hasMany(ShippmentCommodity::class, 'shippment_id', 'id');
    }

    public function loadSize()
    {
        return $this->belongsTo(Content::class, 'load_size_id', 'id')->where('base_type', 'CATEGORY')->where('type', 'load-size');
    }

    public function loadType()
    {
        return $this->belongsTo(Content::class, 'load_type_id', 'id')->where('base_type', 'CATEGORY')->where('type', 'load-type');
    }

    public function truckType()
    {
        return $this->belongsTo(Content::class, 'truck_type_id', 'id')->where('base_type', 'CATEGORY')->where('type', 'truck-type');
    }

    
    public function allAddressess()
    {
        return $this->hasOne(Address::class, 'related_id', 'id')
        ->where('related_type', 'SHIPPMENT');
    }

    public function pickupAddress()
    {
        return $this->hasOne(Address::class, 'related_id', 'id')
        ->where('related_type', 'SHIPPMENT')
        ->where('address_tag' , 'SHIPPMENT_LOADING_POINT');
    }

    public function deliveryAddresses()
    {
        return $this->hasMany(Address::class, 'related_id', 'id')
        ->where('related_type', 'SHIPPMENT')
        ->where('address_tag' , 'SHIPPMENT_OFFLOADING_POINT');
    }

    public function companyUser()
    {
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function transporterUser()
    {
        return $this->belongsTo(User::class, 'transporter_user_id', 'id');
    }

    public function fromCountry()
    {
        return $this->belongsTo(Country::class, 'from_country_id', 'id');
    }

    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from_city_id', 'id');
    }

    public function toCountry()
    {
        return $this->belongsTo(Country::class, 'to_country_id', 'id');
    }

    public function toCity()
    {
        return $this->belongsTo(City::class, 'to_city_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }

    public function matchedTransportars()
    {
        return $this->hasMany(ShippmentTransporter::class, 'shippment_id', 'id');
    }

    public function currentTransportar()
    {
        return $this->hasOne(ShippmentTransporter::class, 'shippment_id', 'id')
        ->whereColumn('lgl_shippments_matched_transporters.transporter_user_id', 'lgl_shippments.transporter_user_id');
    }

    public function rfqs()
    {
        return $this->hasMany(ShippmentRfqTransporter::class, 'shippment_id', 'id');
    }

    public function ATTACHMENT_1()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'shippment')
        ->where('file_tag', 'ATTACHMENT_1');
    }

    public function ATTACHMENT_2()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'shippment')
        ->where('file_tag', 'ATTACHMENT_2');
    }

    public function ATTACHMENT_3()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'shippment')
        ->where('file_tag', 'ATTACHMENT_3');
    }

    public function ATTACHMENT_4()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'shippment')
        ->where('file_tag', 'ATTACHMENT_4');
    }

    public function WAYBILL()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'shippment')
        ->where('file_tag', 'WAYBILL');
    }

    public function scopeWithSelectMeta($query)
    {
        $query->addSelect([
            DB::raw('
                DATE_FORMAT(lgl_shippments.created_at, "%b %d, %Y | %h:%i %p") AS new_created_at,
                DATE_FORMAT(lgl_shippments.updated_at, "%b %d, %Y | %h:%i %p") AS new_updated_at
            '),
            DB::raw('(CASE 
                WHEN lgl_shippments.sys_status = "DRAFT"                                     THEN "'. __('contenttypes::inputs.shippment_status.options.DRAFT')   .'"
                WHEN lgl_shippments.sys_status = "MATCHING"                                  THEN "'. __('contenttypes::inputs.shippment_status.options.MATCHING')   .'"
                WHEN lgl_shippments.sys_status = "NO_MATCHING_CONTRACTS"                     THEN "'. __('contenttypes::inputs.shippment_status.options.NO_MATCHING_CONTRACTS')   .'"
                WHEN lgl_shippments.sys_status = "PENDING_TRANSPORTER_APPROVAL"              THEN "'. __('contenttypes::inputs.shippment_status.options.PENDING_TRANSPORTER_APPROVAL') .'"
                WHEN lgl_shippments.sys_status = "TRANSPORTER_APPROVED_WITH_PRICE_CHANGE"    THEN "'. __('contenttypes::inputs.shippment_status.options.TRANSPORTER_APPROVED_WITH_PRICE_CHANGE') .'"
                WHEN lgl_shippments.sys_status = "CANCELED_DUE_DELAYED_TRANSPORTER_APPROVAL" THEN "'. __('contenttypes::inputs.shippment_status.options.CANCELED_DUE_DELAYED_TRANSPORTER_APPROVAL') .'"
                WHEN lgl_shippments.sys_status = "PENDING_PICKUP"                            THEN "'. __('contenttypes::inputs.shippment_status.options.PENDING_PICKUP') .'"
                WHEN lgl_shippments.sys_status = "PENDING_DELIVERY"                          THEN "'. __('contenttypes::inputs.shippment_status.options.PENDING_DELIVERY') .'"
                WHEN lgl_shippments.sys_status = "DELIVERED_PENDING_PAYMENT"                 THEN "'. __('contenttypes::inputs.shippment_status.options.DELIVERED_PENDING_PAYMENT') .'"
                WHEN lgl_shippments.sys_status = "COMPLETED"                                 THEN "'. __('contenttypes::inputs.shippment_status.options.COMPLETED')   .'"
                WHEN lgl_shippments.sys_status = "CANCELLED"                                 THEN "'. __('contenttypes::inputs.shippment_status.options.CANCELLED')   .'"
                WHEN lgl_shippments.sys_status = "RFQ_PENDING_TRANSPORTER_SELECTION"         THEN "'. __('contenttypes::inputs.shippment_status.options.RFQ_PENDING_TRANSPORTER_SELECTION')   .'"
                WHEN lgl_shippments.sys_status = "RFQ_PENDING_TRANSPORTER_OFFER"             THEN "'. __('contenttypes::inputs.shippment_status.options.RFQ_PENDING_TRANSPORTER_OFFER')   .'"
                ELSE "-----"
            END) AS new_status'),
            DB::raw('(CASE 
                WHEN lgl_shippments.sys_status = "DRAFT"                                     THEN "muted"
                WHEN lgl_shippments.sys_status = "MATCHING"                                  THEN "info"
                WHEN lgl_shippments.sys_status = "NO_MATCHING_CONTRACTS"                     THEN "danger"
                WHEN lgl_shippments.sys_status = "PENDING_TRANSPORTER_APPROVAL"              THEN "warning"
                WHEN lgl_shippments.sys_status = "TRANSPORTER_APPROVED_WITH_PRICE_CHANGE"    THEN "warning"
                WHEN lgl_shippments.sys_status = "CANCELED_DUE_DELAYED_TRANSPORTER_APPROVAL" THEN "danger"
                WHEN lgl_shippments.sys_status = "PENDING_PICKUP"                            THEN "warning"
                WHEN lgl_shippments.sys_status = "PENDING_DELIVERY"                          THEN "warning"
                WHEN lgl_shippments.sys_status = "DELIVERED_PENDING_PAYMENT"                 THEN "warning"
                WHEN lgl_shippments.sys_status = "COMPLETED"                                 THEN "success"
                WHEN lgl_shippments.sys_status = "CANCELLED"                                 THEN "danger"
                WHEN lgl_shippments.sys_status = "RFQ_PENDING_TRANSPORTER_SELECTION"         THEN "warning"
                WHEN lgl_shippments.sys_status = "RFQ_PENDING_TRANSPORTER_OFFER"             THEN "warning"
                ELSE "danger"
            END) AS new_status_color'),
            DB::raw('(CASE 
                WHEN lgl_shippments.is_rfq = "YES" THEN "'. __('contenttypes::inputs.is_rfq.options.YES')   .'"
                WHEN lgl_shippments.is_rfq = "NO"  THEN "'. __('contenttypes::inputs.is_rfq.options.NO')   .'"
                ELSE "-----"
            END) AS new_is_rfq'),
            DB::raw('(CASE 
                WHEN lgl_shippments.is_rfq = "YES" THEN "danger"
                WHEN lgl_shippments.is_rfq = "NO"  THEN "primary"
                ELSE "danger"
            END) AS new_is_rfq_color'),
        ]);

        return $query;
    }

    public function scopeWithClientSelect($query)
    {
        $query->addSelect([
            DB::raw('
                client_user.name AS client_user_name,
                client_user.email AS client_user_email,
                client.company_name AS client_company_name,
                client.company_email AS client_company_email,
                (CASE 
                    WHEN client.company_logo IS NULL THEN "'. app('GraphManager')->defaultSrc('400x400', 'company') .'"
                    ELSE CONCAT("'. app('GraphManager')->src('400x400', 'company', '-SQL-') .'", client.company_logo)
                END) AS client_company_logo,
                client.company_phone_number_ext AS client_company_phone_number_ext,
                client.company_phone_number AS client_company_phone_number,
                client.registration_number AS client_registration_number,
                client.tax_identification_number AS client_tax_identification_number,
                client.sys_status AS client_sys_status
            '),
        ]);

        $query->join('users AS client_user', 'client_user.id', '=', 'lgl_shippments.company_user_id');
        $query->join('lgl_company_profile AS client', 'client.user_id', '=', 'lgl_shippments.company_user_id');

        return $query;
    }

    public function scopeWithTransportarSelect($query)
    {
        $query->addSelect([
            DB::raw('
                transportar_user.name AS transportar_user_name,
                transportar_user.email AS transportar_user_email,
                transportar.company_name AS transportar_company_name,
                transportar.company_email AS transportar_company_email,
                (CASE 
                    WHEN transportar.company_logo IS NULL THEN "'. app('GraphManager')->defaultSrc('400x400', 'company') .'"
                    ELSE CONCAT("'. app('GraphManager')->src('400x400', 'company', '-SQL-') .'", transportar.company_logo)
                END) AS transportar_company_logo,
                transportar.company_phone_number_ext AS transportar_company_phone_number_ext,
                transportar.company_phone_number AS transportar_company_phone_number,
                transportar.registration_number AS transportar_registration_number,
                transportar.tax_identification_number AS transportar_tax_identification_number,
                transportar.sys_status AS transportar_sys_status
            '),
        ]);

        $query->leftJoin('users AS transportar_user', 'transportar_user.id', '=', 'lgl_shippments.transporter_user_id');
        $query->leftJoin('lgl_company_profile AS transportar', 'transportar.user_id', '=', 'lgl_shippments.transporter_user_id');

        return $query;
    }

    public function scopeWithCountryAndCitySelect($query)
    {
        $locale = in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'en';

        $query->addSelect([
            DB::raw('
                from_country.sortname AS from_country_code,
                from_country.name_'. ($locale) .' AS from_country_name,
                from_city.name_'. ($locale) .' AS from_city_name,
                to_country.sortname AS to_country_code,
                to_country.name_'. ($locale) .' AS to_country_name,
                to_city.name_'. ($locale) .' AS to_city_name
            '),
        ]);

        $query->leftJoin('countries AS from_country', 'from_country.id', '=', 'lgl_shippments.from_country_id');
        $query->leftJoin('cities AS from_city', 'from_city.id', '=', 'lgl_shippments.from_city_id');

        $query->leftJoin('countries AS to_country', 'to_country.id', '=', 'lgl_shippments.to_country_id');
        $query->leftJoin('cities AS to_city', 'to_city.id', '=', 'lgl_shippments.to_city_id');

        return $query;
    }

    public function scopeWithLocationsCountSelect($query)
    {
        $locale = in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'en';

        $query->addSelect([
            DB::raw('(
                SELECT COUNT(*) FROM lgl_addressess AS address
                WHERE address.related_type = "SHIPPMENT"
                AND address.related_id = lgl_shippments.id
                AND address.address_tag = "SHIPPMENT_OFFLOADING_POINT"
            ) AS offloading_locations_count'),
        ]);
        return $query;
    }

    public function scopeWithDriverSelect($query)
    {
        $query->addSelect([
            DB::raw('
                driver.name AS driver_name,
                driver.id_number AS driver_id_number,
                driver.sys_status AS driver_sys_status
            '),
        ]);

        $query->leftJoin('lgl_drivers AS driver', 'driver.id', '=', 'lgl_shippments.driver_id');

        return $query;
    }

    public function scopeWithWaybillSelect($query)
    {
        $query->addSelect([
            DB::raw('(
                SELECT (CASE 
                    WHEN waybill.file_path IS NOT NULL THEN CONCAT("'. app('GraphManager')->src('original', 'files', '-SQL-') .'", waybill.file_path)
                    ELSE NULL
                END) FROM lgl_related_files AS waybill
                WHERE waybill.related_id = lgl_shippments.id
                AND waybill.related_type = "shippment"
                AND waybill.file_tag = "WAYBILL"
            ) AS waybill_path'),
        ]);

        return $query;
    }

    public function scopeWithCurrentTransportar($query)
    {
        $query->with([
            'currentTransportar' => function($query) {},
        ]);
    }

    public function scopeWithDriver($query)
    {
        $query->with([
            'driver' => function($query) {},
        ]);
    }

    public function scopeWithRfqSelectMeta($query)
    {
        $query->addSelect([
            DB::raw('
                DATE_FORMAT(lgl_shippments_rfqs.requested_at, "%b %d, %Y | %h:%i %p") AS new_requested_at,
                DATE_FORMAT(lgl_shippments_rfqs.responded_at, "%b %d, %Y | %h:%i %p") AS new_responded_at
            '),
            DB::raw('
                lgl_shippments_rfqs.total_price AS rfq_total_price
            '),
            DB::raw('(CASE 
                WHEN lgl_shippments_rfqs.status = "PENDING"             THEN "'. __('contenttypes::inputs.rfq_status.options.PENDING')   .'"
                WHEN lgl_shippments_rfqs.status = "PENDING_RESPONSE"    THEN "'. __('contenttypes::inputs.rfq_status.options.PENDING_RESPONSE')   .'"
                WHEN lgl_shippments_rfqs.status = "RESPONSE_DELAYED"    THEN "'. __('contenttypes::inputs.rfq_status.options.RESPONSE_DELAYED')   .'"
                WHEN lgl_shippments_rfqs.status = "PENDING_ACCEPTANCE"  THEN "'. __('contenttypes::inputs.rfq_status.options.PENDING_ACCEPTANCE')   .'"
                WHEN lgl_shippments_rfqs.status = "ACCEPTED"            THEN "'. __('contenttypes::inputs.rfq_status.options.ACCEPTED')   .'"
                WHEN lgl_shippments_rfqs.status = "REJECTED"            THEN "'. __('contenttypes::inputs.rfq_status.options.REJECTED')   .'"
                ELSE "-----"
            END) AS new_status'),
            DB::raw('(CASE 
                WHEN lgl_shippments_rfqs.status = "PENDING"   THEN "warning"
                WHEN lgl_shippments_rfqs.status = "PENDING_RESPONSE"   THEN "warning"
                WHEN lgl_shippments_rfqs.status = "RESPONSE_DELAYED"   THEN "danger"
                WHEN lgl_shippments_rfqs.status = "PENDING_ACCEPTANCE" THEN "warning"
                WHEN lgl_shippments_rfqs.status = "ACCEPTED"           THEN "success"
                WHEN lgl_shippments_rfqs.status = "REJECTED"           THEN "danger"
                ELSE "danger"
            END) AS new_status_color'),
            DB::raw('(CASE 
                WHEN lgl_shippments.is_rfq = "YES" THEN "'. __('contenttypes::inputs.is_rfq.options.YES')   .'"
                WHEN lgl_shippments.is_rfq = "NO"  THEN "'. __('contenttypes::inputs.is_rfq.options.NO')   .'"
                ELSE "-----"
            END) AS new_is_rfq'),
            DB::raw('(CASE 
                WHEN lgl_shippments.is_rfq = "YES" THEN "danger"
                WHEN lgl_shippments.is_rfq = "NO"  THEN "primary"
                ELSE "danger"
            END) AS new_is_rfq_color'),
        ]);

        $query
        ->join('lgl_shippments_rfq_transporters AS lgl_shippments_rfqs', 'lgl_shippments_rfqs.shippment_id', '=', 'lgl_shippments.id')
        ->groupBy('lgl_shippments_rfqs.id');

        return $query;
    }

    public function scopeWithRfqTransportarSelect($query)
    {
        $query->addSelect([
            DB::raw('
                transportar_user.name AS transportar_user_name,
                transportar_user.email AS transportar_user_email,
                transportar.company_name AS transportar_company_name,
                transportar.company_email AS transportar_company_email,
                (CASE 
                    WHEN transportar.company_logo IS NULL THEN "'. app('GraphManager')->defaultSrc('400x400', 'company') .'"
                    ELSE CONCAT("'. app('GraphManager')->src('400x400', 'company', '-SQL-') .'", transportar.company_logo)
                END) AS transportar_company_logo,
                transportar.company_phone_number_ext AS transportar_company_phone_number_ext,
                transportar.company_phone_number AS transportar_company_phone_number,
                transportar.registration_number AS transportar_registration_number,
                transportar.tax_identification_number AS transportar_tax_identification_number,
                transportar.sys_status AS transportar_sys_status
            '),
        ]);

        $query->leftJoin('users AS transportar_user', 'transportar_user.id', '=', 'lgl_shippments_rfqs.transporter_user_id');
        $query->leftJoin('lgl_company_profile AS transportar', 'transportar.user_id', '=', 'lgl_shippments_rfqs.transporter_user_id');

        return $query;
    }

    public function scopeWithTransportarFinance($query)
    {
        $query->addSelect([
            DB::raw('SUM((
                SELECT lgl_shippments.total_price
                FROM lgl_shippments AS sub_lgl_shippments
                WHERE sub_lgl_shippments.id = lgl_shippments.id
                AND sub_lgl_shippments.sys_status IN ("PENDING_PICKUP", "PENDING_DELIVERY", "PENDING_DELIVERY")
            )) AS open_amount_total'),
            DB::raw('SUM((
                SELECT lgl_shippments.total_price
                FROM lgl_shippments AS sub_lgl_shippments
                WHERE sub_lgl_shippments.id = lgl_shippments.id
                AND sub_lgl_shippments.sys_status IN ("DELIVERED_PENDING_PAYMENT")
            )) AS due_amount_total'),
            DB::raw('COUNT((
                SELECT lgl_shippments.id
                FROM lgl_shippments AS sub_lgl_shippments
                WHERE sub_lgl_shippments.id = lgl_shippments.id
                AND sub_lgl_shippments.sys_status IN ("PENDING_PICKUP", "PENDING_DELIVERY", "PENDING_DELIVERY", "DELIVERED_PENDING_PAYMENT")
            )) AS shippments_count'),
        ]);

        return $query;
    }
}
