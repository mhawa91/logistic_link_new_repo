<?php

namespace Modules\Admin\Entities;

use Validator;
use Bouncer;
use Modules\SystemCore\Entities\Role;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Entities\Ability;
use Modules\SystemCore\System\Services\ModelService;

class RoleService extends ModelService
{
    public function register() 
    {
        $this->modelNamespace = Role::class;
        // $this->guard();
        if ($this->can('update')) {
            $this->registerDataTableAction('update', function($Row) {
                return [
                    'label'  => __('ui::strings.update'),
                    'action' => route('UsersController@update', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('delete')) {
            $this->registerDataTableAction('delete', function($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('UsersController@postDelete', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('restore')) {
            $this->registerDataTableAction('restore', function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('UsersController@postRestore', ['model' => $Row->id]),
                ];
            });
        }
        if ($this->can('permaDelete')) {
            $this->registerDataTableAction('permaDelete', function($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('UsersController@postPermaDelete', ['model' => $Row->id]),
                ];
            });
        }
    }

    public function validator($type = 'CREATE', array $data = [])
    {
        $rules = [
            'name'        => 'required|unique:roles,name',
            'level'       => 'nullable|numeric',
            'scope'       => 'nullable|max:191',
            'suspendable' => 'nullable|in:Y,N',
            'updatable'   => 'nullable|in:Y,N',
        ];

        return Validator::make($data, $rules);
    }

    public function findByAny(string $string)
    {
        $string = strtolower($string);
        $this->model()->where(function ($Query) use($string) {
            $Query->where('id', $string);
            $Query->orWhere('name', $string);
            $Query->orWhereTranslationLike('title', "%$string%");
        });
        return $this->firstOrFail()->data();
    }

    public function findById(string $id)
    {
        $this->findOrFail($id);

        return $this->data();
    }

    public function findByName(string $name)
    {
        $this->model()->where('name', $name);

        return $this->firstOrFail();
    }

    public function create(array $data = [])
    {
        $data = array_merge([
            'name'        => null,
            'level'       => null,
            'scope'       => null,
            'suspendable' => 'Y',
            'updatable'   => 'Y',
        ], $data);

        if (isset($data['translations'])) {
            $data = array_merge($data, $data['translations']);
            unset($data['translations']);
        }
        return parent::create( $data )->data();
    }

    public function update(array $data = [])
    {
        $data = array_merge([
            'name'        => null,
            'level'       => null,
            'scope'       => null,
            'suspendable' => 'Y',
            'updatable'   => 'Y',
        ], $data);

        if (isset($data['translations'])) {
            $data = array_merge($data, $data['translations']);
            unset($data['translations']);
        }

        $model = parent::update( $data );

        return $model->data();
    }

    public function get()
    {
        $data = $this->model();
        if ( auth()->check() ) {
            if (! auth()->user()->isAn('ROOT')) {
                $data->where('name', '!=', 'ROOT');
            }
        }
        return parent::get();
    }
}
