<?php

namespace Modules\SystemCore\System\Services;

use Exception;
use DataTables;
use Closure;
use Validator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Modules\SystemCore\Exceptions\PermissionException;
use Modules\SystemCore\System\Contracts\EloquentInterface;

abstract class EloquentBaseService implements EloquentInterface
{
    /**
     * @var Model
     */
    protected $model; 

    /**
     * @var Model
     */
    protected static $isGuardActive = true;

    /**      
     * BaseRepository constructor.      
     *      
     * @param Model $model
     */     
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param void
     * @return Model
     */
    public function when(bool $Condition, Closure $do): self
    {
        if ($Condition) {
            $do($this);
        }
        return $this;
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
     * @param string|array|null $filter
     * @return void
     */
    public function autoSearch($filter): self
    {
        if (empty($filter)) {
            return $this;
        }
        if (is_array($filter)) {
            return $this->advancedSearch($filter);
        }
        return $this->search($filter);
    }

    /**
     * @param string|null $filter
     * @return void
     */
    public function search(?string $filter): self
    {
        return $this;
    }

    /**
     * @param array|null $filter
     * @return void
     */
    public function advancedSearch(?array $filter): self
    {
        return $this;
    }

    /**
     * @param void
     * @return DataTables
     */
    public function dataTable()
    {
        $Actions = static::dataTableAllowedActions();
        return DataTables::of($this->model->newQuery())
        ->filter(function($Query) {})
        ->addColumn('actions', function($Row) use($Actions) {
            return ! empty($rowActions = static::dataTableRowActions($Actions, $Row)) ? $rowActions : null;
        });
    }

    /**
     * @param void
     * @return array
     */
    public static function dataTableAllowedActions(): array
    {
        return [];
    }

    /**
     * @param array $allowedActions
     * @param Model $Row
     * @return array
     */
    public static function dataTableRowActions(array $allowedActions, $Row): array
    {
        $Res = [];
        foreach ($allowedActions as $action => $Closure) {
            if ($Closure) {
                $Res[ $action ] = $Closure($Row);
            }
        }
        return $Res;
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        static::canStrict('create');
        return $this->model->create($attributes);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return Model
     */
    public function update($id, array $attributes): Model
    {
        $model = $this->model->withTrashed()->find($id);
        static::canStrict('update', $model);
        $model->update($attributes);
        return $model;
    }

     /**
     * @param void
     * @return self
     */
    public function withTrashed(): self
    {
        $this->model = $this->model->withTrashed();
        return $this;
    }

    /**
     * @param void
     * @return self
     */
    public function onlyTrashed(): self
    {
        $this->model = $this->model->onlyTrashed();
        return $this;
    }

    /**
     * @param int $id
     * @return Model
     */
    public function find($id):? Model
    {
        $model = $this->model->find($id);
        static::canStrict('read', $model);
        return $model;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function softDelete($id): bool
    {
        $model = $this->model->find($id);
        static::canStrict('delete', $model);
        return $model->delete();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function permaDelete($id): bool
    {
        $model = $this->model->withTrashed()->find($id);
        static::canStrict('permaDelete', $model);
        return $model->forceDelete();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function restore($id): bool
    {
        $model = $this->model->withTrashed()->find($id);
        static::canStrict('restore', $model);
        return $model->restore();
    }

    /**
     * @param int $limitPerPage
     * @return LengthAwarePaginator
     */
    public function paginate($limitPerPage): LengthAwarePaginator
    {
        return $this->model->paginate($limitPerPage);
    }

    /**
     * @param int $id
     * @param string $newStatus
     * @return bool
     */
    public function toggleStatus($id, string $newStatus): bool
    {
        $model = $this->model->find($id);
        static::canStrict('statusUpdate', $model);
        return $model->update($attributes);
    }

    /**
     * @param LengthAwarePaginator $Rows
     * @param array|null $options
     * @return void
     */
    public static function toSelect2(LengthAwarePaginator $Rows, ?array $Options = []): array
    {
        $Options = array_merge([
            'nullable' => true,
        ], $Options);

        $Result = [
            'results'     => [],
            'pagination'  => [
                'more' => $Rows->hasMorePages(),
            ]
        ];
        if ($Options['nullable'] !== 'false' && $Rows->currentPage() == 1) {
            $Result['results'][] = static::toSelect2Option(null);
        }
        foreach ($Rows as $key => $value) {
            $Result['results'][] = static::toSelect2Option($value);
        }
        return $Result;
    }

    /**
     * @param Model $Row
     * @return array
     */
    public static function toSelect2Option($Row): array
    {
        return $Row ? [
            'id'   => $Row->id,
            'text' => $Row->id,
            'data' => $Row->toArray(),
        ] : [
            'id'   => '0',
            'text' => '-- Select --',
            'data' => [],
        ];
    }

    /**
     * @param int|null $limit
     * @return Collection
     */
    public static function all(?int $limit = null)
    {
        static::canStrict('read');
        // $this->model = $limit ? $this->model->limit($limit) : $this->model;
        // return $this->model->get();
    }

    /**
     * @param Model $Row
     * @return bool
     */
    public static function toResource($Row): object
    {
        return $Row;
    }

    /**
     * @param Collection $Rows
     * @return bool
     */
    public static function toCollectionResource(Collection $Rows): object
    {
        return $Rows;
    }

    /**
     * @param string $action
     * @return string
     */
    public static function permissionNamespace(?string $action): string
    {
        return static::class . ( $action ? '@' . $action : '' );
    }

    /**
     * @param string $action
     * @param Model|null $Row
     * @return bool
     */
    public static function can(string $action, $Row = null): bool
    {
        if (! static::$isGuardActive) {
            return true;
        }
        if (! auth()->check()) {
            return false;
        }
        if (auth()->user()->isAn('ROOT')) {
            return true;
        }
        if ($Row && static::canByModel($action, $Row)) {
            return true;
        }
        if (auth()->user()->can(static::permissionNamespace($action))) {
            return true;
        }
        return false;
    }

    /**
     * @param string $action
     * @param Model|null $Row
     * @return bool|PermissionException
     */
    public static function canStrict(string $action, $Row = null): bool
    {
        if (! static::$isGuardActive) {
            return true;
        }
        if (static::can($action, $Row)) {
            return true;
        }
        static::throwPermissionException($action);
    }

    /**
     * @param void
     * @return void
     */
    public static function disableGuard(): void
    {
        static::$isGuardActive = false;
    }

    /**
     * @param void
     * @return void
     */
    public static function enableGuard(): void
    {
        static::$isGuardActive = true;
    }

    /**
     * @param string $action
     * @param Model $Row
     * @return bool
     */
    public static function canByModel(string $action, $Row): bool
    {
        return false;
    }

    /**
     * @param string $action
     * @return bool
     */
    public static function throwPermissionException(string $action): void
    {
        $PermissionException = new PermissionException('PERMISSION_ERROR:' . static::permissionNamespace($action), 403);
        $PermissionException->setNeededPermissions([static::permissionNamespace($action)]);
        throw $PermissionException;
    }
}