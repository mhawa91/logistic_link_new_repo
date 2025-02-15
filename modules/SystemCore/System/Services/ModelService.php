<?php

namespace Modules\SystemCore\System\Services;

use DataTables;
use Exception;
use Closure;
use Modules\SystemCore\Entities\AbilityGroup;
use Modules\SystemCore\Exceptions\PermissionException;
use Modules\SystemCore\System\Services\ExportService;

class ModelService
{
    protected $modelNamespace   = null;

    protected $modelQuery       = null;

    protected $data             = null;

    protected $protected        = false;

    protected $dataTableActions    = [];

    protected $ajaxSelectCallbacks = [];

    public function __construct() 
    {
        $this->register();
    }

    public function register() 
    {}

    public function new() 
    {
        return (new static);
    }

    public function model($model = null) 
    {
        if (! is_null($model)) {
            $this->modelQuery = $model->query();
        }
        if (is_null($this->modelQuery)) {
            $this->modelQuery = app($this->modelNamespace)->query();
        }
        return $this->modelQuery;
    }

    public function when($Condition, Closure $do)
    {
        if ($Condition) {
            $do($this);
        }
        return $this;
    }

    public function withTrashed()
    {
        if ($this->can('restore') || $this->can('permaDelete')) {
            $this->model()->withTrashed();
        }
        return $this;
    }

    public function onlyTrashed()
    {
        if ($this->can('restore') || $this->can('permaDelete')) {
            $this->model()->onlyTrashed();
        }
        return $this;
    }

    public function isGuarded()
    {
        return $this->protected;
    }

    public function can(string $permission)
    {
        if (! $this->isGuarded()) {
            return true;
        }
        if (! auth()->check()) {
            return false;
        }        
        return auth()->user()->isAn('ROOT') || auth()->user()->can($this->getPermissionName($permission));
    }

    public function getPermissionGroupName()
    {
        return str_replace(['\\'], '_', $this->modelNamespace);
    }

    public function getPermissionName(string $permission)
    {
        return str_replace(['\\'], '_', $this->modelNamespace) . '@' . $permission;
    }

    public function getPermissionMiddlewareName(string $type = 'HTTP', string $permission)
    {
        if ($type == 'API') {
            return config('systemcore.auth.API_PERMISSIONS_MIDDLEWARE') . ':' . $this->getPermissionName($permission);
        }
        return config('systemcore.auth.HTTP_PERMISSIONS_MIDDLEWARE') . ':' . $this->getPermissionName($permission);
    }

    public function throwPermissionException(string $permission)
    {
        $PermissionException = new PermissionException('permissions_error:' . $this->getPermissionName($permission), 403);
        $PermissionException->setNeededPermissions([$this->getPermissionName($permission)]);
        throw $PermissionException;
    }

    public function guard()
    {
        $this->protected = true;

        return $this;
    }

    public function unguard()
    {
        $this->protected = false;
        
        return $this;
    }

    public function filter($query, $data = null)
    {
        if (is_array($data)) {
            $this->advancedFilter($query, $data);
        }
        else if (! empty($data)) {
            $this->keywordFilter($query, $data);
        }
        return $this;
    }

    protected function keywordFilter($query, string $keyword = null)
    {}

    protected function advancedFilter($query, array $filter = [])
    {}

    public function dataTable($filter = null)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }
        return DataTables::of( $this->model() )
        ->filter(function($Model) use($filter) {
            $this->filter($Model, $filter);
        })
        ->addColumn('actions', function($Row) {
            return ! empty($Actions = $this->getDataTableActions($Row)) ? $Actions : null;
        });
    }

    public function export($filter = null)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }

        $Model = $this->model();

        $this->filter($Model, $filter);

        return null;
    }

    public function getDataTableActions($Row)
    {
        $Result = [];
        foreach ($this->dataTableActions as $name => $action) {
            if ($actionResult = $action($Row)) {
                $Result[ $name ] = $actionResult;
            }
        }
        return $Result;
    }

    public function registerDataTableAction(string $name, Closure $action)
    {
        $this->dataTableActions[ $name ] = $action;
    }

    public function getAjaxSelectResult($Row)
    {
        $Result = [];
        foreach ($this->ajaxSelectCallbacks as $name => $action) {
            if ($actionResult = $action($Row)) {
                $Result = array_merge($Result, $actionResult);
            }
        }
        return $Result;
    }

    public function registerAjaxSelectCallback(Closure $action)
    {
        $this->ajaxSelectCallbacks[] = $action;
    }

    public function ajaxSelect($filter = null, array $Options = [], int $page = 1, int $limit = 30)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }

        $this->filter($this->model(), $filter);

        $this->data = $this->model()->paginate($limit);

        $Result = [
            'results'     => [],
            'pagination'  => [
                'more' => $this->data->hasMorePages(),
            ]
        ];
        if ($Options['nullable'] !== 'false' && $page == 1) {
            $Result['results'][] = $this->getAjaxSelectResult(null);
        }
        foreach ($this->data as $key => $value) {
            $Result['results'][] = $this->getAjaxSelectResult($value);
        }
        return $Result;
    }

    public function find($id)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }
        $this->data = $this->model()->find($id);
        return $this;
    }

    public function findOrFail($id)
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }
        $this->data = $this->model()->findOrFail($id);
        return $this;
    }

    public function first()
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }
        $this->data = $this->model()->first();
        return $this;
    }

    public function get()
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }
        $this->data = $this->model()->get();
        return $this;
    }

    public function firstOrFail()
    {
        if (! $this->can('read')) {
            $this->throwPermissionException('read');
        }
        $this->data = $this->model()->firstOrFail();
        return $this;
    }

    public function validator($type = 'create', array $data = [])
    {
        return Validator::make($data, []);
    }

    public function validate($type = 'create', array $data = [])
    {
        $this->validator($type, $data)->validate();
    }

    public function create(array $data)
    {
        if (! $this->can('create')) {
            $this->throwPermissionException('create');
        }
        $this->data = $this->model()->create($data);
        return $this;
    }

    public function update(array $data)
    {
        if (! $this->can('update')) {
            $this->throwPermissionException('update');
        }
        $this->data ? $this->data->update($data) : $this->model()->update($data);
        return $this;
    }

    public function delete()
    {
        if (! $this->can('delete')) {
            $this->throwPermissionException('delete');
        }
        $this->data ? $this->data->delete() : $this->model()->delete();
        return $this;
    }

    public function restore()
    {
        if (! $this->can('restore')) {
            $this->throwPermissionException('restore');
        }
        $this->data ? $this->data->restore() : $this->model()->restore();
        return $this;
    }

    public function permaDelete()
    {
        if (! $this->can('permaDelete')) {
            $this->throwPermissionException('permaDelete');
        }
        $this->data ? $this->data->forceDelete() : $this->model()->forceDelete();
        return $this;
    }

    public function statusUpdate(string $status)
    {
        if (! $this->can('statusUpdate')) {
            $this->throwPermissionException('statusUpdate');
        }
        $data = ['sys_status' => $status];
        $this->data ? $this->data->update($data) : $this->model()->update($data);
        return $this;
    }

    public function data()
    {
        return $this->data;
    }

    public function firstOrNew(array $condition, array $data = []) : bool
    {
        $this->data = $this->new()->model()->firstOrNew($condition);
        foreach ($data as $column => $value) {
            $this->data->{ $column } = $value;
        }
        $this->data->save();
        return $this->data;
    }

    public function installedPermissions()
    {
        return AbilityGroup::with('translations', 'Abilities.translations')->where('code', $this->getPermissionGroupName())->first();
    }

    public function install() : bool
    {
        return true;
    }

    public function uninstall() : bool
    {
        return true;
    }

    public function repair() : bool
    {
        return true;
    }
}