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
use DB;

class Company extends Model 
{
    use SoftDeletes;

    protected $table = 'lgl_company_profile';

    protected $fillable = [
        'user_id',
        'profile_type_category_id',
        'registration_type_category_id',
        'company_logo',
        'company_name',
        'contact_name',
        'company_email',
        'company_phone_number_ext',
        'company_phone_number',
        'registration_number',
        'registration_number_string',
        'tax_identification_number',
        'tax_identification_number_string',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function profileType()
    {
        return $this->belongsTo(Role::class, 'profile_type_category_id', 'id');
    }

    public function registrationTypeCategory()
    {
        return $this->belongsTo(Content::class, 'registration_type_category_id', 'id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'related_id', 'id')->where('related_type', 'PRIMARY_COMPANY_ADDRESS');
    }

    public function scopeWithReviewsStates($query)
    {
        return $query->addSelect([
            DB::raw('IFNULL((SELECT COUNT(id) FROM lgl_shippments_reviews WHERE lgl_shippments_reviews.transportar_id = lgl_company_profile.id), 0) as reviews_count'),
            DB::raw('ROUND(IFNULL((SELECT AVG(rate) FROM lgl_shippments_reviews WHERE lgl_shippments_reviews.transportar_id = lgl_company_profile.id), 0), 2) as reviews_rate'),
        ]);
    }

    public function scopeWithBaseSelect($query)
    {
        return $query->addSelect([
            DB::raw('
                DATE_FORMAT(lgl_company_profile.created_at, "%b %d, %Y | %h:%i %p") AS new_created_at,
                DATE_FORMAT(lgl_company_profile.updated_at, "%b %d, %Y | %h:%i %p") AS new_updated_at,
                (CASE 
                    WHEN lgl_company_profile.company_logo IS NULL THEN "'. app('GraphManager')->defaultSrc('400x400', 'company') .'"
                    ELSE CONCAT("'. app('GraphManager')->src('400x400', 'company', '-SQL-') .'", lgl_company_profile.company_logo)
                END) AS logo,
                CONCAT("+", lgl_company_profile.company_phone_number_ext, lgl_company_profile.company_phone_number) AS full_phone_number
            '),
        ]);
    }

    public function scopeOnlySuitableForShippment($query, $shippment)
    {
        return $query->whereHas('user', function($query) use($shippment) {
            $query->where('users.type', 'TRANSPORTER');
            $query->where('users.sys_status', 'ACTIVE');
            $query->whereHas('contracts', function($query) use($shippment) {
                $query->whereHas('instances', function($query) use($shippment) {
                    $query->whereHas('pricing',function($query) use($shippment) {
                        $query->where('truck_type_id', $shippment->truck_type_id);
                        $query->where('from_country_id', $shippment->from_country_id);
                        $query->where('from_city_id', $shippment->from_city_id);
                        $query->where('to_country_id', $shippment->to_country_id);
                        $query->where('to_city_id', $shippment->to_city_id);
                        $query->where('type', $shippment->type);
                    });
                });
            });
        });
    }
}
