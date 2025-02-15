<?php

namespace Modules\SystemCore\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Neighbourhood extends Model
{
    use SoftDeletes;

    protected $table = 'neighbourhoods';

    protected $fillable = [
        'country_id',
        'city_id',
        'municipality_id',
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

    public function Municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'id');
    }
}