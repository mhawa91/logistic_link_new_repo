<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Modules\SystemCore\Entities\Role;

use Modules\SystemCore\Entities\Country;
use Modules\SystemCore\Entities\City;
use Modules\ContentTypes\Entities\Company;
use Modules\ContentManager\Entities\Content;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory,
        Notifiable,
        SoftDeletes,
        HasRolesAndAbilities;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // Base Columns
        'name',
        'username',
        'phone_number',
        'email',
        'avatar',
        'password',
        'locale',
        'type',
        'completed_at',
        'reviewd_at',
        'sys_status',
        'created_by_user_id',
        'updated_by_user_id',

        // Modules Columns
        'branch_id',

        // Custom Columns
        'country_id',
        'city_id',
        'first_name',
        'last_name',
        'department',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    protected $with = [
        // 'Type'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            // 'BASE_USER_ID' => $this->id
        ];
    }

    public function Type()
    {
        return $this->belongsTo(Role::class, 'type', 'name');
    }

    // Modules Relations
    public function Branch()
    {
        return $this->belongsTo(\Modules\Branches\Entities\Branch::class, 'branch_id', 'id');
    }

    public function scopeAdvancedSearch($query, array $filter = [])
    {
        if (! empty($filter['name'])) {
            $query->where(function($query) use($filter) {
                $query->where('name' , 'like', "%{$filter['name']}%");
                $query->orWhereRaw('CONCAT_WS(" ", first_name, last_name) LIKE ?' ,"%{$filter['name']}%");    
            });
        }
        if (! empty($filter['username'])) {
            $query->where('username' , 'like', "%{$filter['username']}%");
        }
        if (! empty($filter['email'])) {
            $query->where('email' , 'like', "%{$filter['email']}%");
        }
        if (! empty($filter['phone_number'])) {
            $query->where('phone_number' , 'like', "%{$filter['phone_number']}%");
        }
        if (! empty($filter['type'])) {
            if (is_array($filter['type'])) {
                $query->whereIn('type', (array) $filter['type']);
            } else {
                $query->where('type', $filter['type']);
            }
        }
        if (! empty($filter['sys_status'])) {
            $query->where('sys_status', $filter['sys_status']);
        }
        if (\Module::installedAndEnabled('Branches') && ! empty($filter['branch_id'])) {
            $query->where('branch_id', $filter['branch_id']);
        }
        return $query;
    }

    // Custom Models
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
