<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

use DB;
use App\Models\User;

class TransporterContract extends Model 
{
    use SoftDeletes;

    protected $table = 'lgl_transporters_contracts';

    protected $fillable = [
        'company_user_id',
        'transporter_user_id',
        'is_rfq',
        'shippment_id',
        'order_start_date',
        'order_end_date',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function shippment()
    {
        return $this->belongsTo(Shippment::class, 'shippment_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function transporter()
    {
        return $this->belongsTo(User::class, 'transporter_user_id', 'id');
    }

    public function instances()
    {
        return $this->hasMany(TransporterContractInstance::class, 'contract_id', 'id');
    }

    public function instance()
    {
        return $this->hasOne(TransporterContractInstance::class, 'contract_id', 'id');
    }

    public function currentInstance()
    {
        return $this->hasOne(TransporterContractInstance::class, 'contract_id', 'id')->where('is_current', 'YES');
    }

    public function relatedFiles()
    {
        return $this->hasMany(UploadedFile::class, 'related_id', 'id')->where('related_type', 'contract');
    }

    public function TRANSPORTER_TAX_CERTIFICATE()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'contract')
        ->where('file_tag', 'TRANSPORTER_TAX_CERTIFICATE');
    }

    public function TRANSPORTER_REGISTAION_LICENCE()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'contract')
        ->where('file_tag', 'TRANSPORTER_REGISTAION_LICENCE');
    }

    public function TRANSPORTER_INSURANCE_CERTIFICATE()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'contract')
        ->where('file_tag', 'TRANSPORTER_INSURANCE_CERTIFICATE');
    }

    public function TRANSPORTER_TRANSPORTATION_LICENCE()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'contract')
        ->where('file_tag', 'TRANSPORTER_TRANSPORTATION_LICENCE');
    }

    public function scopeWithSelectMeta($query)
    {
        $query->addSelect([
            DB::raw('
                DATE_FORMAT(created_at, "%d %b %Y") AS new_created_at,
                DATE_FORMAT(updated_at, "%d %b %Y") AS new_updated_at
            '),
            DB::raw('
                DATE_FORMAT(order_start_date, "%d %b %Y") AS new_order_start_date,
                DATE_FORMAT(order_end_date, "%d %b %Y") AS new_order_end_date
            '),
            DB::raw('(CASE 
                WHEN sys_status = "ACTIVE"   THEN "'. __('ui::inputs.status.options.ACTIVE')   .'"
                WHEN sys_status = "DISABLED" THEN "'. __('ui::inputs.status.options.DISABLED') .'"
                ELSE "-----"
            END) AS new_status'),
            DB::raw('(CASE 
                WHEN sys_status = "ACTIVE"   THEN "success"
                WHEN sys_status = "DISABLED" THEN "danger"
                ELSE "danger"
            END) AS new_status_color'),
            DB::raw('(CASE 
                WHEN (DATEDIFF(order_start_date, NOW()) <= 0 AND DATEDIFF(order_end_date, NOW()) >= 0) THEN "ACTIVE"
                WHEN (DATEDIFF(order_end_date, NOW()) < 0)                                             THEN "EXPIRED"
                ELSE "EXPIRED"
            END) AS status_by_date'),
            DB::raw('(CASE 
                WHEN (DATEDIFF(order_start_date, NOW()) <= 0 AND DATEDIFF(order_end_date, NOW()) >= 0) THEN "'. __('contenttypes::inputs.contract_status.options.ACTIVE') .'"
                WHEN (DATEDIFF(order_end_date, NOW()) <  0)                                            THEN "'. __('contenttypes::inputs.contract_status.options.EXPIRED') .'"
                ELSE "'. __('contenttypes::inputs.contract_status.options.EXPIRED') .'"
            END) AS new_status_by_date'),
            DB::raw('(CASE 
                WHEN (DATEDIFF(order_start_date, NOW()) <= 0 AND DATEDIFF(order_end_date, NOW()) >= 0) THEN "success"
                WHEN (DATEDIFF(order_end_date, NOW()) <  0)                                            THEN "danger"
                ELSE "danger"
            END) AS new_status_by_date_color'),
            DB::raw('
                DATEDIFF(order_end_date, order_start_date) AS contract_total_days,
                DATEDIFF(order_start_date, NOW()) AS contract_days_since_start,
                DATEDIFF(order_end_date, NOW()) AS contract_days_until_end
            ')
        ]);

        return $query;
    }

    public function scopeWithClient($query)
    {
        $query->with([
            'client' => function($rel) {
                $rel->with(['company' => function($rel) {
                    $rel->withTrashed();
                    $rel->with(['address' => function($rel) {
                        $rel->withTrashed();
                        $rel->with(['city' => function($rel) {
                            $rel->withTrashed();
                            $rel->with(['country' => function($rel) {
                                $rel->withTrashed();
                            }]);
                        }]);
                    }]);
                }]);
            },
        ]);

        return $query;
    }

    public function scopeWithTransporter($query)
    {
        $query->with([
            'transporter' => function($rel) {
                $rel->with(['company' => function($rel) {
                    $rel->withTrashed();
                    $rel->with(['address' => function($rel) {
                        $rel->withTrashed();
                        $rel->with(['city' => function($rel) {
                            $rel->withTrashed();
                            $rel->with(['country' => function($rel) {
                                $rel->withTrashed();
                            }]);
                        }]);
                    }]);
                }]);
            },
        ]);

        return $query;
    }

    public function scopeWithInstance($query, $instanceId)
    {
        $query->with([
            'instance' => function($rel) use ($instanceId) {
                $rel->where('id', $instanceId);
            },
        ]);
        
        return $query;
    }

    public function scopeWithCurrentInstance($query)
    {
        $query->with([
            'currentInstance' => function($rel) {},
        ]);
        
        return $query;
    }

    public function scopeWithInstanceDetails($query)
    {
        $query->with([
            'currentInstance' => function($rel) {
                $rel->select('*')->withFullDetails();
            },
        ]);
        
        return $query;
    }

    public function scopeWithFiles($query)
    {
        $query->with([
            'TRANSPORTER_TAX_CERTIFICATE' => function($rel) {},
            'TRANSPORTER_REGISTAION_LICENCE' => function($rel) {},
            'TRANSPORTER_INSURANCE_CERTIFICATE' => function($rel) {},
            'TRANSPORTER_TRANSPORTATION_LICENCE' => function($rel) {},
        ]);
        
        return $query;
    }

    public function scopeOnlyActiveByStartDate($query)
    {
        $query->where('order_start_date', '<=', now()->format('Y-m-d'));
        $query->where('order_end_date'  , '>=', now()->format('Y-m-d'));
        $query->where('sys_status'      , 'ACTIVE');
        // $query->where('is_rfq'          , 'NO');
        
        return $query;
    }
}
