<?php

namespace Modules\SystemCore\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipality extends Model
{
    use SoftDeletes;

    protected $table = 'municipalities';

    protected $fillable = [
        'country_id',
        'city_id',
        'name_en',
        'name_ar',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function Country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function City()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function Neighbourhoods()
    {
        return $this->hasMany(Neighbourhood::class, 'municipality_id', 'id');
    }
}