<?php

namespace Modules\SystemCore\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $table = 'countries';

    protected $fillable = [
        'sortname',
        'name_en',
        'name_ar',
        'flag',
        'code',
        'currency',
        'currency_icon',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function Cities()
    {
        return $this->hasMany(City::class, 'country_id', 'id');
    }

    public function Municipalities()
    {
        return $this->hasMany(Municipality::class, 'country_id', 'id');
    }

    public function Neighbourhoods()
    {
        return $this->hasMany(Neighbourhood::class, 'country_id', 'id');
    }
}
