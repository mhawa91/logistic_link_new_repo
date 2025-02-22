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
        'tax_identification_number',
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
}
