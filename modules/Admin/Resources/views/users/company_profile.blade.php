@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'xl',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('admin::strings.company.profile'),
        'items' => [
            [
                'title' => __('admin::strings.company.profile'),
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
                    'action'            => route('UsersController@postCompany'),
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

                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">{{ __('admin::strings.company.personal_info') }}</h4>
                        <p>{{ __('admin::strings.company.personal_info_desc') }}</p>
                    </div>
                </div>
                <hr class="preview-hr" style="margin-bottom: 0.5rem">

                <div class="row">
                    <div class="col-md-8">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'DEFAULT',
                                        'type'        => 'text',
                                        'name'        => 'company_name',
                                        'label'       => __('contenttypes::inputs.company_name.label'),
                                        'placeholder' => __('contenttypes::inputs.company_name.placeholder'),
                                        'description' => __('contenttypes::inputs.company_name.description'),
                                        'value'       => $model->company?->company_name,
                                    ],
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'DEFAULT',
                                        'type'        => 'text',
                                        'name'        => 'contact_name',
                                        'label'       => __('contenttypes::inputs.contact_name.label'),
                                        'placeholder' => __('contenttypes::inputs.contact_name.placeholder'),
                                        'description' => __('contenttypes::inputs.contact_name.description'),
                                        'value'       => $model->name,
                                    ],
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'DEFAULT',
                                        'type'        => 'text',
                                        'name'        => 'company_email',
                                        'label'       => __('contenttypes::inputs.company_email.label'),
                                        'placeholder' => __('contenttypes::inputs.company_email.placeholder'),
                                        'description' => __('contenttypes::inputs.company_email.description'),
                                        'value'       => $model->email,
                                    ],
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'DEFAULT',
                                        'type'        => 'text',
                                        'name'        => 'company_phone_number',
                                        'label'       => __('contenttypes::inputs.company_phone_number.label'),
                                        'placeholder' => __('contenttypes::inputs.company_phone_number.placeholder'),
                                        'description' => __('contenttypes::inputs.company_phone_number.description'),
                                        'value'       => $model->company?->company_phone_number ? '+' . $model->company?->company_phone_number_ext . $model->company?->company_phone_number : null,
                                        'attributes'  => ['dir' => 'ltr', 'style' => 'text-align: ' . ($_DIR_ == 'ltr' ? 'left' : 'right')],
                                    ],
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'file_with_preview',
                                'name'        => 'company_logo',
                                'label'       => __('contenttypes::inputs.company_logo.label'),
                                'description' => __('contenttypes::inputs.company_logo.description'),
                                'placeholder' => app('GraphManager')->defaultSrc('400x400', 'company'),
                                'value'       => app('GraphManager')->src('400x400', 'company', $model->company?->company_logo),
                            ],
                        ])
                    </div>
                </div>

                <div class="nk-block-head" style="margin-top: 20px">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">{{ __('admin::strings.company.company_info') }}</h4>
                        <p>{{ __('admin::strings.company.company_info_desc') }}</p>
                    </div>
                </div>
                <hr class="preview-hr" style="margin-bottom: 0.5rem">
                <div class="row gy-4">
                    <div class="col-md-4">
                        @include('ui::components.select', [
                            'options' => [
                                'view'              => 'DEFAULT',
                                'name'              => 'registration_type_category_id',
                                'label'             => __('contenttypes::inputs.registration_type_category_id.label'),
                                'placeholder'       => __('contenttypes::inputs.registration_type_category_id.placeholder'),
                                'description'       => __('contenttypes::inputs.registration_type_category_id.description'),
                                'attributes'        => [],
                                'value'             => $model->company?->registration_type_category_id,
                                'search'            => true,
                                'multiple'          => false,
                                'options'           => $registerationTypes,
                                'option_text'       => function($key, $option)                   { return $option->__('title'); },
                                'option_value'      => function($key, $option)                   { return $option->id; },
                                'option_attributes' => function($key, $option)                   { return []; },
                                'selected'          => function($options, $key, $option, $value) { return $option->id == $value; },
                                'disabled'          => function($options, $key, $option, $value) { return false; },
                                'nullable'          => true,
                                'nullable_value'    => '0',
                            ],
                        ])
                    </div>
                    <div class="col-md-4">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'file',
                                'name'        => 'registration_number',
                                'label'       => __('contenttypes::inputs.registration_number.label'),
                                'description' => __('contenttypes::inputs.registration_number.description'),
                                'placeholder' => app('GraphManager')->defaultSrc('400x400', 'files'),
                                'value'       => app('GraphManager')->src('400x400', 'files', $model->company?->registration_number),
                            ],
                        ])
                    </div>
                    <div class="col-md-4">
                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'file',
                                'name'        => 'tax_identification_number',
                                'label'       => __('contenttypes::inputs.tax_identification_number.label'),
                                'description' => __('contenttypes::inputs.tax_identification_number.description'),
                                'placeholder' => app('GraphManager')->defaultSrc('400x400', 'files'),
                                'value'       => app('GraphManager')->src('400x400', 'files', $model->company?->tax_identification_number),
                            ],
                        ])
                    </div>
                </div>

                <div class="nk-block-head" style="margin-top: 20px">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">{{ __('admin::strings.company.address') }}</h4>
                        <p>{{ __('admin::strings.company.address_desc') }}</p>
                    </div>
                </div>
                <hr class="preview-hr" style="margin-bottom: 0.5rem">

                <div class="row gy-4">
                    <div class="col-md-4">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                @include('ui::components.select', [
                                    'options' => [
                                        'id'                => 'country_select',
                                        'jsSelect2'         => 'countryJsSelect2',
                                        'view'              => 'DEFAULT',
                                        'name'              => 'country_id',
                                        'label'             => __('systemcore::inputs.country.label'),
                                        'placeholder'       => __('systemcore::inputs.country.placeholder'),
                                        'attributes'        => [],
                                        'value'             => $model->company?->address?->country ? $model->company?->address?->country->id : null,
                                        'search'            => true,
                                        'multiple'          => false,
                                        'options'           => $model->company?->address?->country ? [
                                            [
                                                'id'   => $model->company?->address?->country->id,
                                                'text' => $model->company?->address?->country->{'name_' . (in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'ar')},
                                                'data' => $model->company?->address?->country->toArray(),
                                            ]
                                        ] : [],
                                        'option_text'       => function($key, $option)                   { return $option['text']; },
                                        'option_value'      => function($key, $option)                   { return $option['id']; },
                                        'option_attributes' => function($key, $option)                   { return []; },
                                        'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                        'disabled'          => function($options, $key, $option, $value) { return false; },
                                    ],
                                ])
                            </div>
                            <div class="col-md-12">
                                @include('ui::components.select', [
                                    'options' => [
                                        'id'                => 'city_select',
                                        'jsSelect2'         => 'cityJsSelect2',
                                        'view'              => 'DEFAULT',
                                        'name'              => 'city_id',
                                        'label'             => __('systemcore::inputs.city.label'),
                                        'placeholder'       => __('systemcore::inputs.city.placeholder'),
                                        'attributes'        => [],
                                        'value'             => $model->company?->address?->city ? $model->company?->address?->city->id : null,
                                        'search'            => true,
                                        'multiple'          => false,
                                        'options'           => $model->company?->address?->city ? [
                                            [
                                                'id'   => $model->company?->address?->city->id,
                                                'text' => $model->company?->address?->city->{'name_' . (in_array(app()->getLocale(), ['ar', 'en']) ? app()->getLocale() : 'ar')},
                                                'data' => $model->company?->address?->city->toArray(),
                                            ]
                                        ] : [],
                                        'option_text'       => function($key, $option)                   { return $option['text']; },
                                        'option_value'      => function($key, $option)                   { return $option['id']; },
                                        'option_attributes' => function($key, $option)                   { return []; },
                                        'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                                        'disabled'          => function($options, $key, $option, $value) { return false; },
                                    ],
                                ])
                            </div>
                            <div class="col-md-12">
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'DEFAULT',
                                        'type'        => 'text',
                                        'name'        => 'street',
                                        'label'       => __('contenttypes::inputs.street.label'),
                                        'placeholder' => __('contenttypes::inputs.street.placeholder'),
                                        'description' => __('contenttypes::inputs.street.description'),
                                        'value'       => $model->company?->address?->street,
                                    ],
                                ])
                            </div>
                            <div class="col-md-12">
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'DEFAULT',
                                        'type'        => 'text',
                                        'name'        => 'lat_lng',
                                        'label'       => __('contenttypes::inputs.lat.label') . ' / ' . __('contenttypes::inputs.lng.label'),
                                        'placeholder' => __('contenttypes::inputs.lat.placeholder') . ' / ' . __('contenttypes::inputs.lng.placeholder'),
                                        'description' => __('contenttypes::inputs.lat.description') . ' / ' . __('contenttypes::inputs.lng.description'),
                                        'value'       => $model->company?->address?->lat && $model->company?->address?->lng ? $model->company?->address?->lat . ',' . $model->company?->address?->lng : null,
                                        'attributes'  => [
                                            'v-model' => 'latlng',
                                        ],
                                    ],
                                ])
                            </div>
                            <div class="col-md-12">
                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'DEFAULT',
                                        'type'        => 'textarea',
                                        'name'        => 'full_address',
                                        'label'       => __('contenttypes::inputs.full_address.label'),
                                        'placeholder' => __('contenttypes::inputs.full_address.placeholder'),
                                        'description' => __('contenttypes::inputs.full_address.description'),
                                        'value'       => $model->company?->address?->full_address,
                                    ],
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div dir="ltr" class="bs-map-lg" id="geo_map"></div>
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
            el: '#vue-form',
            data: {
                country : {!! $model->company?->address?->country ? $model->company?->address?->country->toJson() : 'null' !!},
                city    : {!! $model->company?->address?->city    ? $model->company?->address?->city->toJson()    : 'null' !!},
                lat      : Number('{{ $model->company?->address?->lat ?? config('address.map.lat') }}'),
                lng      : Number('{{ $model->company?->address?->lng ?? config('address.map.lng') }}'),
                latlng   : `${Number('{{ $model->company?->address?->lat ?? config('address.map.lat') }}')},${Number('{{ $model->company?->address?->lng ?? config('address.map.lng') }}')}`,
            },
            watch: {
                latlng: function(value) {
                    let _latlng = value.split(',');
                    if (_latlng.length != 2) {
                        return;
                    }
                    this.lat = _latlng[0];
                    this.lng = _latlng[1];
                    mapApp.methods.placeMarker(new google.maps.LatLng(this.lat, this.lng));
                }
            },
            methods: {
                mapReady: function() {},
                addressSearch: function() {
                    var vm = this;
                    vm.address_loading = true;
                    BS.BTN_TOGGLE_LOADING($('.search-address'));
                    BS.BTN_TOGGLE_LOADING($('[type=submit]'));
                    $('.search_icon').hide();
                    $.get('{!! route('GeoCoderController@search') !!}', {
                        address: vm.address_search,
                    }, function(data) {
                        BS.BTN_TOGGLE_LOADING($('.search-address'));
                        BS.BTN_TOGGLE_LOADING($('[type=submit]'));
                        $('.search_icon').show();
                        vm.address_loading = false;
                        if (! data.status) {
                            return;
                        }
                        if (data.result) {
                            vm.address = data.result;
                        }
                        if (vm.address.lat && vm.address.lng) {
                            mapApp.methods.focusMap(vm.address.lat, vm.address.lng);
                            vm.address.lat = null;
                            vm.address.lng = null;
                        }
                    });
                },
                addressReverse: function(lat, lng) {
                    var vm = this;
                    vm.address_loading = true;
                    vm.lat = lat;
                    vm.lng = lng;
                    BS.BTN_TOGGLE_LOADING($('.search-address'));
                    BS.BTN_TOGGLE_LOADING($('[type=submit]'));
                    $('.search_icon').hide();
                    $.get('{!! route('GeoCoderController@reverse') !!}', {
                        lat: lat,
                        lng: lng,
                    }, function(data) {
                        BS.BTN_TOGGLE_LOADING($('.search-address'));
                        BS.BTN_TOGGLE_LOADING($('[type=submit]'));
                        $('.search_icon').show();
                        vm.address_loading = false;
                        if (! data.status) {
                            return;
                        }
                        if (data.result) {
                            vm.address = data.result;
                        }
                    });
                },
            }
        });

        const mapApp = {
            el: 'geo_map',
            data: {
                lat      : Number('{{ $model->company?->address?->lat ?? config('address.map.lat') }}'),
                lng      : Number('{{ $model->company?->address?->lng ?? config('address.map.lng') }}'),
                zoom     : Number('{{ config('address.map.zoom') }}'),
                map      : null,
                marker   : null,
                selected : {!! ! $model->company?->address?->lat || ! $model->company?->address?->lng ? 'null' : json_encode([
                    'lat' => $model->company?->address?->lat,
                    'lng' => $model->company?->address?->lng,
                ]) !!},
                // selected : {
                //     lat: 25.225095151701264,
                //     lng: 55.27210069385604,
                // },
            },
            methods: {
                init: async function() {
                    const { Map } = await google.maps.importLibrary("maps");
                    mapApp.data.map = new Map(document.getElementById(mapApp.el), {
                        zoom  : mapApp.data.zoom,
                        center: {
                            lat : mapApp.data.lat,
                            lng : mapApp.data.lng,
                        },
                    });
                    mapApp.data.map.addListener("click", (e) => {
                        mapApp.methods.placeMarker(e.latLng, true);
                    });
                    if (mapApp.data.selected) {
                        mapApp.methods.placeMarker(new google.maps.LatLng(
                            mapApp.data.selected.lat,
                            mapApp.data.selected.lng
                        ));
                    }
                    CrudApp.mapReady();
                },
                placeMarker: function(coordinates, runReverse = false) {
                    mapApp.methods.clearMarker();
                    mapApp.data.lat  = coordinates.lat();
                    mapApp.data.lng  = coordinates.lng();
                    mapApp.data.zoom = 18.25;
                    mapApp.data.selected = {
                        lat: mapApp.data.lat,
                        lng: mapApp.data.lng,
                    };
                    mapApp.data.marker = new google.maps.Marker({
                        position: coordinates,
                        map     : mapApp.data.map,
                    });
                    mapApp.data.map.panTo(coordinates);
                    mapApp.data.map.setZoom(mapApp.data.zoom);
                    if (runReverse) {
                        CrudApp.addressReverse(mapApp.data.lat, mapApp.data.lng);
                        CrudApp.latlng = `${mapApp.data.lat},${mapApp.data.lng}`;
                    }
                },
                clearMarker: function() {
                    if (mapApp.data.marker) {
                        mapApp.data.marker.setMap(null);
                        mapApp.data.marker = null;
                    }
                },
                focusMap: function(lat, lng) {
                    mapApp.data.zoom = 15;
                    mapApp.data.map.panTo(new google.maps.LatLng(lat, lng));
                    mapApp.data.map.setZoom(mapApp.data.zoom);
                }
            },
        };

        $(document).ready(function() {
            $(window).keydown(function(e) {
                if(e.keyCode == 13) {
                    e.preventDefault();
                    if ($("#address_search").is(":focus")) {
                        CrudApp.addressSearch();
                    }
                    return false;
                }
            });
            mapApp.methods.init();
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
    </script>
@endpush