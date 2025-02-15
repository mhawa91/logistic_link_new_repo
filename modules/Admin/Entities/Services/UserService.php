<?php

namespace Modules\Admin\Entities\Services;

use Validator;
use DataTables;
use App\Models\User;
use Modules\Admin\Entities\Contracts\UserInterface;
use Modules\SystemCore\System\Services\EloquentBaseService;

class UserService extends EloquentBaseService implements UserInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @param void
     * @return Validator
     */
    public static function validator(array $attributes, string $action)
    {
        return Validator::make($attributes, []);
    }

    /**
     * @param string|null $filter
     * @return void
     */
    public function search(?string $filter): self
    {
        $this->model = $this->model->where(function($Model) use($filter) {
            $Model->where('name'           , 'like', "%{$filter}%");
            $Model->orWhere('email'        , 'like', "%{$filter}%");
            $Model->orWhere('username'     , 'like', "%{$filter}%");
            $Model->orWhere('phone_number' , 'like', "%{$filter}%");
            $Model->orWhereHas('roles' , function($Rel) use($filter) {
                $Rel->whereHas('translations', function($Rel) use($filter) {
                    $Rel->where('title', 'like', "%{$filter}%");
                });
            });
        });
        return $this;
    }

    /**
     * @param array|null $filter
     * @return void
     */
    public function advancedSearch(?array $filter): self
    {
        if (! empty($filter['name'])) {
            $this->model = $this->model->where('name' , 'like', "%{$filter['name']}%");
        }
        if (! empty($filter['username'])) {
            $this->model = $this->model->where('username' , 'like', "%{$filter['username']}%");
        }
        if (! empty($filter['email'])) {
            $this->model = $this->model->where('email' , 'like', "%{$filter['email']}%");
        }
        if (! empty($filter['phone_number'])) {
            $this->model = $this->model->where('phone_number' , 'like', "%{$filter['phone_number']}%");
        }
        if (! empty($filter['type'])) {
            if (is_array($filter['type'])) {
                $this->model = $this->model->whereIn('type', (array) $filter['type']);
            }
            else {
                $this->model = $this->model->where('type', $filter['type']);
            }
        }
        if (\Module::installedAndEnabled('Branches')) {
            if (! empty($filter['branch_id'])) {
                $this->model = $this->model->where('branch_id' , $filter['branch_id']);
            }
        }
        return $this;
    }

    /**
     * @param void
     * @return array
     */
    public static function dataTableAllowedActions(): array
    {
        return [
            'update' => ! static::can('update') ? null : function($Row) {
                return [
                    'label'  => __('ui::strings.update'),
                    'action' => route('UsersController@update', ['model' => $Row->id]),
                ];
            },
            'delete' => ! static::can('delete') ? null : function($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('UsersController@postDelete', ['model' => $Row->id]),
                ];
            },
            'restore' => ! static::can('restore') ? null : function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('UsersController@postRestore', ['model' => $Row->id]),
                ];
            },
            'permaDelete' => ! static::can('permaDelete') ? null : function($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('UsersController@postPermaDelete', ['model' => $Row->id]),
                ];
            }
        ];
    }

    /**
     * @param User $Row
     * @return array
     */
    public static function toSelect2Option($Row): array
    {
        return $Row ? [
            'id'   => $Row->id,
            'text' => $Row->name,
            'data' => $Row->toArray(),
        ] : [
            'id'   => '0',
            'text' => '-- Select --',
            'data' => [],
        ];
    }

    /**
     * @param string $action
     * @return string
     */
    public static function permissionNamespace(?string $action): string
    {
        return User::class . ( $action ? '@' . $action : '' );
    }

    /**
     * @param string $action
     * @param User $Row
     * @return bool
     */
    public static function canByModel(string $action, $Row): bool
    {
        return false;
    }

    /**
     * @param void
     * @return DataTables
     */
    public function dataTable()
    {
        return parent::dataTable();
    }
}