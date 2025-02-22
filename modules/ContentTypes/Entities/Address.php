<?php

namespace Modules\ContentTypes\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

use Modules\SystemCore\Entities;

class Address extends Model 
{
    use SoftDeletes;

    protected $table = 'lgl_addressess';

    protected $fillable = [
        'related_id',
        'related_type',
        'address_tag',
        'country_id',
        'city_id',
        'street',
        'postal_code',
        'line_1',
        'line_2',
        'full_address',
        'public_notes',
        'private_notes',
        'primary_contact_company_name',
        'primary_contact_source_name',
        'primary_contact_name',
        'primary_contact_email',
        'primary_contact_phone_number_ext',
        'primary_contact_phone_number',
        'lat',
        'lng',
        'round_trip',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function country()
    {
        return $this->belongsTo(Entities\Country::class, 'country_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(Entities\City::class, 'city_id', 'id');
    }
}
