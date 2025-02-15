@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('admin::strings.users.create'),
        'items' => [
            [
                'title' => __('admin::strings.users.menu'),
                'link'  => route('UsersController@index')
            ],
            [
                'title' => __('admin::strings.users.create'),
                'link'  => route('UsersController@create')
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
                    'action'            => route('UsersController@postCreate'),
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
                        'type'        => 'file_with_preview',
                        'name'        => 'avatar',
                        'label'       => __('admin::inputs.avatar.label'),
                        'placeholder' => __('admin::inputs.avatar.placeholder'),
                        'description' => __('admin::inputs.avatar.description'),
                        'value'       => null,
                    ],
                ])

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

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'name',
                        'label'       => __('admin::inputs.name.label'),
                        'placeholder' => __('admin::inputs.name.placeholder'),
                        'description' => __('admin::inputs.name.description'),
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
                    ],
                ])

                {{-- @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'text',
                        'name'        => 'phone_number',
                        'label'       => __('admin::inputs.phone_number.label'),
                        'placeholder' => __('admin::inputs.phone_number.placeholder'),
                        'description' => __('admin::inputs.phone_number.description'),
                        'attributes'  => ['dir' => 'ltr', 'style' => 'text-align: ' . ($_DIR_ == 'ltr' ? 'left' : 'right')],
                    ],
                ]) --}}

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
                        'value'             => null,
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
                        'view'              => 'INLINE',
                        'name'              => 'type',
                        'label'             => __('admin::inputs.type.label'),
                        'placeholder'       => __('admin::inputs.type.placeholder'),
                        'description'       => __('admin::inputs.type.description'),
                        'attributes'        => [],
                        'value'             => 'DEFAULT',
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
                                'id'       => 'PENDING_REVIEW',
                                'label'    => __('ui::inputs.status.options.PENDING_REVIEW'),
                                'selected' => false,
                            ],
                            [
                                'id'       => 'SUSPENDED',
                                'label'    => __('ui::inputs.status.options.SUSPENDED'),
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

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'textarea',
                        'name'        => 'bio',
                        'label'       => __('admin::inputs.bio.label'),
                        'placeholder' => __('admin::inputs.bio.placeholder'),
                        'description' => __('admin::inputs.bio.description'),
                    ],
                ])

                {{-- <div class="row g-3 align-center">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label" for="site-off">Maintanance Mode</label>
                            <span class="form-note">Enable to make website make offline.</span>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="reg-public" id="site-off">
                                <label class="custom-control-label" for="site-off">Offline</label>
                            </div>
                        </div>
                    </div>
                </div> --}}

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