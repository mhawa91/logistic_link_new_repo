<?php

namespace Modules\SystemCore\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class SystemConfig extends Model 
{
    use SoftDeletes;
    
    protected $table    = 'system_config';

    protected $fillable = [
        'x_key',
        'x_value',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
    ];
}
