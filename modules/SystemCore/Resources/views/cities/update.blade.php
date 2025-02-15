@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('systemcore::strings.cities.update'),
        'items' => [
            [
                'title' => __('systemcore::strings.cities.menu'),
                'link'  => route('CitiesController@index')
            ],
            [
                'title' => __('systemcore::strings.cities.update'),
                'link'  => route('CitiesController@update', ['model' => $model->id])
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
                    'action'            => route('CitiesController@postUpdate'),
                    'method'            => 'POST',
                    'class'             => '',
                    'attributes'        => [
                        'onsubmit'                     => 'BS.AJAX_FORM(event)',
                        'data-keep-loading-on-success' => 'false',
                    ],
                ],
            ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'model',
                        'value'       => $model->id,
                    ],
                ])

                @include('ui::components.select', [
                    'options' => [
                        'id'                => 'country_select',
                        'jsSelect2'         => 'countryJsSelect2',
                        'view'              => 'INLINE',
                        'name'              => 'country_id',
                        'label'             => __('systemcore::inputs.country.label'),
                        'placeholder'       => __('systemcore::inputs.country.placeholder'),
                        'attributes'        => [],
                        'value'             => $model->Country ? $model->Country->id : null,
                        'search'            => true,
                        'multiple'          => false,
                        'options'           => $model->Country ? [
                            [
                                'id'   => $model->Country->id,
                                'text' => $model->Country->{'name_' . (in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'ar')},
                                'data' => $model->Country->toArray(),
                            ]
                        ] : [],
                        'option_text'       => function($key, $option)                   { return $option['text']; },
                        'option_value'      => function($key, $option)                   { return $option['id']; },
                        'option_attributes' => function($key, $option)                   { return []; },
                        'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                        'disabled'          => function($options, $key, $option, $value) { return false; },
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
                                'id'       => 's_2',
                                'value'    => 'ACTIVE',
                                'label'    => __('ui::inputs.status.options.ACTIVE'),
                                'selected' => $model->sys_status == 'ACTIVE',
                            ],
                            [
                                'id'       => 's_3',
                                'value'    => 'DISABLED',
                                'label'    => __('ui::inputs.status.options.DISABLED'),
                                'selected' => $model->sys_status == 'DISABLED',
                            ],
                        ],
                        'option_id'         => function($key, $option)                   { return $option['id']; },
                        'option_text'       => function($key, $option)                   { return $option['label']; },
                        'option_sub_text'   => function($key, $option)                   { return null; },
                        'option_value'      => function($key, $option)                   { return $option['value']; },
                        'option_attributes' => function($key, $option)                   { return []; },
                        'selected'          => function($options, $key, $option, $value) { return $option['selected']; },
                        'disabled'          => function($options, $key, $option, $value) { return false; },
                    ],
                ])

                <ul class="nav nav-tabs">
                    @foreach ($_ALL_LOCALES_ as $locale => $item)
                        @if (in_array($locale, ['ar', 'en']))
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#locale_{{ $locale }}">
                                    <img src="{{ asset( config('ui.logos.' . $locale . '.flag.publicPath') ) }}" alt="" class="language-flag" style="border-radius: 30%;">
                                    <span>{{ $item['native'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach ($_ALL_LOCALES_ as $locale => $item)
                        @if (in_array($locale, ['ar', 'en']))
                            <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="locale_{{ $locale }}">
                                
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'INLINE',
                                        'type'        => 'text',
                                        'name'        => 'name_'. $locale,
                                        'label'       => __('systemcore::inputs.name_'. $locale .'.label') . '<span class="text-primary"> [' . $locale . ']</span>',
                                        'placeholder' => __('systemcore::inputs.name_'. $locale .'.placeholder'),
                                        'description' => __('systemcore::inputs.name_'. $locale .'.description'),
                                        'value'       => $model->{'name_' . $locale},
                                    ],
                                ])
                            </div>
                        @endif
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
            data: {
                country : {!! $model->Country ? $model->Country->toJson() : 'null' !!},
                city    : null,
            }
        });
    </script>
    <script>
        var countryJsSelect2 = $('#country_select').select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('CountriesController@select') }}';
                },
                data: function (params) {
                    var query = {
                        nullable : 'true',
                        search   : params.term,
                        page     : params.page || 1
                    }
                    return query;
                },
                processResults: function (data) {
                    return data;
                }
            }
        })
        .on("select2:select", function() {
            let _data = $(this).select2('data')[0];
            CrudApp.country = _data?.id ? _data : null;
        });
    </script>
@endpush