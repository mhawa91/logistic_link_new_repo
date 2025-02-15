@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('admin::strings.users.profile'),
        'items' => [
            [
                'title' => __('admin::strings.users.profile'),
                'link'  => route('UsersController@profile')
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
                    'action'            => route('UsersController@postProfile'),
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
                        'value'       => $model->phone_number,
                    ],
                ]) --}}

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

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'password',
                        'name'        => 'current_password',
                        'label'       => __('admin::inputs.current_password.label'),
                        'placeholder' => __('admin::inputs.current_password.placeholder'),
                        'description' => __('admin::inputs.current_password.description'),
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'password',
                        'name'        => 'new_password',
                        'label'       => __('admin::inputs.new_password.label'),
                        'placeholder' => __('admin::inputs.new_password.placeholder'),
                        'description' => __('admin::inputs.new_password.description'),
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'password',
                        'name'        => 'new_password_confirmation',
                        'label'       => __('admin::inputs.new_password_confirmation.label'),
                        'placeholder' => __('admin::inputs.new_password_confirmation.placeholder'),
                        'description' => __('admin::inputs.new_password_confirmation.description'),
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
@endpush