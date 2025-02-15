<?php

namespace Modules\SystemCore\System\Contracts;

use Exception;
use DataTables;
use Closure;
use Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EloquentInterface
{
    /**
     * @param void
     * @return Model
     */
    public function when(bool $Condition, Closure $do): self;

    /**
     * @param void
     * @return Validator
     */
    public static function validator(array $attributes, string $action);

    /**
     * @param string|array|null $filter
     * @return self
     */
    public function autoSearch($filter): self;

    /**
     * @param string|null $filter
     * @return self
     */
    public function search(?string $filter): self;

    /**
     * @param array|null $filter
     * @return void
     */
    public function advancedSearch(?array $filter): self;

    /**
     * @param void
     * @return DataTables
     */
    public function dataTable();

    /**
     * @param void
     * @return array
     */
    public static function dataTableAllowedActions(): array;

    /**
     * @param array $allowedActions
     * @param Model $Row
     * @return array
     */
    public static function dataTableRowActions(array $allowedActions, $Row): array;

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * @param int $id
     * @param array $attributes
     * @return Model
     */
    public function update($id, array $attributes): Model;

    /**
     * @param void
     * @return self
     */
    public function withTrashed(): self;

    /**
     * @param void
     * @return self
     */
    public function onlyTrashed(): self;

    /**
     * @param int $id
     * @return Model
     */
    public function find($id):? Model;

    /**
     * @param int $id
     * @return bool
     */
    public function softDelete($id): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function permaDelete($id): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function restore($id): bool;

    /**
     * @param int $limitPerPage
     * @return LengthAwarePaginator
     */
    public function paginate($limitPerPage): LengthAwarePaginator;

    /**
     * @param int $id
     * @param string $newStatus
     * @return bool
     */
    public function toggleStatus($id, string $newStatus): bool;

    /**
     * @param LengthAwarePaginator $Rows
     * @param array $Options
     * @return void
     */
    public static function toSelect2(LengthAwarePaginator $Rows, ?array $Options = []): array;

    /**
     * @param Model $Row
     * @return array
     */
    public static function toSelect2Option($Row): array;

    /**
     * @param Model $Row
     * @return bool
     */
    public static function toResource($Row): object;

    /**
     * @param Collection $Rows
     * @return bool
     */
    public static function toCollectionResource(Collection $Rows): object;

    /**
     * @param string $action
     * @return string
     */
    public static function permissionNamespace(string $action): string;

    /**
     * @param string $action
     * @return bool
     */
    public static function can(string $action, $Row = null): bool;

    /**
     * @param string $action
     * @param Model|null $Row
     * @return bool|PermissionException
     */
    public static function canStrict(string $action, $Row = null): bool;

    /**
     * @param string $action
     * @param Model $Row
     * @return bool
     */
    public static function canByModel(string $action, $Row): bool;

    /**
     * @param string $action
     * @return bool
     */
    public static function throwPermissionException(string $action): void;

    /**
     * @param int|null $limit
     * @return Collection
     */
    public static function all(?int $limit = null);

    /**
     * @param void
     * @return void
     */
    public static function disableGuard(): void;

    /**
     * @param void
     * @return void
     */
    public static function enableGuard(): void;
}