@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('systemcore::strings.countries.update'),
        'items' => [
            [
                'title' => __('systemcore::strings.countries.menu'),
                'link'  => route('CountriesController@index')
            ],
            [
                'title' => __('systemcore::strings.countries.update'),
                'link'  => route('CountriesController@update', ['model' => $model->id])
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
                    'action'            => route('CountriesController@postUpdate'),
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

                @component('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'sortname',
                        'label'       => 'ISO 2',
                        'placeholder' => 'ISO 2',
                        'description' => '',
                        'attributes'  => [],
                        'class'       => 'rtl-input',
                        'value'       => $model->sortname,
                    ],
                ])
                @endcomponent

                @component('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'code',
                        'label'       => __('systemcore::inputs.country_code.label'),
                        'placeholder' => __('systemcore::inputs.country_code.placeholder'),
                        'description' => __('systemcore::inputs.country_code.description'),
                        'attributes'  => [],
                        'class'       => 'rtl-input',
                        'value'       => $model->code,
                    ],
                ])
                @endcomponent

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
        });
    </script>
@endpush