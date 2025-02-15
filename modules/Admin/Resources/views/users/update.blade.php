@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('admin::strings.users.update'),
        'items' => [
            [
                'title' => __('admin::strings.users.menu'),
                'link'  => route('UsersController@index')
            ],
            [
                'title' => __('admin::strings.users.update'),
                'link'  => route('UsersController@update', ['model' => $model->id])
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
                    'action'            => route('UsersController@postUpdate'),
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

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'file_with_preview',
                        'name'        => 'avatar',
                        'label'       => __('admin::inputs.avatar.label'),
                        'description' => __('admin::inputs.avatar.description'),
                        'placeholder' => app('GraphManager')->defaultSrc('400x400', 'user'),
                        'value'       => app('GraphManager')->src('400x400', 'user', $model->avatar),
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

                {{-- @include('ui::components.input', [
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

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'textarea',
                        'name'        => 'bio',
                        'label'       => __('admin::inputs.bio.label'),
                        'placeholder' => __('admin::inputs.bio.placeholder'),
                        'description' => __('admin::inputs.bio.description'),
                        'value'       => $model->bio
                    ],
                ])

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