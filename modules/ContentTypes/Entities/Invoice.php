<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

use DB;
use Modules\SystemCore\Entities;
use App\Models\User;

class Invoice extends Model 
{
    use SoftDeletes;

    protected $table = 'lgl_invoices';

    protected $fillable = [
        'invoice_number',
        'company_user_id',
        'company_id',
        'transportar_user_id',
        'transportar_id',
        'invoice_file',
        'note',
        'invoiced_at',
        'due_at',
        'paid_at',
        'sub_total',
        'discounts_total',
        'total',
        'sys_status', // PENDING | PENDING_SUPPLIER_REVIEW | PAID
        'created_by_user_id',
        'updated_by_user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function ATTACHMENT()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'invoice')
        ->where('file_tag', 'invoice_file');
    }

    public function INVOICE_FILE()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'invoice')
        ->where('file_tag', 'transportar_invoice_file');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function clientUser()
    {
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function clientCompany()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_id', 'user_id');
    }

    public function transportarUser()
    {
        return $this->belongsTo(User::class, 'transportar_user_id', 'id');
    }

    public function transportarCompany()
    {
        return $this->belongsTo(CompanyProfile::class, 'transportar_id', 'user_id');
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

        $query->join('users AS client_user', 'client_user.id', '=', 'lgl_invoices.company_user_id');
        $query->join('lgl_company_profile AS client', 'client.user_id', '=', 'lgl_invoices.company_user_id');

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

        $query->leftJoin('users AS transportar_user', 'transportar_user.id', '=', 'lgl_invoices.transportar_user_id');
        $query->leftJoin('lgl_company_profile AS transportar', 'transportar.user_id', '=', 'lgl_invoices.transportar_user_id');

        return $query;
    }

    public function scopeWithSelectMeta($query)
    {
        $query->addSelect([
            DB::raw('
                DATE_FORMAT(lgl_invoices.created_at, "%b %d, %Y | %h:%i %p")  AS new_created_at,
                DATE_FORMAT(lgl_invoices.updated_at, "%b %d, %Y | %h:%i %p")  AS new_updated_at,
                DATE_FORMAT(lgl_invoices.invoiced_at, "%b %d, %Y") AS new_invoiced_at,
                DATE_FORMAT(lgl_invoices.due_at, "%b %d, %Y")      AS new_due_at,
                DATE_FORMAT(lgl_invoices.paid_at, "%b %d, %Y")     AS new_paid_at
            '),
            DB::raw('(CASE 
                WHEN lgl_invoices.sys_status = "PENDING"                   THEN "'. __('contenttypes::inputs.invoice_status.options.PENDING')   .'"
                WHEN lgl_invoices.sys_status = "PENDING_SUPPLIER_REVIEW"   THEN "'. __('contenttypes::inputs.invoice_status.options.PENDING_SUPPLIER_REVIEW')   .'"
                WHEN lgl_invoices.sys_status = "PAID"                      THEN "'. __('contenttypes::inputs.invoice_status.options.PAID')   .'"
                ELSE "-----"
            END) AS new_status'),
            DB::raw('(CASE 
                WHEN lgl_invoices.sys_status = "PENDING"                   THEN "danger"
                WHEN lgl_invoices.sys_status = "PENDING_SUPPLIER_REVIEW"   THEN "warning"
                WHEN lgl_invoices.sys_status = "PAID"                      THEN "success"
                ELSE "danger"
            END) AS new_status_color'),
        ]);

        return $query;
    }
}
