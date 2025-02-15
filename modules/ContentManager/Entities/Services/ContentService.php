<?php

namespace Modules\ContentManager\Entities\Services;

use Exception;
use Validator;
use DataTables;
use Modules\ContentManager\Entities\Tag;
use Modules\ContentManager\Entities\Content;
use Modules\ContentManager\Entities\Contracts\ContentInterface;
use Modules\SystemCore\System\Services\EloquentBaseService;
use Illuminate\Support\Str;

class ContentService extends EloquentBaseService implements ContentInterface
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
        $LoadedType = static::getType($attributes['type']);
        if ($action == 'create') {
            return Validator::make($attributes, [
                'sys_status'                   => 'nullable|in:DISABLED,ACTIVE',
                'type'                         => 'required',
                'parent_id'                    => 'nullable',
                'ordering'                     => ($LoadedType['inputs']['ordering']     ? 'required' : 'nullable') . '|numeric',
                'published_at'                 => ($LoadedType['inputs']['published_at'] ? 'required' : 'nullable') . '|date_format:Y-m-d',
                'translations.*.sub_title'     => 'nullable|max:191',
                'translations.*.title'         => ($LoadedType['inputs']['title']        ? 'required' : 'nullable') . '',
                'translations.*.short_content' => 'nullable',
                'translations.*.long_content'  => 'nullable',
                'translations.*.url'           => 'nullable|url',
                'translations.*.icon'          => 'nullable',

                'translations.*.meta_alt_img'     => 'nullable',
                'translations.*.meta_title'       => 'nullable',
                'translations.*.meta_description' => 'nullable',
            ]);
        }
        if ($action == 'update') {
            return Validator::make($attributes, [
                'sys_status'                  => 'nullable|in:DISABLED,ACTIVE',
                'type'                         => 'required',
                'parent_id'                    => 'nullable',
                'ordering'                     => ($LoadedType['inputs']['ordering']     ? 'required' : 'nullable') . '|numeric',
                'published_at'                 => ($LoadedType['inputs']['published_at'] ? 'required' : 'nullable') . '|date_format:Y-m-d',
                'translations.*.sub_title'     => 'nullable|max:191',
                'translations.*.title'         => ($LoadedType['inputs']['title']        ? 'required' : 'nullable') . '',
                'translations.*.short_content' => 'nullable',
                'translations.*.long_content'  => 'nullable',
                'translations.*.url'           => 'nullable|url',
                'translations.*.icon'          => 'nullable',

                'translations.*.meta_alt_img'     => 'nullable',
                'translations.*.meta_title'       => 'nullable',
                'translations.*.meta_description' => 'nullable',
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
                    'action' => route('ContentController@update', ['model' => $Row->id]),
                ];
            },
            'delete' => ! static::can('delete') ? null : function($Row) {
                return $Row->trashed() ? null : [
                    'label'  => __('ui::strings.delete'),
                    'action' => route('ContentController@postDelete', ['model' => $Row->id]),
                ];
            },
            'restore' => ! static::can('restore') ? null : function($Row) {
                return ! $Row->trashed() ? null : [
                    'label'  => __('ui::strings.restore'),
                    'action' => route('ContentController@postRestore', ['model' => $Row->id]),
                ];
            },
            'permaDelete' => ! static::can('permaDelete') ? null : function($Row) {
                return [
                    'label'  => __('ui::strings.perma_delete'),
                    'action' => route('ContentController@postPermaDelete', ['model' => $Row->id]),
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
            'text' => $Row->__('title'),
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
        return Content::class . ( $action ? '@' . $action : '' );
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
            'type'               => null,
            'parent_id'          => null,
            'ordering'           => null,
            'published_at'       => null,
            'created_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'updated_by_user_id' => null,
            'sys_status'         => 'ACTIVE',
        ], $attributes);
        $data['base_type'] = 'POST';
        if (isset($attributes['translations'])) {
            foreach ($attributes['translations'] as $locale => $value) {
                if (! empty($attributes['translations'][ $locale ]['image'])) {
                    // $attributes['translations'][ $locale ]['image'] = $attributes['translations'][ $locale ]['image']->store('content', 'graph');
                    $attributes['translations'][ $locale ]['image'] = app('GraphManager')->process('content', $attributes['translations'][ $locale ]['image']);
                }
                if (! empty($attributes['translations'][ $locale ]['seo_image'])) {
                    // $attributes['translations'][ $locale ]['seo_image'] = $attributes['translations'][ $locale ]['seo_image']->store('seo', 'graph');
                    $attributes['translations'][ $locale ]['seo_image'] = app('GraphManager')->process('seo', $attributes['translations'][ $locale ]['seo_image']);
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
        $Category = [];
        $Tags     = [];
        if (isset($data['category_id'])) {
            $Category = is_array($data['category_id']) ? $data['category_id'] : [ $data['category_id'] ];
            $Category = in_array(0, $Category) ? [] : $Category;
            unset($data['category_id']);
        }
        if (isset($data['tags'])) {
            $Tags = collect($data['tags'] ?? [])->map(function($Tag) {
                $T = Tag::searchStrict($Tag)->first() ?? Tag::create([
                    app()->getLocale() => [
                        'title' => $Tag
                    ]
                ]);
                return $T->id;
            })->toArray();
            unset($data['tags']);
        }
        $Model = parent::create($data);
        $Model->Categories()->sync($Category);
        $Model->Tags()->sync($Tags);
        return $Model;
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
            'type'               => $model->type,
            'parent_id'          => $model->parent_id,
            'ordering'           => $model->ordering,
            'published_at'       => $model->published_at,
            'updated_by_user_id' => auth()->check() ? auth()->user()->id : null,
            'sys_status'         => $model->sys_status,
        ], $attributes);
        $data['base_type'] = $model->base_type;
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
                if (! empty($attributes['translations'][ $locale ]['seo_image'])) {
                    if (! empty( $oldImage = $model->__f('seo_image', $locale) )) {
                        app()->make('GraphManager')->remove( $oldImage );
                    }
                    // $attributes['translations'][ $locale ]['seo_image'] = $attributes['translations'][ $locale ]['seo_image']->store('seo', 'graph');
                    $attributes['translations'][ $locale ]['seo_image'] = app('GraphManager')->process('seo', $attributes['translations'][ $locale ]['seo_image']);
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
                        if (! empty( $oldImage = $model->__f('seo_image', $locale) )) {
                            app()->make('GraphManager')->remove( $oldImage );
                            $attributes['translations'][ $locale ]['seo_image'] = null;
                        }
                    }
                }
            }
        }
        $Category = [];
        $Tags     = [];
        if (isset($data['category_id'])) {
            $Category = is_array($data['category_id']) ? $data['category_id'] : [ $data['category_id'] ];
            $Category = in_array(0, $Category) ? [] : $Category;
            unset($data['category_id']);
        }
        if (isset($data['tags'])) {
            $Tags = collect($data['tags'] ?? [])->map(function($Tag) {
                $T = Tag::searchStrict($Tag)->first() ?? Tag::create([
                    app()->getLocale() => [
                        'title' => $Tag
                    ]
                ]);
                return $T->id;
            })->toArray();
            unset($data['tags']);
        }
        $Model = parent::update($id, $data);
        $Model->Categories()->sync($Category);
        $Model->Tags()->sync($Tags);
        return $Model;
    }

    /**
     * @param string $Type
     * @return self
     */
    public function ofType(string $Type) : self
    {
        $this->model = $this->model->where(function($Model) use($Type) {
            $Model->where('base_type', 'POST');
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
        $Content['inputs'] = array_merge([
            'sub_title'     => false,
            'title'         => true,
            'short_content' => true,
            'long_content'  => true,
            'image'         => true,
            'seo_image'     => false,
            'url'           => false,
            'icon'          => false,
            'parent_id'     => false,
            'ordering'      => true,
            'published_at'  => true,
            'category'      => false,
            'tags'          => false,
            'seo'           => false,
        ], isset($Content['inputs']) ? $Content['inputs'] : []);
        $Content['inputs']['published_at'] = true;
        $Content = array_merge([
            'ordering'  => 0,
            'base_type' => null,
            'type'      => null,
            'menu'      => null,
            'list'      => null,
            'create'    => null,
            'update'    => null,
            'inputs'    => [],
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
     * @param string $type
     * @param int|null $limit
     * @return Collection
     */
    public function frontendSection(string $type, ?int $limit = null)
    {
        $Data = $limit ? Content::limit($limit) : Content::query();
        return $Data->where('base_type', 'POST')
        ->where('sys_status', 'ACTIVE')
        ->where('type', $type)
        ->with('translations', 'Categories.translations')
        ->orderBy('published_at', 'DESC')
        ->orderBy('ordering', 'ASC')
        ->get();
    }

    /**
     * @param string $type
     * @param int $limit
     * @param array|null $options
     * @return array
     */
    public function frontendList(string $type, int $limit = null, ?array $options = []): array
    {
        $Data = Content::where('base_type', 'POST')
        ->where('sys_status', 'ACTIVE')
        ->where('type', $type)
        ->with('translations', 'Categories.translations')
        ->orderBy('published_at', 'DESC')
        ->orderBy('ordering', 'ASC');
        if (! empty($options['categories'])) {
            $Data->whereHas('Categories', function($Rel) use($options) {
                $Rel->whereIn('id', $options['categories']);
            });
        }
        if (!empty($options['search'])) {
            $Data->where(function($Query) use($options) {
                $Query->whereHas('translations', function($Rel) use($options) {
                    $Rel->where(function($Query) use($options) {
                        $Query->where('title', 'like', "%{$options['search']}%");
                        $Query->orWhere('short_content', 'like', "%{$options['search']}%");
                    });
                });
                $Query->orWhereHas('Categories.translations', function($Rel) use($options) {
                    $Rel->where('title', 'like', "%{$options['search']}%");
                });
            });
        }
        $MostViews = Content::where('base_type', 'POST')
        ->where('sys_status', 'ACTIVE')
        ->where('type', $type)
        ->with('translations', 'Categories.translations')
        ->orderBy('views_count', 'DESC')
        ->orderBy('published_at', 'DESC')
        ->orderBy('ordering', 'ASC');
        if (! empty($options['categories'])) {
            $MostViews->whereHas('Categories', function($Rel) use($options) {
                $Rel->whereIn('id', $options['categories']);
            });
        }
        $Categories = Content::where('base_type', 'CATEGORY')->where('type', $type)->whereHas('SubContents', function($Rel) {
            $Rel->where('sys_status', 'ACTIVE');
        })
        ->withCount([
            'SubContents' => function($Rel) {
                $Rel->where('sys_status', 'ACTIVE');
            }
        ]);
        $Categories->orderBy('ordering', 'ASC')->orderBy('published_at', 'DESC');
        return [
            'list'       => $limit ? $Data->paginate($limit) : $Data->get(),
            'most_views' => $MostViews->limit(5)->get(),
            'categories' => $Categories->get(),
        ];
    }

    /**
     * @param string $id
     * @return Content
     */
    public function frontendRead(string $id):? Content
    {
        $Content = Content::where('base_type', 'POST')
        ->where('sys_status', 'ACTIVE')
        ->with('translations', 'Categories.translations')
        ->findOrFail($id);

        $Content->views_count = $Content->views_count + 1;
        $Content->save();

        return $Content;
    }

    /**
     * @param Content $Item
     * @return Collection
     */
    public function frontendRelated(Content $Item)
    {
        $Related = Content::where('base_type', 'POST')
        ->where('sys_status', 'ACTIVE')
        ->where('type', $Item->type)
        ->where('id', '!=', $Item->id)
        ->with('translations', 'Categories.translations')
        ->orderBy('views_count', 'DESC')
        ->orderBy('published_at', 'DESC')
        ->orderBy('ordering', 'ASC');
        if ($Item->Categories->isNotEmpty()) {
            $Related->whereHas('Categories', function($Rel) use($Item) {
                $Rel->whereIn('id', $Item->Categories->pluck('id')->toArray());
            });
        }
        return $Related->limit(9)->get();
    }
}