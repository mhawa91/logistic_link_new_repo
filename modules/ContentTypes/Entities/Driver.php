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
use Modules\SystemCore\Entities\Country;

class Driver extends Model 
{
    use SoftDeletes;

    protected $table = 'lgl_drivers';

    protected $fillable = [
        'transporter_user_id',
        'id_number',
        'name',
        'nationality_country_id',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    public function transportarUser()
    {
        return $this->belongsTo(User::class, 'transporter_user_id', 'id');
    }

    public function nationality()
    {
        return $this->belongsTo(Country::class, 'nationality_country_id', 'id');
    }

    public function ATTACHMENT_1()
    {
        return $this->hasOne(UploadedFile::class, 'related_id', 'id')
        ->where('related_type', 'driver')
        ->where('file_tag', 'ATTACHMENT_1');
    }
}
