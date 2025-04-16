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

class ShippmentTransporter extends Model 
{
    protected $table = 'lgl_shippments_matched_transporters';

    protected $fillable = [
        'shippment_id',
        'company_user_id',
        'transporter_user_id',
        'price',
        'pre_additional_location_price',
        'total_price',
        'status',
        'requested_at',
        'responded_at',
    ];
}
