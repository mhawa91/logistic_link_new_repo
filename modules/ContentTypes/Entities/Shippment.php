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

class Shippment extends Model 
{
    use SoftDeletes;

    protected $table = 'lgl_shippments';

    protected $fillable = [
        'company_user_id',
        'transporter_user_id',
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
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    public function loadingLocation()
    {
        return $this->hasOne(Address::class, 'related_id', 'id')->where('related_type', 'SHIPPMENT_LOADING_POINT');
    }

    public function offloadingLocations()
    {
        return $this->hasMany(Address::class, 'related_id', 'id')->where('related_type', 'SHIPPMENT_OFFLOADING_POINT');
    }
}
