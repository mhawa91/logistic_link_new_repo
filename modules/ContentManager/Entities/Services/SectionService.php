<?php

namespace Modules\ContentManager\Entities\Services;

use Exception;
use Validator;
use DataTables;
use Modules\ContentManager\Entities\Section;
use Modules\ContentManager\Entities\SectionData;
use Modules\ContentManager\Entities\Contracts\SectionInterface;
use Modules\SystemCore\System\Services\EloquentBaseService;

class SectionService extends EloquentBaseService implements SectionInterface
{
    /**
     * @var array
     */
    public static $sections = [];

    /**
     * @var array
     */
    public static $loadedSections = [];

    /**
     * @var array
     */
    public static $loadedSectionsTranslated = [];

    /**
     * SectionRepository constructor.
     *
     * @param Section $model
     */
    public function __construct(Section $model)
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
                'section.*.tag'                       => 'required',
            ]);
        }
        if ($action == 'update') {
            return Validator::make($attributes, [
                'section.*.tag'                       => 'required',
            ]);
        }
        throw new Exception("Unsupported action [". $action ."]", 500);
    }

    /**
     * @param string $action
     * @return string
     */
    public static function permissionNamespace(?string $action): string
    {
        return Section::class . ( $action ? '@' . $action : '' );
    }

    /**
     * @param string $action
     * @param Section $Row
     * @return bool
     */
    public static function canByModel(string $action, $Row): bool
    {
        return false;
    }

    /**
     * @param array $attributes
     * @return Section
     */
    public function create(array $attributes): Section
    {
        $data = [
            'tag' => $attributes['key'],
        ];
        return parent::create($data);
    }

    /**
     * @param array
     * @return void
     */
    public static function register(array $Section): void
    {
        if (empty($Section['tag'])) {
            throw new Exception("You must provide a tag for section registeration", 500);
        }
        $Section = array_merge([
            'title'       => 'title',
            'description' => null,
            'tag'         => null,
            'form'        => function($locale, $valueFromDB) { return ''; },
            'update'      => function($oldValues, $newValueFromRequest) { return true; },
            'show'        => function() { return true; },
        ], $Section);
        static::$sections[ $Section['tag'] ] = $Section;
    }

    /**
     * @param void
     * @return array
     */
    public static function load(): array
    {
        static::$loadedSections = Section::with('Data.translations')->get()->mapWithKeys(function($Row, $key) {
            return [
                $Row->tag => $Row->Data->mapWithKeys(function($DataItem, $key) {
                    return [
                        $DataItem->x_key => $DataItem->__('x_value', app()->getLocale()),
                    ];
                })->toArray()
            ];
        })->toArray();
        return static::$loadedSections;
    }

    /**
     * @param void
     * @return array
     */
    public static function loadTranslated() : array
    {
        static::$loadedSectionsTranslated = Section::with('Data.translations')->get()->mapWithKeys(function($Row, $key) {
            return [
                $Row->tag => $Row->Data->mapWithKeys(function($DataItem, $key) {
                    return [
                        $DataItem->x_key => collect(\LaravelLocalization::getLocalesOrder())->mapWithKeys(function($localeData, $locale) use($DataItem) {
                            return [
                                $locale => $DataItem->__f('x_value', $locale)
                            ];
                        })->toArray()
                    ];
                })->toArray()
            ];
        })->toArray();
        return static::$loadedSectionsTranslated;
    }

    /**
     * @param string $Tag
     * @param any $Default
     * @return array
     */
    public static function of(string $Tag, $Default = null) :? array
    {
        return empty(static::$loadedSections[ $Tag ]) ? $Default : static::$loadedSections[ $Tag ];
    }

    /**
     * @param string $Tag
     * @param string $Key
     * @param any $Default
     * @return array
     */
    public static function byKey(string $Tag, string $Key, $Default = null) :? string
    {
        if ($Section = static::of($Tag)) {
            return empty($Section[$Key]) ? $Default : ($Section[$Key] ?? $Default);
        }
        return $Default;
    }

    /**
     * @param string $Tag
     * @param any $Default
     * @return array
     */
    public static function ofTranslated(string $Tag, $Default = null) :? array
    {
        if (empty(static::$loadedSectionsTranslated[ $Tag ])) {
            return $Default;
        }
        return static::$loadedSectionsTranslated[ $Tag ];
    }

    /**
     * @param void
     * @return array
     */
    public static function get(): array
    {
        return static::$sections;
    }
    
    /**
     * @param array
     * @return void
     */
    public static function save(array $GroupedSections) : void
    {
        $oldValuesModels = Section::with('Data.translations')->get();
        foreach ($GroupedSections as $tag => $dataByLocales) {
            if ($registery = static::$sections[ $tag ]) {
                $registery['update'](static::ofTranslated($tag), $dataByLocales);
            }
        }
        $oldValuesModels->map(function($oldValue) {
            $oldValue->delete();
        });
    }

    /**
     * @param string $tag
     * @param array $data
     * @return void
     */
    public function insert(string $tag, array $data): void
    {
        $Section      = new Section;
        $Section->tag = $tag;
        $Section->save();
        foreach ($data as $x_key => $locales) {
            $item = [
                'section_id' => $Section->id,
                'x_key'      => $x_key,
            ];
            foreach ($locales as $locale => $x_value) {
                $item[ $locale ] = [
                    'x_value' => $x_value,
                ];
            }
            SectionData::create($item);
        }
    }
}
