<?php

namespace Modules\ContactUs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Modules\SystemCore\Entities\Helpers\TranslatableHelper;

class ContactUs extends Model 
{
    use SoftDeletes;

    protected $table = 'contact_us';

    protected $fillable = [
        'email',
        'phone_number',
        'name',
        'subject',
        'message',
        'created_by_user_id',
        'updated_by_user_id',
        'sys_status',
    ];
}
