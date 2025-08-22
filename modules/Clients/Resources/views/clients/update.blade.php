@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('clients::strings.clients.update'),
        'items' => [
            [
                'title' => __('clients::strings.clients.menu'),
                'link'  => route('ClientsController@index')
            ],
            [
                'title' => __('clients::strings.clients.update'),
                'link'  => route('ClientsController@update', ['model' => $model->id])
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
                    'action'            => route('ClientsController@postUpdate'),
                    'method'            => 'POST',
                    'class'             => '',
                    'attributes'        => [
                        'onsubmit'                     => 'BS.AJAX_FORM(event)',
                        'data-keep-loading-on-success' => 'false',
                    ],
                ],
            ])

                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#meta_data">
                            <em class="icon icon ni ni-file-docs"></em>
                            <span>{{ __('clients::strings.base_information') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#address_information">
                            <em class="icon icon ni ni-file-docs"></em>
                            <span>{{ __('clients::strings.address_information') }}</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="meta_data">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'hidden',
                                'name'        => 'model',
                                'value'       => $model->id,
                            ],
                        ])
        
                        <div class="row g-3 align-center">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="avatar" class="form-label">
                                        {{ __('clients::inputs.images.label') }}
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
                                                'name'        => 'avatar',
                                                'label'       => __('admin::inputs.avatar.label'),
                                                'description' => __('admin::inputs.avatar.description'),
                                                'placeholder' => app('GraphManager')->defaultSrc('300x400', 'user'),
                                                'value'       => app('GraphManager')->src('400x400', 'user', $model->avatar),
                                            ],
                                        ])
                                    </div>
                                    <div class="col-4">
                                        @include('ui::components.input', [
                                            'options' => [
                                                'view'        => 'DEFAULT',
                                                'type'        => 'file_with_preview',
                                                'name'        => 'cover',
                                                'label'       => __('clients::inputs.cover.label'),
                                                'description' => __('clients::inputs.cover.description'),
                                                'placeholder' => app('GraphManager')->defaultSrc('400x400', 'user'),
                                                'value'       => app('GraphManager')->src('400x400', 'user', $model->cover),
                                            ],
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                                            
                        @if (\Module::installedAndEnabled('Branches'))
                            @include('ui::components.select', [
                                'options' => [
                                    'id'                => 'user_branch_id_select',
                                    'jsSelect2'         => 'branchSelect2',
                                    'view'              => 'INLINE',
                                    'name'              => 'branch_id',
                                    'label'             => __('branches::inputs.user_branch_id.label'),
                                    'placeholder'       => __('branches::inputs.user_branch_id.placeholder'),
                                    'attributes'        => [],
                                    'value'             => $model->branch_id,
                                    'search'            => true,
                                    'multiple'          => false,
                                    'options'           => ! $model->Branch ? [] : [
                                        [
                                            'id'   => $model->Branch->id,
                                            'text' => $model->Branch->__('title', app()->getLocale()),
                                        ]
                                    ],
                                    'option_text'       => function($key, $option)                   { return $option['text']; },
                                    'option_value'      => function($key, $option)                   { return $option['id']; },
                                    'option_attributes' => function($key, $option)                   { return []; },
                                    'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                    'disabled'          => function($options, $key, $option, $value) { return false; },
                                ],
                            ])
                        @endif
        
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'text',
                                'name'        => 'name',
                                'label'       => __('admin::inputs.name.label'),
                                'placeholder' => __('admin::inputs.name.placeholder'),
                                'description' => __('admin::inputs.name.description'),
                                'value'       => $model->name,
                            ],
                        ])
        
                        {{-- @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'text',
                                'name'        => 'first_name',
                                'label'       => __('admin::inputs.first_name.label'),
                                'placeholder' => __('admin::inputs.first_name.placeholder'),
                                'description' => __('admin::inputs.first_name.description'),
                                'value'       => $model->first_name,
                            ],
                        ])
        
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'text',
                                'name'        => 'last_name',
                                'label'       => __('admin::inputs.last_name.label'),
                                'placeholder' => __('admin::inputs.last_name.placeholder'),
                                'description' => __('admin::inputs.last_name.description'),
                                'value'       => $model->last_name,
                            ],
                        ]) --}}
        
                        {{-- @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'text',
                                'name'        => 'username',
                                'label'       => __('admin::inputs.username.label'),
                                'placeholder' => __('admin::inputs.username.placeholder'),
                                'description' => __('admin::inputs.username.description'),
                                'value'       => $model->username,
                            ],
                        ]) --}}
        
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'text',
                                'name'        => 'email',
                                'label'       => __('admin::inputs.email.label'),
                                'placeholder' => __('admin::inputs.email.placeholder'),
                                'description' => __('admin::inputs.email.description'),
                                'value'       => $model->email,
                            ],
                        ])
        
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'text',
                                'name'        => 'phone_number',
                                'label'       => __('admin::inputs.phone_number.label'),
                                'placeholder' => __('admin::inputs.phone_number.placeholder'),
                                'description' => __('admin::inputs.phone_number.description'),
                                'attributes'  => ['dir' => 'ltr', 'style' => 'text-align: ' . ($_DIR_ == 'ltr' ? 'left' : 'right')],
                                'value'       => $model->phone_number,
                            ],
                        ])
        
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'password',
                                'name'        => 'password',
                                'label'       => __('admin::inputs.password.label'),
                                'placeholder' => __('admin::inputs.password.placeholder'),
                                'description' => __('admin::inputs.password.description'),
                            ],
                        ])
        
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'password',
                                'name'        => 'password_confirmation',
                                'label'       => __('admin::inputs.password_confirmation.label'),
                                'placeholder' => __('admin::inputs.password_confirmation.placeholder'),
                                'description' => __('admin::inputs.password_confirmation.description'),
                            ],
                        ])
        
                        @include('ui::components.select', [
                            'options' => [
                                'view'              => 'INLINE',
                                'name'              => 'locale',
                                'label'             => __('admin::inputs.locale.label'),
                                'placeholder'       => __('admin::inputs.locale.placeholder'),
                                'description'       => __('admin::inputs.locale.description'),
                                'attributes'        => [],
                                'value'             => $model->locale,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => $_ALL_LOCALES_,
                                'option_text'       => function($key, $option)                   { return $option['native']; },
                                'option_value'      => function($key, $option)                   { return $key; },
                                'option_attributes' => function($key, $option)                   { return []; },
                                'selected'          => function($options, $key, $option, $value) { return $key == $value; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])
        
                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'client_type_select',
                                'view'              => 'INLINE',
                                'name'              => 'type',
                                'label'             => __('admin::inputs.type.label'),
                                'placeholder'       => __('admin::inputs.type.placeholder'),
                                'description'       => __('admin::inputs.type.description'),
                                'attributes'        => [],
                                'value'             => $model->type,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => $Roles,
                                'option_text'       => function($key, $option)                   { return $option->__('title', app()->getLocale()); },
                                'option_value'      => function($key, $option)                   { return $option->name; },
                                'option_attributes' => function($key, $option)                   { return []; },
                                'selected'          => function($options, $key, $option, $value) { return $option->name == $value; },
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
                                'value'             => $model->sys_status,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => [
                                    [
                                        'id'       => 'ACTIVE',
                                        'label'    => __('ui::inputs.status.options.ACTIVE'),
                                        'selected' => $model->sys_status == 'ACTIVE',
                                    ],
                                    [
                                        'id'       => 'PENDING_REVIEW',
                                        'label'    => __('ui::inputs.status.options.PENDING_REVIEW'),
                                        'selected' => $model->sys_status == 'PENDING_REVIEW',
                                    ],
                                    [
                                        'id'       => 'SUSPENDED',
                                        'label'    => __('ui::inputs.status.options.SUSPENDED'),
                                        'selected' => $model->sys_status == 'SUSPENDED',
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
                    </div>
                    <div class="tab-pane" id="address_information">

                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'country_select',
                                'jsSelect2'         => 'countryJsSelect2',
                                'view'              => 'INLINE',
                                'name'              => 'country_id',
                                'label'             => __('systemcore::inputs.country.label'),
                                'placeholder'       => __('systemcore::inputs.country.placeholder'),
                                'attributes'        => [],
                                'value'             => $model->country ? $model->country->id : null,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => $model->country ? [
                                    [
                                        'id'   => $model->country->id,
                                        'text' => $model->country->{'name_' . (in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'ar')},
                                        'data' => $model->country->toArray(),
                                    ]
                                ] : [],
                                'option_text'       => function($key, $option)                   { return $option['text']; },
                                'option_value'      => function($key, $option)                   { return $option['id']; },
                                'option_attributes' => function($key, $option)                   { return []; },
                                'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])

                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'city_select',
                                'jsSelect2'         => 'cityJsSelect2',
                                'view'              => 'INLINE',
                                'name'              => 'city_id',
                                'label'             => __('systemcore::inputs.city.label'),
                                'placeholder'       => __('systemcore::inputs.city.placeholder'),
                                'attributes'        => [],
                                'value'             => $model->city ? $model->city->id : null,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => $model->city ? [
                                    [
                                        'id'   => $model->city->id,
                                        'text' => $model->city->{'name_' . (in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'ar')},
                                        'data' => $model->city->toArray(),
                                    ]
                                ] : [],
                                'option_text'       => function($key, $option)                   { return $option['text']; },
                                'option_value'      => function($key, $option)                   { return $option['id']; },
                                'option_attributes' => function($key, $option)                   { return []; },
                                'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                            ],
                        ])

                        {{-- @include('ui::components.input', [
                            'options' => [
                                'view'        => 'INLINE',
                                'type'        => 'textarea',
                                'name'        => 'address',
                                'label'       => __('clients::inputs.address.label'),
                                'placeholder' => __('clients::inputs.address.placeholder'),
                                'description' => __('clients::inputs.address.description'),
                                'value'       => $model->address
                            ],
                        ]) --}}
                    </div>
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
            el  : '#vue-form',
            data: {
                country      : {!! $model->country ? $model->country->toJson() : 'null' !!},
                city         : {!! $model->city    ? $model->city->toJson()    : 'null' !!},
                hide_company : eval('{{ $model->type == 'APP_COURSE_MAKER' }}'),
            }
        });

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

        var cityJsSelect2 = $('#city_select').select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('CitiesController@select') }}';
                },
                data: function (params) {
                    var query = {
                        nullable    : 'true',
                        search      : params.term,
                        page        : params.page || 1,
                        country_id  : CrudApp.country?.id || null
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

        var client_type_select2 = $('#client_type_select').select2()
        .on("select2:select", function() {
            let _data = $(this).select2('data')[0];
            let data  = _data?.id && _data?.id != 0 ? _data : null
            type      = data ? data.id : null;
            if (type == 'APP_COURSE_MAKER') {
                CrudApp.hide_company = true;
            } else {
                CrudApp.hide_company = false;
            }
        });
    </script>
    @if (\Module::installedAndEnabled('Branches'))
        <script>
            var jsSelect2 = $('#user_branch_id_select').select2({
                ajax: {
                    delay: 250,
                    url: function (params) {
                        return '{{ route('BranchesController@select') }}';
                    },
                    data: function (params) {
                        var query = {
                            search      : params.term,
                            type_in     : ['BRANCH'],
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