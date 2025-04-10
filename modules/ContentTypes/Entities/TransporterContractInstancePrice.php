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
use Modules\SystemCore\Entities\Country;
use Modules\SystemCore\Entities\City;

class TransporterContractInstancePrice extends Model 
{
    public $timestamps = false;
    
    protected $table = 'lgl_transporters_contracts_pricing';

    protected $fillable = [
        'contract_id',
        'contract_instance_id',
        'company_user_id',
        'transporter_user_id',
        'truck_type_id',
        'type', // ['CITY_TO_CITY', 'CITY_TO_CITY_MULTIPLE_LOCATIONS', 'CITY_TO_CITY_ROUND_TRIP']
        'from_country_id',
        'from_city_id',
        'to_country_id',
        'to_city_id',
        'price',
        'pre_additional_location_price',
    ];

    public function contract()
    {
        return $this->belongsTo(TransporterContract::class, 'contract_id', 'id');
    }

    public function contractInstance()
    {
        return $this->belongsTo(TransporterContractInstance::class, 'contract_instance_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'company_user_id', 'id');
    }

    public function transporter()
    {
        return $this->belongsTo(User::class, 'transporter_user_id', 'id');
    }

    public function truckType()
    {
        return $this->belongsTo(Content::class, 'truck_type_id', 'id');
    }

    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from_city_id', 'id');
    }

    public function toCity()
    {
        return $this->belongsTo(City::class, 'to_city_id', 'id');
    }

    public function fromCountry()
    {
        return $this->belongsTo(Country::class, 'from_country_id', 'id');
    }

    public function toCountry()
    {
        return $this->belongsTo(Country::class, 'to_country_id', 'id');
    }
    
    public function scopeWithFullDetails($query)
    {
        $query->with([
            'truckType' => function($rel) {
                $rel->withTrashed();
                $rel->with([
                    'translations' => function($rel) {},
                ]);
            },
            'fromCity' => function($rel) {
                $rel->withTrashed();
                $rel->with([
                    'country' => function($rel) {
                        $rel->withTrashed();
                    },
                ]);
            },
            'toCity' => function($rel) {
                $rel->withTrashed();
                $rel->with([
                    'country' => function($rel) {
                        $rel->withTrashed();
                    },
                ]);
            },
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
}
