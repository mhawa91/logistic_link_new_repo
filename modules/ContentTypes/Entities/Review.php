<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

use Modules\SystemCore\Entities;

class Review extends Model 
{
    protected $table = 'lgl_shippments_reviews';

    protected $fillable = [
        'shippment_id',
        'company_user_id',
        'company_id',
        'transportar_user_id',
        'transportar_id',
        'rate',
        'created_at',
        'updated_at',
    ];
}
