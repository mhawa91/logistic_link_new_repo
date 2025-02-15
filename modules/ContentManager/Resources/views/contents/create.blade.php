@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => $LoadedType['create'],
        'items' => [
            [
                'title' => $LoadedType['menu'],
                'link'  => route('ContentController@index', ['type' => $type])
            ],
            [
                'title' => $LoadedType['create'],
                'link'  => route('ContentController@create', ['type' => $type])
            ]
        ],
    ]
])
@endcomponent

@section('content')

<div class="nk-block nk-block-lg" id="vue-form">
    <div class="card card-bordered">
        <div class="card-inner">

            @component('ui::components.form', [
                'options' => [
                    'id'                => null,
                    'action'            => route('ContentController@postCreate'),
                    'method'            => 'POST',
                    'class'             => '',
                    'attributes'        => [
                        'onsubmit'                     => 'BS.AJAX_FORM(event)',
                        'data-keep-loading-on-success' => 'true',
                    ],
                ],
            ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'type',
                        'value'       => $type,
                    ],
                ])

                @if ($LoadedType['inputs']['category'])
                    @include('ui::components.select', [
                        'options' => [
                            'id'                => 'category_id_select',
                            'jsSelect2'         => 'categorySelect2',
                            'view'              => 'INLINE',
                            'name'              => 'category_id',
                            'label'             => __('contentmanager::inputs.categories.label'),
                            'placeholder'       => __('contentmanager::inputs.categories.placeholder'),
                            'attributes'        => [],
                            'value'             => null,
                            'search'            => true,
                            'multiple'          => false,
                            'options'           => [],
                            'option_text'       => function($key, $option)                   { return $option['native']; },
                            'option_value'      => function($key, $option)                   { return $key; },
                            'option_attributes' => function($key, $option)                   { return []; },
                            'selected'          => function($options, $key, $option, $value) { return $key == $value; },
                            'disabled'          => function($options, $key, $option, $value) { return false; },
                        ],
                    ])
                @endif

                @if ($LoadedType['inputs']['tags'])
                    @include('ui::components.select', [
                        'options' => [
                            'id'                => 'tags_select',
                            'jsSelect2'         => 'tagsSelect2',
                            'view'              => 'INLINE',
                            'name'              => 'tags[]',
                            'label'             => __('contentmanager::inputs.tags.label'),
                            'placeholder'       => __('contentmanager::inputs.tags.placeholder'),
                            'attributes'        => [],
                            'value'             => null,
                            'search'            => true,
                            'multiple'          => true,
                            'options'           => [],
                            'option_text'       => function($key, $option)                   { return $option['native']; },
                            'option_value'      => function($key, $option)                   { return $key; },
                            'option_attributes' => function($key, $option)                   { return []; },
                            'selected'          => function($options, $key, $option, $value) { return $key == $value; },
                            'disabled'          => function($options, $key, $option, $value) { return false; },
                        ],
                    ])
                @endif

                @if ($LoadedType['inputs']['published_at'])
                    <div class="row g-3 align-center">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="published_at" class="form-label">{{ __('contentmanager::inputs.published_at.label') }}</label>
                            </div>
                        </div> 
                        <div class="col-lg-8">
                            <div class="form-group">
                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-right">
                                        <em class="icon ni ni-calendar-alt"></em>
                                    </div>
                                    <ui-datepicker id="published_at" name="published_at" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></ui-datepicker>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($LoadedType['inputs']['ordering'])
                    @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'number',
                            'step'        => '1',
                            'name'        => 'ordering',
                            'label'       => __('contentmanager::inputs.ordering.label'),
                            'placeholder' => __('contentmanager::inputs.ordering.placeholder'),
                            'description' => __('contentmanager::inputs.ordering.description'),
                            'value'       => 1,
                        ],
                    ])
                @endif

                @include('ui::components.radios', [
                    'options' => [
                        'view'              => 'INLINE',
                        'type'              => 'radio',
                        'name'              => 'sys_status',
                        'label'             => __('admin::inputs.status.label'),
                        'placeholder'       => __('admin::inputs.status.placeholder'),
                        'description'       => __('admin::inputs.status.description'),
                        'attributes'        => [],
                        'value'             => null,
                        'search'            => true,
                        'multiple'          => false,
                        'options'           => [
                            [
                                'id'       => 'ACTIVE',
                                'label'    => __('ui::inputs.status.options.ACTIVE'),
                                'selected' => true,
                            ],
                            [
                                'id'       => 'DISABLED',
                                'label'    => __('ui::inputs.status.options.DISABLED'),
                                'selected' => false,
                            ],
                        ],
                        'option_text'       => function($key, $option)                   { return $option['label']; },
                        'option_sub_text'   => function($key, $option)                   { return null; },
                        'option_value'      => function($key, $option)                   { return $option['id']; },
                        'option_attributes' => function($key, $option)                   { return []; },
                        'selected'          => function($options, $key, $option, $value) { return $option['selected']; },
                        'disabled'          => function($options, $key, $option, $value) { return false; },
                    ],
                ])

                <ul class="nav nav-tabs">
                    @foreach ($_ALL_LOCALES_ as $locale => $item)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#locale_{{ $locale }}">
                                <img src="{{ asset( config('ui.logos.' . $locale . '.flag.publicPath') ) }}" alt="" class="language-flag" style="border-radius: 30%;">
                                <span>{{ $item['native'] }}</span>
                            </a>
                        </li>
                    @endforeach                    
                </ul>
                <div class="tab-content">
                    @foreach ($_ALL_LOCALES_ as $locale => $item)
                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="locale_{{ $locale }}">

                            @if ($LoadedType['inputs']['image'] && ! $LoadedType['inputs']['seo_image'])
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'file_with_preview',
                                        'name'        => 'translations['. $locale .'][image]',
                                        'label'       => __('contentmanager::inputs.image.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => app('GraphManager')->defaultSrc('400x400', 'content'),
                                        'description' => __('contentmanager::inputs.image.description'),
                                        'value'       => null,
                                    ],
                                ])
                            @elseif (! $LoadedType['inputs']['image'] && $LoadedType['inputs']['seo_image'])
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'file_with_preview',
                                        'name'        => 'translations['. $locale .'][seo_image]',
                                        'label'       => __('contentmanager::inputs.seo_image.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => app('GraphManager')->defaultSrc('original', 'seo'),
                                        'description' => __('contentmanager::inputs.seo_image.description'),
                                        'value'       => null,
                                    ],
                                ])
                            @elseif ($LoadedType['inputs']['image'] && $LoadedType['inputs']['seo_image'])
                                <div class="row g-3 align-center">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="avatar" class="form-label">
                                                {{ __('contentmanager::inputs.images.label') }} <span class="text-primary"> [{{ $locale }}]</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-3">
                                                @include('ui::components.input', [
                                                    'options' => [
                                                        'view'        => 'DEFAULT',
                                                        'type'        => 'file_with_preview',
                                                        'name'        => 'translations['. $locale .'][image]',
                                                        'label'       => __('contentmanager::inputs.image.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                                        'placeholder' => app('GraphManager')->defaultSrc('400x400', 'content'),
                                                        'description' => __('contentmanager::inputs.image.description'),
                                                        'value'       => null,
                                                    ],
                                                ])
                                            </div>
                                            <div class="col-5">
                                                @include('ui::components.input', [
                                                    'options' => [
                                                        'view'        => 'DEFAULT',
                                                        'type'        => 'file_with_preview',
                                                        'name'        => 'translations['. $locale .'][seo_image]',
                                                        'label'       => __('contentmanager::inputs.seo_image.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                                        'placeholder' => app('GraphManager')->defaultSrc('original', 'seo'),
                                                        'description' => __('contentmanager::inputs.seo_image.description'),
                                                        'value'       => null,
                                                    ],
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($LoadedType['inputs']['icon'])
                                <br>
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'text',
                                        'name'        => 'translations['. $locale .'][icon]',
                                        'label'       => __('contentmanager::inputs.icon.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('contentmanager::inputs.icon.placeholder'),
                                        'description' => __('contentmanager::inputs.icon.description'),
                                    ],
                                ])
                            @endif

                            @if ($LoadedType['inputs']['url'])
                                <br>
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'text',
                                        'name'        => 'translations['. $locale .'][url]',
                                        'label'       => __('contentmanager::inputs.url.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('contentmanager::inputs.url.placeholder'),
                                        'description' => __('contentmanager::inputs.url.description'),
                                    ],
                                ])
                            @endif

                            @if ($LoadedType['inputs']['sub_title'])
                                <br>
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'text',
                                        'name'        => 'translations['. $locale .'][sub_title]',
                                        'label'       => __('contentmanager::inputs.sub_title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('contentmanager::inputs.sub_title.placeholder'),
                                        'description' => __('contentmanager::inputs.sub_title.description'),
                                    ],
                                ])
                            @endif

                            @if ($LoadedType['inputs']['title'])
                                <br>
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'text',
                                        'name'        => 'translations['. $locale .'][title]',
                                        'label'       => __('contentmanager::inputs.title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('contentmanager::inputs.title.placeholder'),
                                        'description' => __('contentmanager::inputs.title.description'),
                                    ],
                                ])
                            @endif

                            @if ($LoadedType['inputs']['short_content'])
                                <br>
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'textarea',
                                        'name'        => 'translations['. $locale .'][short_content]',
                                        'label'       => __('contentmanager::inputs.short_content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('contentmanager::inputs.short_content.placeholder'),
                                        'description' => __('contentmanager::inputs.short_content.description'),
                                    ],
                                ])
                            @endif

                            @if ($LoadedType['inputs']['long_content'])
                                @include('ui::components.editor', [
                                    'options' => [
                                        'id'          => 'editor-' . $locale,
                                        'rows'        => 30,
                                        'view'        => 'DEFAULT',
                                        'name'        => 'translations['. $locale .'][long_content]',
                                        'label'       => __('contentmanager::inputs.long_content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('contentmanager::inputs.long_content.placeholder'),
                                        'description' => __('contentmanager::inputs.long_content.description'),
                                    ],
                                ])
                            @endif


                            @if ($LoadedType['inputs']['seo'])
                                <hr>
                                <br>
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'text',
                                        'name'        => 'translations['. $locale .'][meta_alt_img]',
                                        'label'       => __('contentmanager::inputs.meta_alt_img.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('contentmanager::inputs.meta_alt_img.placeholder'),
                                        'description' => __('contentmanager::inputs.meta_alt_img.description'),
                                    ],
                                ])
                                <br>
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'text',
                                        'name'        => 'translations['. $locale .'][meta_title]',
                                        'label'       => __('contentmanager::inputs.meta_title.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('contentmanager::inputs.meta_title.placeholder'),
                                        'description' => __('contentmanager::inputs.meta_title.description'),
                                    ],
                                ])
                                <br>
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'textarea',
                                        'name'        => 'translations['. $locale .'][meta_description]',
                                        'label'       => __('contentmanager::inputs.meta_description.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('contentmanager::inputs.meta_description.placeholder'),
                                        'description' => __('contentmanager::inputs.meta_description.description'),
                                    ],
                                ])
                            @endif

                        </div>
                    @endforeach  
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-md btn-primary">
                        <span>{{ __('ui::strings.save') }}</span>
                    </button>
                </div>
            @endcomponent

        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        const CrudApp = new Vue({
            el: '#vue-form',
        });
        
    </script>
    @if ($LoadedType['inputs']['category'])
        <script>
            var jsSelect2 = $('#category_id_select').select2({
                ajax: {
                    delay: 250,
                    url: function (params) {
                        return '{{ route('CategoryController@select') }}';
                    },
                    data: function (params) {
                        var query = {
                            search      : params.term,
                            type        : '{{ $type }}',
                            page        : params.page || 1
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return data;
                    }
                }
            })
            .on("select2:select", function() {});
        </script>
    @endif
    @if ($LoadedType['inputs']['tags'])
        <script>
            var jsSelect2 = $('#tags_select').select2({
                tags: true,
                ajax: {
                    delay: 250,
                    url: function (params) {
                        return '{{ route('ContentController@tagSelect') }}';
                    },
                    data: function (params) {
                        var query = {
                            search      : params.term,
                            page        : params.page || 1
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return data;
                    }
                }
            })
            .on("select2:select", function() {});
        </script>
    @endif
@endpush