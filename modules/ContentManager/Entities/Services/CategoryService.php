<?php

namespace Modules\ContentManager\Entities\Services;

use Exception;
use Validator;
use DataTables;
use Modules\ContentManager\Entities\Content;
use Modules\ContentManager\Entities\Contracts\CategoryInterface;
use Modules\SystemCore\System\Services\EloquentBaseService;
use Illuminate\Support\Str;

class CategoryService extends EloquentBaseService implements CategoryInterface
{
    /**
     * @var array
     */
    public static $types = [];

    /**
     * ContentRepository constructor.
     *
     * @param Content $model
     */
    public function __construct(Content $model)
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
                'sys_status'                  => 'nullable|in:DISABLED,ACTIVE',
                'type'                        => 'required',
                'parent_id'                   => 'nullable',
                'ordering'                    => 'required|numeric',
                'published_at'                => 'required|date_format:Y-m-d',
                'translations.*.title'        => 'required',
                'translations.*.descriptions' => 'nullable|max:500',
            ]);
        }
        if ($action == 'update') {
            return Validator::make($attributes, [
                'sys_status'                  => 'nullable|in:DISABLED,ACTIVE',
                'type'                        => 'required',
                'parent_id'                   => 'nullable',
                'ordering'                    => 'required|numeric',
                'published_at'                => 'required|date_format:Y-m-d',
                'translations.*.title'        => 'required',
                'translations.*.descriptions' => 'nullable|max:500',
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
        $this->model = $this->model->where(function($Model) use($filter) {
            $Model->whereHas('translations', function($Rel) use($filter) {
                $Rel->where('title', 'like', "%{$filter}%");
            });
        });
        return $this;
    }

    /**
     * @param array|null $filter
     * @return self
     */
    public function advancedSearch(?array $filter): self
    {
        $this->model = $this->model->where(function($Model) use($filter) {
            if (! empty($filter['title'])) {
                $Model->whereHas('translations', function($Rel) use($filter) {
                    $Rel->where('title', 'like', "%{$filter['title']}%");
                });
            }
            if (! empty($filter['sys_status'])) {
                $Model->where('sys_status', $filter['sys_status']);
            }
        });
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
                    'action' => route('CategoryController@update', ['model' => $Row->id]),
                ];
            },
            'delete' => ! static::can('delete') ? null : function($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('CategoryController@postDelete', ['model' => $Row->id]),
                ];
            },
            'restore' => ! static::can('restore') ? null : function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('CategoryController@postRestore', ['model' => $Row->id]),
                ];
            },
            'permaDelete' => ! static::can('permaDelete') ? null : function($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('CategoryController@postPermaDelete', ['model' => $Row->id]),
                ];
            }
        ];
    }

    /**
     * @param Content $Row
     * @return array
     */
    public static function toSelect2Option($Row): array
    {
        return $Row ? [
            'id'   => $Row->id,
            'text' => $Row->__('title', app()->getLocale()),
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
        return Content::class . '\\Category' . ( $action ? '@' . $action : '' );
    }

    /**
     * @param string $action
     * @param Content $Row
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
     * @return Content
     */
    public function create(array $attributes): Content
    {
        $data = array_merge([
            'base_type'          => null,
            'type'               => null,
            'parent_id'          => null,
            'ordering'           => null,
            'published_at'       => null,
            'created_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'updated_by_user_id' => null,
            'sys_status'         => 'ACTIVE',
        ], $attributes);
        $data['base_type'] = 'CATEGORY';
        if (isset($attributes['translations'])) {
            foreach ($attributes['translations'] as $locale => $value) {
                if (! empty($attributes['translations'][ $locale ]['image'])) {
                    // $attributes['translations'][ $locale ]['image'] = $attributes['translations'][ $locale ]['image']->store('content', 'graph');
                    $attributes['translations'][ $locale ]['image'] = app('GraphManager')->process('content', $attributes['translations'][ $locale ]['image']);
                }
                $attributes['translations'][ $locale ]['slug'] = Str::slug($attributes['translations'][ $locale ]['title']);

                $check = Content::whereHas('translations', function($Rel) use($attributes, $locale) {
                    $Rel->where('slug', $attributes['translations'][ $locale ]['slug']);
                })->count();
                if ($check > 0) {
                    $attributes['translations'][ $locale ]['slug'] = $attributes['translations'][ $locale ]['slug'] . '-' . $check;
                }
            }
            $data = array_merge($data, $attributes['translations']);
            unset($data['translations']);
        }
        if (isset($data['remove_translations'])) {
            unset($data['remove_translations']);
        }
        if (isset($attributes['translations'])) {
            $data = array_merge($data, $attributes['translations']);
            unset($data['translations']);
        }
        return parent::create($data);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return Content
     */
    public function update($id, array $attributes): Content
    {
        $model = $this->withTrashed()->find($id);
        $data = array_merge([
            'base_type'          => $model->base_type,
            'type'               => $model->type,
            'parent_id'          => $model->parent_id,
            'ordering'           => $model->ordering,
            'published_at'       => $model->published_at,
            'updated_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'sys_status'         => $model->sys_status,
        ], $attributes);
        $data['base_type'] = $model->base_type;
        $data['parent_id'] = $data['parent_id'] == 0 ? null : $data['parent_id'];
        if (isset($attributes['translations'])) {
            $model->load('translations');
            foreach ($attributes['translations'] as $locale => $value) {
                if (! empty($attributes['translations'][ $locale ]['image'])) {
                    if (! empty( $oldImage = $model->__f('image', $locale) )) {
                        app()->make('GraphManager')->remove( $oldImage );
                    }
                    // $attributes['translations'][ $locale ]['image'] = $attributes['translations'][ $locale ]['image']->store('content', 'graph');
                    $attributes['translations'][ $locale ]['image'] = app('GraphManager')->process('content', $attributes['translations'][ $locale ]['image']);
                }
                $attributes['translations'][ $locale ]['slug'] = Str::slug($attributes['translations'][ $locale ]['title']);

                $check = Content::where('id', '!=', $model->id)->whereHas('translations', function($Rel) use($attributes, $locale) {
                    $Rel->where('slug', $attributes['translations'][ $locale ]['slug']);
                })->count();
                if ($check > 0) {
                    $attributes['translations'][ $locale ]['slug'] = $attributes['translations'][ $locale ]['slug'] . '-' . $check;
                }

            }
            $data = array_merge($data, $attributes['translations']);
            unset($data['translations']);
        }
        if (isset($attributes['remove_translations'])) {
            foreach ($attributes['remove_translations'] as $locale => $value) {
                if (! empty($attributes['remove_translations'][ $locale ]['image'])) {
                    if ($attributes['remove_translations'][ $locale ]['image'] == 'Y') {
                        if (! empty( $oldImage = $model->__f('image', $locale) )) {
                            app()->make('GraphManager')->remove( $oldImage );
                            $attributes['translations'][ $locale ]['image'] = null;
                        }
                    }
                }
            }
        }
        if (isset($attributes['translations'])) {
            $data = array_merge($data, $attributes['translations']);
            unset($data['translations']);
        }
        return parent::update($id, $data);
    }

    /**
     * @param int $id
     * @return Content
     */
    public function find($id):? Content
    {
        $this->model = $this->model->with('Parent');
        return parent::find($id);
    }

    /**
     * @param string $IDS
     * @return self
     */
    public function excludeIds(? string $IDS) : self
    {
        $this->model = $this->model->whereNotIn('id', explode(',', $IDS));
        return $this;
    }

    /**
     * @param string $Type
     * @return self
     */
    public function ofType(string $Type) : self
    {
        $this->model = $this->model->where(function($Model) use($Type) {
            $Model->where('base_type', 'CATEGORY');
            $Model->where('type', $Type);
        });
        return $this;
    }

    /**
     * @param array
     * @return void
     */
    public static function register(array $Content): void
    {
        if (empty($Content['type'])) {
            throw new Exception("You must provide a type for content registeration", 500);
        }
        $Content = array_merge([
            'ordering'  => 0,
            'base_type' => null,
            'type'      => null,
            'menu'      => null,
            'list'      => null,
            'create'    => null,
            'update'    => null,
            'show'      => function() { return true; },
        ], $Content);
        static::$types[ $Content['type'] ] = $Content;
    }

    /**
     * @param void
     * @return array
     */
    public static function getTypes(): array
    {
        return static::$types;
    }

    /**
     * @param string
     * @return array|null
     */
    public static function getType(string $Type) :? array
    {
        return isset(static::$types[ $Type ]) ? static::$types[ $Type ] : null;
    }

    /**
     * @param int $level
     * @return self
     */
    public function ofLevel(int $level) : self
    {
        if ($level == 0) {
            $this->model = $this->model->whereNull('parent_id');
            return $this;
        }
        $RelationKey = '';
        for ($i = 0; $i < $level ; $i++) { 
            $RelationKey .= empty($RelationKey) ? 'Parent' : '.Parent';
        }
        $this->model = $this->model->whereHas($RelationKey);
        return $this;
    }

    /**
     * @param string $type
     * @param int $limit
     * @param array|null $options
     * @return array
     */
    public function frontendList(string $type, int $limit = null, ?array $options = []): array
    {
        $Data = Content::where('base_type', 'CATEGORY')
        ->where('sys_status', 'ACTIVE')
        ->where('type', $type)
        ->with('translations', 'Categories.translations')
        ->orderBy('published_at', 'DESC')
        ->orderBy('ordering', 'ASC');
        return [
            'list'       => $limit ? $Data->paginate($limit) : $Data->get(),
        ];
    }

    /**
     * @param string $type
     * @return Content
     */
    public function frontendRead(string $id):? Content
    {
        return Content::where('base_type', 'CATEGORY')
        ->where('sys_status', 'ACTIVE')
        ->with('translations', 'Categories.translations')
        ->findOrFail($id);
    }

    /**
     * @param void
     * @return Collection
     */
    public function filterData()
    {
        return $this->model->where(function($QUERY) {
            $QUERY
            ->whereHas('ExperincedUsers', function($Rel) { $Rel->where('sys_status', 'ACTIVE'); })
            ->orWhereHas('WorkUsers', function($Rel) { $Rel->where('sys_status', 'ACTIVE'); });
        })
        ->withCount([
            'ExperincedUsers' => function($Rel) { $Rel->where('sys_status', 'ACTIVE'); },
            'WorkUsers'       => function($Rel) { $Rel->where('sys_status', 'ACTIVE'); },
        ])
        ->get();
    }

    public function hasProfileBlogs()
    {
        $this->model = $this->model->has('ProfileBlogs');
        return $this;
    }

    public function withCountProfileBlogs()
    {
        $this->model = $this->model->withCount('ProfileBlogs');
        return $this;
    }

    public function defaultOrder()
    {
        $this->model = $this->model->orderBy('published_at', 'DESC')
        ->orderBy('ordering', 'ASC');
        return $this;
    }
}