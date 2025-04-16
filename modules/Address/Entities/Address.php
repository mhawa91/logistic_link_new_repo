<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;
use Modules\SystemCore\Entities;
use App\Models\User;
use Modules\Providers\Entities\Provider;

class Address extends Model
{
    use SoftDeletes;

    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'phone_number',
        'email',
        'country_id',
        'city_id',
        'municipality_id',
        'neighbourhood_id',
        'postal_code',
        'street',
        'building',
        'floor',
        'appartment',
        'instructions',
        'full_address',
        'lat',
        'lng',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Provider()
    {
        return $this->hasOne(Provider::class, 'address_id', 'id');
    }

    public function Country()
    {
        return $this->belongsTo(Entities\Country::class, 'country_id', 'id');
    }

    public function City()
    {
        return $this->belongsTo(Entities\City::class, 'city_id', 'id');
    }

    public function Municipality()
    {
        return $this->belongsTo(Entities\Municipality::class, 'municipality_id', 'id');
    }

    public function Neighbourhood()
    {
        return $this->belongsTo(Entities\Neighbourhood::class, 'neighbourhood_id', 'id');
    }

    public function scopeWithDistance($Query, $lat, $lng, $prefix = 'distance')
    {
        $Query->addSelect([
            DB::raw('(
                DISTANCE( POINT('. (double) $lng .', '.  (double) $lat .'), POINT(lng, lat) ) / 1000
            ) AS ' . $prefix),
        ]);

        return $Query;
    }

    public function scopeInDistance($Query, $lat, $lng, $km_radius)
    {
        $Query->whereRaw(DB::raw('
            lat BETWEEN LAT_START(?, ?, ?) AND LAT_END(?, ?, ?)
            AND
            lng BETWEEN LNG_START(?, ?, ?) AND LNG_END(?, ?, ?)
        '), [
            $lat, $lng, $km_radius, $lat, $lng, $km_radius,
            $lat, $lng, $km_radius, $lat, $lng, $km_radius,
        ]);

        return $Query;
    }

    public function toGeo()
    {
        $locale = in_array(request()->locale ?? app()->getLocale(), ['ar', 'en']) ? (request()->locale ?? app()->getLocale()) : 'ar';
        return [
            'lat'          => $this->lat,
            'lng'          => $this->lng,
            'postal_code'  => $this->postal_code,
            'street'       => $this->street,
            'full_address' => $this->full_address,
            'country'      => empty($this->Country) ? null : [
                'id'   => $this->Country->id,
                'text' => $this->Country->{'name_' . $locale},
                'data' => $this->Country->toArray(),
            ],
            'city'         => empty($this->City) ? null : [
                'id'   => $this->City->id,
                'text' => $this->City->{'name_' . $locale},
                'data' => $this->City->toArray(),
            ],
            'municipality' => empty($this->Municipality) ? null : [
                'id'   => $this->Municipality->id,
                'text' => $this->Municipality->{'name_' . $locale},
                'data' => $this->Municipality->toArray(),
            ],
            'neighborhood' => empty($this->Neighbourhood) ? null : [
                'id'   => $this->Neighbourhood->id,
                'text' => $this->Neighbourhood->{'name_' . $locale},
                'data' => $this->Neighbourhood->toArray(),
            ],
        ];
    }
}
