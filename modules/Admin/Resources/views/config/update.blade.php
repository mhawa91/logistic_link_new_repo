@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('admin::strings.config.update'),
        'items' => [
            [
                'title' => __('admin::strings.config.menu'),
                'link'  => route('ConfigController@update')
            ],
            [
                'title' => __('admin::strings.config.update'),
                'link'  => route('ConfigController@update')
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
                    'action'            => route('ConfigController@postUpdate'),
                    'method'            => 'POST',
                    'class'             => '',
                    'attributes'        => [
                        'onsubmit'                     => 'BS.AJAX_FORM(event)',
                        'data-keep-loading-on-success' => 'false',
                    ],
                ],
            ])

                @foreach ($inputs as $item)
                    @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'hidden',
                            'name'        => 'config['. $item['key'] .'][key]',
                            'value'       => $item['key'],
                        ],
                    ])
                    {!! $item['input']( $service->of($item['key'], null) ) !!}
                @endforeach

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