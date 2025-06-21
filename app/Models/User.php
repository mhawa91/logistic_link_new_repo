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
use DB;

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

    public function company()
    {
        return $this->hasOne(\Modules\ContentTypes\Entities\Company::class, 'user_id', 'id');
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

    public function contracts()
    {
        return $this->hasMany(\Modules\ContentTypes\Entities\TransporterContract::class, 'transporter_user_id', 'id');
    }

    public function scopeWithReviewsStates($query)
    {
        return $query->addSelect([
            DB::raw('(SELECT COUNT(*) FROM lgl_shippments_reviews WHERE lgl_shippments_reviews.transportar_user_id = users.id) as reviews_count'),
            DB::raw('(SELECT AVG(rate) FROM lgl_shippments_reviews WHERE lgl_shippments_reviews.transportar_user_id = users.id) as reviews_rate'),
        ]);
    }

    public function shippmentsAsClient()
    {
        return $this->hasMany(\Modules\ContentTypes\Entities\Shippment::class, 'company_user_id', 'id');
    }

    public function shippmentsAsTransportar()
    {
        return $this->hasMany(\Modules\ContentTypes\Entities\Shippment::class, 'transporter_user_id', 'id');
    }

    public function scopeWithMetaSelect($query)
    {
        $query->addSelect([
            DB::raw('
                company.company_name AS company_name,
                company.company_email AS company_email,
                (CASE 
                    WHEN company.company_logo IS NULL THEN "'. app('GraphManager')->defaultSrc('400x400', 'company') .'"
                    ELSE CONCAT("'. app('GraphManager')->src('400x400', 'company', '-SQL-') .'", company.company_logo)
                END) AS company_logo,
                company.company_phone_number_ext AS company_phone_number_ext,
                company.company_phone_number AS company_phone_number,
                company.registration_number AS registration_number,
                company.tax_identification_number AS tax_identification_number,
                company.sys_status AS company_sys_status
            '),
        ]);

        $query->join('lgl_company_profile AS company', 'company.user_id', '=', 'users.id');

        return $query;
    }

    public function scopeWithTransportarInvoicesFinanceSelect($query)
    {
        $query->addSelect([
            DB::raw('
                IFNULL(COUNT(invoices.id), 0)            AS invoices_count,
                IFNULL(SUM(invoices.sub_total), 0)       AS invoices_sub_total,
                IFNULL(SUM(invoices.discounts_total), 0) AS invoices_discounts_total,
                IFNULL(SUM(invoices.total), 0)           AS invoices_total
            '),
            DB::raw('
                IFNULL(SUM(
                    CASE 
                        WHEN invoices.sys_status != "PAID" AND invoices.due_at >= NOW() THEN 1 
                        ELSE 0
                    END
                ), 0) AS shippments_invoiced_count
            '),
            DB::raw('
                IFNULL(SUM(
                    CASE 
                        WHEN invoices.sys_status != "PAID" AND invoices.due_at >= NOW() THEN invoices.total 
                        ELSE 0
                    END
                ), 0) AS shippments_invoiced_total
            '),
            DB::raw('
                IFNULL(SUM(
                    CASE 
                        WHEN invoices.sys_status != "PAID" AND invoices.due_at < NOW() THEN 1 
                        ELSE 0
                    END
                ), 0) AS invoices_dues_count
            '),
            DB::raw('
                IFNULL(SUM(
                    CASE 
                        WHEN invoices.sys_status != "PAID" AND invoices.due_at < NOW() THEN invoices.total 
                        ELSE 0
                    END
                ), 0) AS invoices_dues_total
            '),
            DB::raw('(
                SELECT COUNT(*)
                FROM lgl_shippments AS shippments
                WHERE shippments.transporter_user_id = users.id
                AND shippments.company_user_id = invoices.company_user_id
                AND shippments.sys_status = "DELIVERED_PENDING_PAYMENT"
                AND shippments.deleted_at IS NULL
            ) shippments_uninvoiced_count'),
            DB::raw('(
                SELECT SUM(shippments.total_price)
                FROM lgl_shippments AS shippments
                WHERE shippments.transporter_user_id = users.id
                AND shippments.company_user_id = invoices.company_user_id
                AND shippments.sys_status = "DELIVERED_PENDING_PAYMENT"
                AND shippments.deleted_at IS NULL
            ) shippments_uninvoiced_total'),
        ]);

        $query->join('lgl_invoices AS invoices', 'invoices.transportar_user_id', '=', 'users.id');

        return $query;
    }

    public function scopeWithTransportarShippmentsFinanceSelect($query)
    {
        $query->addSelect([
            DB::raw('
                client_user.name AS client_user_name,
                client_user.email AS client_user_email,
                client.company_name AS client_company_name,
                client.company_email AS client_company_email,
                (CASE 
                    WHEN client.company_logo IS NULL THEN "'. app('GraphManager')->defaultSrc('400x400', 'company') .'"
                    ELSE CONCAT("'. app('GraphManager')->src('400x400', 'company', '-SQL-') .'", client.company_logo)
                END) AS client_company_logo,
                client.company_phone_number_ext AS client_company_phone_number_ext,
                client.company_phone_number AS client_company_phone_number,
                client.registration_number AS client_registration_number,
                client.tax_identification_number AS client_tax_identification_number,
                client.sys_status AS client_sys_status
            '),
        ]);

        $query->join('users AS client_user', 'client_user.id', '=', 'invoices.company_user_id');
        $query->join('lgl_company_profile AS client', 'client.user_id', '=', 'invoices.company_user_id');

        return $query;
    }

    public function scopeWithClientInvoicesFinanceSelect($query)
    {
        $query->addSelect([
            DB::raw('
                IFNULL(COUNT(invoices.id), 0)            AS invoices_count,
                IFNULL(SUM(invoices.sub_total), 0)       AS invoices_sub_total,
                IFNULL(SUM(invoices.discounts_total), 0) AS invoices_discounts_total,
                IFNULL(SUM(invoices.total), 0)           AS invoices_total
            '),
            DB::raw('
                IFNULL(SUM(
                    CASE 
                        WHEN invoices.sys_status != "PAID" AND invoices.due_at >= NOW() THEN 1 
                        ELSE 0
                    END
                ), 0) AS shippments_invoiced_count
            '),
            DB::raw('
                IFNULL(SUM(
                    CASE 
                        WHEN invoices.sys_status != "PAID" AND invoices.due_at >= NOW() THEN invoices.total 
                        ELSE 0
                    END
                ), 0) AS shippments_invoiced_total
            '),
            DB::raw('
                IFNULL(SUM(
                    CASE 
                        WHEN invoices.sys_status != "PAID" AND invoices.due_at < NOW() THEN 1 
                        ELSE 0
                    END
                ), 0) AS invoices_dues_count
            '),
            DB::raw('
                IFNULL(SUM(
                    CASE 
                        WHEN invoices.sys_status != "PAID" AND invoices.due_at < NOW() THEN invoices.total 
                        ELSE 0
                    END
                ), 0) AS invoices_dues_total
            '),
            DB::raw('(
                SELECT COUNT(*)
                FROM lgl_shippments AS shippments
                WHERE shippments.company_user_id = users.id
                AND shippments.transporter_user_id = invoices.transportar_user_id
                AND shippments.sys_status = "DELIVERED_PENDING_PAYMENT"
                AND shippments.deleted_at IS NULL
            ) shippments_uninvoiced_count'),
            DB::raw('(
                SELECT SUM(shippments.total_price)
                FROM lgl_shippments AS shippments
                WHERE shippments.company_user_id = users.id
                AND shippments.transporter_user_id = invoices.transportar_user_id
                AND shippments.sys_status = "DELIVERED_PENDING_PAYMENT"
                AND shippments.deleted_at IS NULL
            ) shippments_uninvoiced_total'),
        ]);

        $query->join('lgl_invoices AS invoices', 'invoices.company_user_id', '=', 'users.id');

        return $query;
    }

    public function scopeWithClientShippmentsFinanceSelect($query)
    {
        $query->addSelect([
            DB::raw('
                transportar_user.name AS transportar_user_name,
                transportar_user.email AS transportar_user_email,
                transportar.company_name AS transportar_company_name,
                transportar.company_email AS transportar_company_email,
                (CASE 
                    WHEN transportar.company_logo IS NULL THEN "'. app('GraphManager')->defaultSrc('400x400', 'company') .'"
                    ELSE CONCAT("'. app('GraphManager')->src('400x400', 'company', '-SQL-') .'", transportar.company_logo)
                END) AS transportar_company_logo,
                transportar.company_phone_number_ext AS transportar_company_phone_number_ext,
                transportar.company_phone_number AS transportar_company_phone_number,
                transportar.registration_number AS transportar_registration_number,
                transportar.tax_identification_number AS transportar_tax_identification_number,
                transportar.sys_status AS transportar_sys_status
            '),
        ]);

        $query->join('users AS transportar_user', 'transportar_user.id', '=', 'invoices.company_user_id');
        $query->join('lgl_company_profile AS transportar', 'transportar.user_id', '=', 'invoices.company_user_id');

        return $query;
    }
}
