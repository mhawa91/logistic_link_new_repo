@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contentmanager::strings.category.create'),
        'items' => [
            [
                'title' => __('contentmanager::strings.category.menu'),
                'link'  => route('CategoryController@index', ['type' => $type])
            ],
            [
                'title' => __('contentmanager::strings.category.create'),
                'link'  => route('CategoryController@create', ['type' => $type])
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
                    'action'            => route('CategoryController@postCreate'),
                    'method'            => 'POST',
                    'class'             => '',
                    'attributes'        => [
                        'onsubmit'                     => 'BS.AJAX_FORM(event)',
                        'data-keep-loading-on-success' => 'true',
                    ],
                ],
            ])

                @include('ui::components.select', [
                    'options' => [
                        'id'                => 'parent_id_select',
                        'jsSelect2'         => 'parentCategorySelect2',
                        'view'              => 'INLINE',
                        'name'              => 'parent_id',
                        'label'             => __('contentmanager::inputs.categories.label'),
                        'placeholder'       => __('contentmanager::inputs.categories.placeholder'),
                        'attributes'        => [],
                        'value'             => null,
                        'search'            => true,
                        'multiple'          => false,
                        'options'           => [],
                        'option_text'       => function($key, $option)                   { return $option['text']; },
                        'option_value'      => function($key, $option)                   { return $option['id']; },
                        'option_attributes' => function($key, $option)                   { return []; },
                        'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                        'disabled'          => function($options, $key, $option, $value) { return false; },
                    ],
                ])

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

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'number',
                        'step'        => 1,
                        'name'        => 'ordering',
                        'label'       => __('contentmanager::inputs.ordering.label'),
                        'placeholder' => __('contentmanager::inputs.ordering.placeholder'),
                        'description' => __('contentmanager::inputs.ordering.description'),
                        'value'       => 1,
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
                            
                            @include('ui::components.input', [
                                'options' => [
                                    'view'        => 'INLINE',
                                    'type'        => 'file_with_preview',
                                    'name'        => 'translations['. $locale .'][image]',
                                    'label'       => __('contentmanager::inputs.image.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                    'placeholder' => __('contentmanager::inputs.image.placeholder'),
                                    'description' => __('contentmanager::inputs.image.description'),
                                    'value'       => null,
                                ],
                            ])
                            
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

                            {{-- @include('ui::components.input', [
                                'options' => [
                                    'view'        => 'INLINE',
                                    'type'        => 'textarea',
                                    'name'        => 'translations['. $locale .'][long_content]',
                                    'label'       => __('contentmanager::inputs.long_content.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                    'placeholder' => __('contentmanager::inputs.long_content.placeholder'),
                                    'description' => __('contentmanager::inputs.long_content.description'),
                                ],
                            ]) --}}

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

    <script>
        var jsSelect2 = $('#parent_id_select').select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('CategoryController@select', ['type' => $type]) }}';
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
@endpush