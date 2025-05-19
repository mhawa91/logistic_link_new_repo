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
use Modules\ContentManager\Entities\Content;

class TransporterContractInstance extends Model 
{
    use SoftDeletes;

    protected $table = 'lgl_transporters_contracts_instances';

    protected $fillable = [
        'contract_id',
        'company_user_id',
        'transporter_user_id',
        'payment_term_id',
        'insurance_value_limit',
        'is_rfq',
        'shippment_id',
        'order_start_date',
        'order_end_date',
        'is_current',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function shippment()
    {
        return $this->belongsTo(Shippment::class, 'shippment_id', 'id');
    }

    public function contract()
    {
        return $this->belongsTo(TransporterContract::class, 'contract_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function transporter()
    {
        return $this->belongsTo(User::class, 'transporter_user_id', 'id');
    }

    public function paymentTerm()
    {
        return $this->belongsTo(Content::class, 'payment_term_id', 'id');
    }

    public function commodities()
    {
        return $this->hasMany(TransporterContractInstanceCommodity::class, 'contract_instance_id', 'id');
    }

    public function pricing()
    {
        return $this->hasMany(TransporterContractInstancePrice::class, 'contract_instance_id', 'id');
    }

    public function TERMS_AND_CONDITIONS()
    {
        return $this->hasOne(TransporterContractInstanceMetadata::class, 'contract_instance_id', 'id')->where('type', 'TERMS_AND_CONDITIONS');
    }

    public function INSURANCE_DETAILS()
    {
        return $this->hasOne(TransporterContractInstanceMetadata::class, 'contract_instance_id', 'id')->where('type', 'INSURANCE_DETAILS');
    }

    public function PENALITIES()
    {
        return $this->hasOne(TransporterContractInstanceMetadata::class, 'contract_instance_id', 'id')->where('type', 'PENALITIES');
    }

    public function scopeWithFullDetails($query)
    {
        $query->with([
            'paymentTerm' => function($rel) {
                $rel->withTrashed();
                $rel->with([
                    'translations' => function($rel) {},
                ]);
            },
            'commodities' => function($rel) {
                $rel->with([
                    'content' => function($rel) {
                        $rel->withTrashed();
                        $rel->with([
                            'translations' => function($rel) {},
                        ]);
                    }
                ]);
            },
            'pricing' => function($rel) {
                $rel->withFullDetails();
            },
            'TERMS_AND_CONDITIONS' => function($rel) {},
            'INSURANCE_DETAILS' => function($rel) {},
            'PENALITIES' => function($rel) {},
        ]);

        return $query;
    }

    public function scopeWithSelectMeta($query)
    {
        $query->addSelect([
            DB::raw('
                DATE_FORMAT(created_at, "%Y-%m-%d") AS new_created_at,
                DATE_FORMAT(updated_at, "%Y-%m-%d") AS new_updated_at
            '),
            DB::raw('
                DATE_FORMAT(order_start_date, "%Y-%m-%d") AS new_order_start_date,
                DATE_FORMAT(order_end_date, "%Y-%m-%d") AS new_order_end_date
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

    public function scopeOnlyActiveByStartDate($query)
    {
        $query->where('lgl_transporters_contracts_instances.order_start_date', '<=', now()->format('Y-m-d'));
        $query->where('lgl_transporters_contracts_instances.order_end_date'  , '>=', now()->format('Y-m-d'));
        $query->where('lgl_transporters_contracts_instances.sys_status'      , 'ACTIVE');
        // $query->where('is_rfq'          , 'NO');
        
        return $query;
    }
}
