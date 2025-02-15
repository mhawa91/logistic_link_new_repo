<?php

namespace Modules\SystemCore\Entities\Services;

use Exception;
use Validator;
use DataTables;
use Modules\SystemCore\Entities\SystemConfig;
use Modules\SystemCore\Entities\Contracts\SystemConfigInterface;
use Modules\SystemCore\System\Services\EloquentBaseService;

class SystemConfigService extends EloquentBaseService implements SystemConfigInterface
{
    /**
     * @var array
     */
    public static $config = [];

    /**
     * @var array
     */
    public static $values = [];

    /**
     * SystemConfigRepository constructor.
     *
     * @param SystemConfig $model
     */
    public function __construct(SystemConfig $model)
    {
        parent::__construct($model);
    }

    /**
     * @param void
     * @return Validator
     */
    public static function validator(array $attributes, string $action)
    {
        if ($action == 'create') {
            return Validator::make($attributes, [
                'config.*.key'                       => 'required',
                'config.*.val'                       => 'nullable',
            ]);
        }
        if ($action == 'update') {
            return Validator::make($attributes, [
                'config.*.key'                       => 'required',
                'config.*.val'                       => 'nullable',
            ]);
        }
        throw new Exception("Unsupported action [". $action ."]", 500);
    }

    /**
     * @param string|null $filter
     * @return void
     */
    public function search(?string $filter): self
    {
        if (empty($filter)) {
            return $this;
        }
        $this->model = $this->model->where(function($Model) use($filter) {});
        return $this;
    }

    /**
     * @param array|null $filter
     * @return self
     */
    public function advancedSearch(?array $filter): self
    {
        $this->model = $this->model->where(function($Model) use($filter) {});
        return $this;
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
     * @param SystemConfig $Row
     * @return array
     */
    public static function toSelect2Option($Row): array
    {
        return $Row ? [
            'id'   => $Row->id,
            'text' => $Row->x_key,
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
        return SystemConfig::class . ( $action ? '@' . $action : '' );
    }

    /**
     * @param string $action
     * @param SystemConfig $Row
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

    /**
     * @param array $attributes
     * @return SystemConfig
     */
    public function create(array $attributes): SystemConfig
    {
        $data = [
            'x_key'              => $attributes['key'],
            'x_value'            => $attributes['value'],
            'created_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'updated_by_user_id' => null,
            'sys_status'         => 'ACTIVE',
        ];
        return parent::create($data);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return SystemConfig
     */
    public function update($id, array $attributes): SystemConfig
    {
        $model = $this->withTrashed()->find($id);
        $data  = [
            'x_key'              => $attributes['key'],
            'x_value'            => $attributes['value'],
            'updated_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'sys_status'         => $model->sys_status,
        ];
        return parent::update($id, $data);
    }

    /**
     * @param int $id
     * @return SystemConfig
     */
    public function find($id):? SystemConfig
    {
        return parent::find($id);
    }

    /**
     * @param array
     * @return void
     */
    public static function register(array $SystemConfig): void
    {
        if (empty($SystemConfig['key'])) {
            throw new Exception("You must provide a key for SystemConfig registeration", 500);
        }
        $SystemConfig = array_merge([
            'ordering'  => 0,
            'key'       => null,
            'input'     => function($valueFromDB)      { return ''; },
            'update'    => function($oldValues, $newValueFromRequest) { return true; },
            'show'      => function()                  { return true; },
        ], $SystemConfig);
        static::$config[ $SystemConfig['key'] ] = $SystemConfig;
    }

    /**
     * @param void
     * @return array
     */
    public static function load(): array
    {
        static::$values = SystemConfig::get()->mapWithKeys(function($Row, $key) {
            return [$Row->x_key => $Row->x_value];
        })->toArray();
        return static::$values;
    }

    /**
     * @param string $Key
     * @param string $Default
     * @return any
     */
    public static function of(string $Key, $Default = null)
    {
        return empty(static::$values[ $Key ]) ? $Default : static::$values[ $Key ];
    }

    /**
     * @param void
     * @return array
     */
    public static function getConfigs(): array
    {
        return static::$config;
    }

    /**
     * @param string
     * @return array|null
     */
    public static function getConfig(string $Config) :? array
    {
        return isset(static::$config[ $Config ]) ? static::$config[ $Config ] : null;
    }

     /**
     * @param array
     * @return void
     */
    public static function save(array $Attributes) : void
    {
        foreach ($Attributes as $key => $item) {
            $oldValues = SystemConfig::where('x_key', $item['key'])->get();
            if ($registery = static::getConfig($item['key'])) {
                $old = $oldValues->mapWithKeys(function($Row, $key) {
                    return [
                        'key' => $Row->x_key,
                        'val' => $Row->x_value,
                    ];
                })->toArray();
                $registery['update']($old, $item);
            }
            $oldValues->map(function($oldValue) {
                $oldValue->forceDelete();
            });
        }
    }
}