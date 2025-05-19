@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'xlg',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.contracts.create'),
        'items' => [
            [
                'title' => __('contenttypes::strings.contracts.menu'),
                'link'  => route('TransportersController@index')
            ],
            [
                'title' => __('contenttypes::strings.contracts.create'),
                'link'  => route('TransportersController@create')
            ]
        ],
    ]
])
@endcomponent

@section('content')

@section('content')
    @component('ui::components.form', [
        'options' => [
            'id'                => null,
            'action'            => route('TransportersController@postCreate'),
            'method'            => 'POST',
            'class'             => '',
            'attributes'        => [
                'onsubmit'                     => 'BS.AJAX_FORM(event)',
                'data-keep-loading-on-success' => 'true',
            ],
        ],
    ])
    <div class="row" id="vue-form">
        <div class="col-md-4">
            <div class="nk-block nk-block-lg">
                <div class="card card-bordered">
                    <div class="card-inner">

                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'name',
                                'label'       => __('contenttypes::inputs.transportar_name.label'),
                                'placeholder' => __('contenttypes::inputs.transportar_name.placeholder'),
                                'description' => __('contenttypes::inputs.transportar_name.description'),
                            ],
                        ])

                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'email',
                                'label'       => __('contenttypes::inputs.transportar_email.label'),
                                'placeholder' => __('contenttypes::inputs.transportar_email.placeholder'),
                                'description' => __('contenttypes::inputs.transportar_email.description'),
                            ],
                        ])

                        @component('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'text',
                                'name'        => 'phone_number',
                                'label'       => __('contenttypes::inputs.transportar_phone_number.label'),
                                'placeholder' => __('contenttypes::inputs.transportar_phone_number.placeholder'),
                                'description' => __('contenttypes::inputs.transportar_phone_number.description'),
                                'attributes'  => ['dir' => 'ltr', 'style' => 'text-align: ' . ($_DIR_ == 'ltr' ? 'left' : 'right')],
                            ],
                        ])
                            <x-slot name="after">
                                <div class="form-text-hint">
                                    <strong class="overline-title" dir="ltr">+966</strong>
                                </div>
                            </x-slot>
                        @endcomponent

                        <hr class="preview-hr">

                        @component('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'daterange',
                                'name'        => 'contract_daterange',
                                'label'       => __('contenttypes::inputs.start_end_date.label'),
                                'placeholder' => __('contenttypes::inputs.start_end_date.placeholder'),
                                'description' => __('contenttypes::inputs.start_end_date.description'),
                                'attributes'  => []
                            ],
                        ])
                        @endcomponent
                    
                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'payment_term_id_select',
                                'jsSelect2'         => 'payment_term_id_jsSelect2',
                                'view'              => 'DEFAULT',
                                'name'              => 'payment_term_id',
                                'label'             => __('contenttypes::inputs.payment_term_id.label'),
                                'placeholder'       => __('contenttypes::inputs.payment_term_id.placeholder'),
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

                        @component('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'number',
                                'name'        => 'insurance_value_limit',
                                'label'       => __('contenttypes::inputs.insurance_value_limit.label'),
                                'placeholder' => __('contenttypes::inputs.insurance_value_limit.placeholder'),
                                'description' => __('contenttypes::inputs.insurance_value_limit.description'),
                                'attributes'  => [
                                    'v-model' => 'insurance_value_limit',
                                ]
                            ],
                        ])
                            <x-slot name="after">
                                <div class="form-text-hint">
                                    <small class="overline-title">SAR</small>
                                </div>
                            </x-slot>
                        @endcomponent

                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'trucks_type_id_select',
                                'jsSelect2'         => 'trucks_type_id_jsSelect2',
                                'view'              => 'DEFAULT',
                                'name'              => 'trucks_type_id[]',
                                'label'             => __('contenttypes::inputs.trucks_type_id.label'),
                                'placeholder'       => __('contenttypes::inputs.trucks_type_id.placeholder'),
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

                        @include('ui::components.select', [
                            'options' => [
                                'id'                => 'commodity_id_select',
                                'jsSelect2'         => 'commodity_id_jsSelect2',
                                'view'              => 'DEFAULT',
                                'name'              => 'commodity_id[]',
                                'label'             => __('contenttypes::inputs.commodity_id.label'),
                                'placeholder'       => __('contenttypes::inputs.commodity_id.placeholder'),
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

                        <hr class="preview-hr">

                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'file',
                                'name'        => 'TRANSPORTER_TAX_CERTIFICATE',
                                'label'       => __('contenttypes::inputs.contract_tax_certificate.label'),
                                'description' => __('contenttypes::inputs.contract_tax_certificate.description'),
                                'placeholder' => null,
                                'value'       => null,
                            ],
                        ])

                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'file',
                                'name'        => 'TRANSPORTER_REGISTAION_LICENCE',
                                'label'       => __('contenttypes::inputs.contract_registration_license.label'),
                                'description' => __('contenttypes::inputs.contract_registration_license.description'),
                                'placeholder' => null,
                                'value'       => null,
                            ],
                        ])

                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'file',
                                'name'        => 'TRANSPORTER_INSURANCE_CERTIFICATE',
                                'label'       => __('contenttypes::inputs.contract_insurance_certificate.label'),
                                'description' => __('contenttypes::inputs.contract_insurance_certificate.description'),
                                'placeholder' => null,
                                'value'       => null,
                            ],
                        ])

                        @include('ui::components.input', [
                            'options' => [
                                'view'        => 'DEFAULT',
                                'type'        => 'file',
                                'name'        => 'TRANSPORTER_TRANSPORTATION_LICENCE',
                                'label'       => __('contenttypes::inputs.contract_transportation_license.label'),
                                'description' => __('contenttypes::inputs.contract_transportation_license.description'),
                                'placeholder' => null,
                                'value'       => null,
                            ],
                        ])
        
                        @include('ui::components.radios', [
                            'options' => [
                                'view'              => 'DEFAULT',
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
                                        'selected' => true,
                                    ],
                                    [
                                        'id'       => 's_3',
                                        'value'    => 'DISABLED',
                                        'label'    => __('ui::inputs.status.options.DISABLED'),
                                        'selected' => false,
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

                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-md btn-primary">
                                <span>{{ __('ui::strings.save') }}</span>
                            </button>
                        </div>
                        
            
                    </div>
                </div>        
            </div>
        </div>
        <div class="col-md-8">
            <div class="row gy-4">
                <div class="col-md-12">
                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered" style="padding-top: 0 !important; padding-bottom: 0 !important;">
                            <div class="table-responsive">
                                <table class="table table-tranx">
                                    <thead>
                                        <tr class="tb-tnx-head responsive-tr">
                                            <th>
                                                <span>{{ __('contenttypes::inputs.truck_type_id.label') }}</span>
                                            </th>
                                            <th>
                                                <span>{{ __('contenttypes::inputs.loading_type.label') }}</span>
                                            </th>
                                            <th>
                                                <span>{{ __('contenttypes::inputs.from_city_id.label') }}</span>
                                            </th>
                                            <th>
                                                <span>{{ __('contenttypes::inputs.to_city_id.label') }}</span>
                                            </th>
                                            <th>
                                                <span>{{ __('contenttypes::inputs.price.label') }}</span>
                                            </th>
                                            <th>
                                                <span>{{ __('contenttypes::inputs.pre_additional_location_price.label') }}</span>
                                            </th>
                                            <th class="tb-tnx-action" style="width: 1%">
                                                <span>&nbsp;</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="is_loading">
                                            <tr class="responsive-tr">
                                                <td colspan="7">
                                                    <div class="text-center m-5">
                                                        <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-if="! is_loading && items.length == 0">
                                            <tr class="responsive-tr">
                                                <td colspan="7">
                                                    <div class="text-center m-5">
                                                        {{-- <em class="icon ni ni-list-index text-primary" style="font-size: 120px;"></em> --}}
                                                        <em class="icon ni ni-file-docs text-primary" style="font-size: 120px;"></em>
                                                        <div class="nk-block nk-block-lg">
                                                            <div class="nk-block-head">
                                                                <div class="nk-block-head-content mt-4">
                                                                    <h4 class="nk-block-title">{{ __('contenttypes::strings.contract_items.no_packages') }}</h4>
                                                                    <p>{{ __('contenttypes::strings.contract_items.no_packages_desc') }}</p>
                                                                    <button type="button" class="btn btn-md btn-primary" data-toggle="modal" data-target="#itemsModal">
                                                                        <em class="icon ni ni-plus"></em>
                                                                        <span>{{ __('contenttypes::strings.contract_items.add_new') }}</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-if="!is_loading && items.length != 0">
                                            <tr class="responsive-tr" v-for="(item, item_index) in items">
                                                <td style="vertical-align: middle;">
                                                    @{{ item.truck_type?.title }}
                                                    @component('ui::components.input', [
                                                        'options' => [
                                                            'type'        => 'hidden',
                                                            'name'        => '--',
                                                            'attributes'  => [
                                                                ':name'   => "'items[' + item_index + '][id]'",
                                                                ':value'  => 'item.id',
                                                            ],
                                                        ],
                                                    ])
                                                    @endcomponent
                                                    @component('ui::components.input', [
                                                        'options' => [
                                                            'type'        => 'hidden',
                                                            'name'        => '--',
                                                            'attributes'  => [
                                                                ':name'   => "'items[' + item_index + '][truck_type_id]'",
                                                                ':value'  => 'item.truck_type?.id || null',
                                                            ],
                                                        ],
                                                    ])
                                                    @endcomponent
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    @{{ loadingTypes[ item.loading_type ] }}
                                                    @component('ui::components.input', [
                                                        'options' => [
                                                            'type'        => 'hidden',
                                                            'name'        => '--',
                                                            'attributes'  => [
                                                                ':name'   => "'items[' + item_index + '][loading_type_id]'",
                                                                ':value'  => 'item.loading_type',
                                                            ],
                                                        ],
                                                    ])
                                                    @endcomponent
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    @{{ item.from_city.name_ar }}<br>
                                                    <small class="text-primary">
                                                        @{{ item.from_city?.country.name_ar }}
                                                    </small>
                                                    @component('ui::components.input', [
                                                        'options' => [
                                                            'type'        => 'hidden',
                                                            'name'        => '--',
                                                            'attributes'  => [
                                                                ':name'   => "'items[' + item_index + '][from_city_id]'",
                                                                ':value'  => 'item.from_city?.id',
                                                            ],
                                                        ],
                                                    ])
                                                    @endcomponent
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    @{{ item.to_city.name_ar }}<br>
                                                    <small class="text-primary">
                                                        @{{ item.to_city?.country.name_ar }}
                                                    </small>
                                                    @component('ui::components.input', [
                                                        'options' => [
                                                            'type'        => 'hidden',
                                                            'name'        => '--',
                                                            'attributes'  => [
                                                                ':name'   => "'items[' + item_index + '][to_city_id]'",
                                                                ':value'  => 'item.to_city?.id',
                                                            ],
                                                        ],
                                                    ])
                                                    @endcomponent
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    @component('ui::components.input', [
                                                        'options' => [
                                                            'view'        => 'TABLE',
                                                            'type'        => 'number',
                                                            'name'        => 'price',
                                                            'label'       => __('contenttypes::inputs.price.label'),
                                                            'placeholder' => __('contenttypes::inputs.price.placeholder'),
                                                            'description' => __('contenttypes::inputs.price.description'),
                                                            'attributes'  => [
                                                                ':name'   => "'items[' + item_index + '][price]'",
                                                                'v-model' => 'item.price',
                                                            ]
                                                        ],
                                                    ])
                                                        <x-slot name="after">
                                                            <div class="form-text-hint">
                                                                <small class="overline-title">SAR</small>
                                                            </div>
                                                        </x-slot>
                                                    @endcomponent
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <template v-if="item.loading_type != 'CITY_TO_CITY_MULTIPLE_LOCATIONS'">
                                                        <small class="text-danger">{{ __('contenttypes::inputs.pre_additional_location_price.unsupported') }}</small>
                                                    </template>
                                                    <template v-if="item.loading_type == 'CITY_TO_CITY_MULTIPLE_LOCATIONS'">
                                                        @component('ui::components.input', [
                                                            'options' => [
                                                                'view'        => 'TABLE',
                                                                'type'        => 'number',
                                                                'name'        => 'pre_additional_location_price',
                                                                'label'       => __('contenttypes::inputs.pre_additional_location_price.label'),
                                                                'placeholder' => __('contenttypes::inputs.pre_additional_location_price.placeholder'),
                                                                'description' => __('contenttypes::inputs.pre_additional_location_price.description'),
                                                                'attributes'  => [
                                                                    ':name'   => "'items[' + item_index + '][pre_additional_location_price]'",
                                                                    'v-model'   => 'item.pre_additional_location_price',
                                                                    ':disabled' => "item.loading_type != 'CITY_TO_CITY_MULTIPLE_LOCATIONS'",
                                                                ]
                                                            ],
                                                        ])
                                                            <x-slot name="after">
                                                                <div class="form-text-hint">
                                                                    <small class="overline-title">SAR</small>
                                                                </div>
                                                            </x-slot>
                                                        @endcomponent
                                                    </template>
                                                    
                                                </td>
                                                <td class="tb-tnx-action" style="vertical-align: middle;">
                                                    <button type="button" class="text-soft btn btn-icon btn-trigger" v-on:click="removeItem(item_index)">
                                                        <em class="text-danger icon ni ni-trash"></em>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <template v-if="!is_loading && items.length != 0">
                                        <tfoot>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="tb-tnx-action">
                                                    <a href="javascript:;" class="text-soft btn btn-icon" data-toggle="modal" data-target="#itemsModal">
                                                        <em class="text-primary icon ni ni-plus"></em>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </template>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                @include('ui::components.input', [
                                    'options' => [
                                        'type'        => 'textarea',
                                        'id'          => 'editor-conditions-and-terms',
                                        'rows'        => 5,
                                        'view'        => 'DEFAULT',
                                        'name'        => 'TERMS_AND_CONDITIONS',
                                        'label'       => __('contenttypes::inputs.conditions-and-terms.label'),
                                        'placeholder' => __('contenttypes::inputs.conditions-and-terms.placeholder'),
                                        'description' => __('contenttypes::inputs.conditions-and-terms.description'),
                                    ],
                                ])

                                <hr class="preview-hr">

                                @include('ui::components.input', [
                                    'options' => [
                                        'type'        => 'textarea',
                                        'id'          => 'editor-insurance-details',
                                        'rows'        => 5,
                                        'view'        => 'DEFAULT',
                                        'name'        => 'INSURANCE_DETAILS',
                                        'label'       => __('contenttypes::inputs.insurance-details.label'),
                                        'placeholder' => __('contenttypes::inputs.insurance-details.placeholder'),
                                        'description' => __('contenttypes::inputs.insurance-details.description'),
                                    ],
                                ])

                                <hr class="preview-hr">

                                @include('ui::components.input', [
                                    'options' => [
                                        'type'        => 'textarea',
                                        'id'          => 'editor-penalties',
                                        'rows'        => 5,
                                        'view'        => 'DEFAULT',
                                        'name'        => 'PENALITIES',
                                        'label'       => __('contenttypes::inputs.penalties.label'),
                                        'placeholder' => __('contenttypes::inputs.penalties.placeholder'),
                                        'description' => __('contenttypes::inputs.penalties.description'),
                                    ],
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        @component('ui::components.modal', [
            'options' => [
                'id'        => 'itemsModal',
                'title'     => __('contenttypes::strings.contract_items.header'),
                'size'      => '',
                'fit'       => false,
            ]
        ])

            @include('ui::components.select', [
                'options' => [
                    'id'                => 'truck_type_id_select',
                    'jsSelect2'         => 'truck_type_id_jsSelect2',
                    'view'              => 'INLINE',
                    'name'              => 'truck_type_id',
                    'label'             => __('contenttypes::inputs.truck_type_id.label'),
                    'placeholder'       => __('contenttypes::inputs.truck_type_id.placeholder'),
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

            @include('ui::components.select', [
                'options' => [
                    'id'                => 'loading_type_select',
                    'view'              => 'INLINE',
                    'name'              => 'type',
                    'label'             => __('contenttypes::inputs.loading_type.label'),
                    'placeholder'       => __('contenttypes::inputs.loading_type.placeholder'),
                    'attributes'        => [],
                    'value'             => null,
                    'search'            => true,
                    'multiple'          => false,
                    'nullable'          => true,
                    'nullable_value'    => null,
                    'options'           => config('contenttypes.loadingTypes'),
                    'option_text'       => function($key, $option)                   { return __($option['label']); },
                    'option_value'      => function($key, $option)                   { return $option['id']; },
                    'option_attributes' => function($key, $option)                   { return []; },
                    'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                    'disabled'          => function($options, $key, $option, $value) { return false; },
                ],
            ])

            <hr class="preview-hr">

            @include('ui::components.select', [
                'options' => [
                    'id'                => 'from_city_select',
                    'jsSelect2'         => 'from_city_JsSelect2',
                    'view'              => 'INLINE',
                    'name'              => 'from_city_id',
                    'label'             => __('contenttypes::inputs.from_city_id.label'),
                    'placeholder'       => __('contenttypes::inputs.from_city_id.placeholder'),
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

            @include('ui::components.select', [
                'options' => [
                    'id'                => 'to_city_select',
                    'jsSelect2'         => 'to_city_JsSelect2',
                    'view'              => 'INLINE',
                    'name'              => 'to_city_id',
                    'label'             => __('contenttypes::inputs.to_city_id.label'),
                    'placeholder'       => __('contenttypes::inputs.to_city_id.placeholder'),
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

            <hr class="preview-hr">

            @component('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'number',
                    'name'        => 'price',
                    'label'       => __('contenttypes::inputs.price.label'),
                    'placeholder' => __('contenttypes::inputs.price.placeholder'),
                    'attributes'  => [
                        'v-model' => 'current_item.price',
                    ]
                ],
            ])
                <x-slot name="after">
                    <div class="form-text-hint">
                        <small class="overline-title">SAR</small>
                    </div>
                </x-slot>
            @endcomponent

            @component('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'number',
                    'name'        => 'pre_additional_location_price',
                    'label'       => __('contenttypes::inputs.pre_additional_location_price.label'),
                    'placeholder' => __('contenttypes::inputs.pre_additional_location_price.placeholder'),
                    'attributes'  => [
                        'v-model'   => 'current_item.pre_additional_location_price',
                        ':disabled' => "current_item.loading_type != 'CITY_TO_CITY_MULTIPLE_LOCATIONS'",
                    ]
                ],
            ])
                <x-slot name="after">
                    <div class="form-text-hint">
                        <small class="overline-title">SAR</small>
                    </div>
                </x-slot>
            @endcomponent
            
            <x-slot name="footer">
                <button type="button" class="btn btn-sm btn-success" v-on:click="addItem()">
                    <span>{{ __('contenttypes::strings.contract_items.save') }}</span>
                </button>

                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger modal-close">
                    <span>{{ __('ui::strings.cancel') }}</span>
                </button>
            </x-slot>
        @endcomponent
    </div>
    @endcomponent
@endsection

@endsection

@push('script')
    <script>
        const CrudApp = new Vue({
            el: '#vue-form',
            data: {
                items        : [],
                is_loading   : false,
                current_item : {
                    id                            : null,
                    truck_type                    : null,
                    loading_type                  : null,
                    from_city                     : null,
                    to_city                       : null,
                    price                         : null,
                    pre_additional_location_price : null,
                },
            },
            methods: {
                removeItem: function(index) {
                    this.items.splice(index, 1);
                },
                addItem: function() {
                    BS.RESET_FORM_VALIDATION_ERRORS($('#itemsModal'));
                    var validations = []
                    if (! this.current_item.truck_type) {
                        validations.push({
                            field   : 'truck_type_id',
                            message : '{{  __('validation.required', ['attribute' => __('contenttypes::inputs.truck_type_id.label')]) }}'
                        });
                    }
                    if (! this.current_item.loading_type) {
                        validations.push({
                            field   : 'type',
                            message : '{{  __('validation.required', ['attribute' => __('contenttypes::inputs.loading_type.label')]) }}'
                        });
                    }
                    if (! this.current_item.from_city) {
                        validations.push({
                            field   : 'from_city_id',
                            message : '{{  __('validation.required', ['attribute' => __('contenttypes::inputs.from_city_id.label')]) }}'
                        });
                    }
                    if (! this.current_item.to_city) {
                        validations.push({
                            field   : 'to_city_id',
                            message : '{{  __('validation.required', ['attribute' => __('contenttypes::inputs.to_city_id.label')]) }}'
                        });
                    }

                    if (validations.length > 0) {
                        BS.SHOW_FORM_VALIDATION_ERRORS($('#itemsModal'), validations);
                        return;
                    }

                    $('#itemsModal').find('.modal-close').click();

                    const itemCheck = this.findItemByData(this.current_item);
                    if (itemCheck) {
                        itemCheck.price                         = this.current_item.price;
                        itemCheck.pre_additional_location_price = this.current_item.pre_additional_location_price;
                    }
                    else {
                        this.items.push({...this.current_item});
                    }
                },
                findItemByData: function(item) {
                    const result = this.items.filter(function(_item) {
                        return _item.truck_type.id == item.truck_type.id && _item.loading_type == item.loading_type && _item.from_city.id == item.from_city.id && _item.to_city.id == item.to_city.id;
                    });
                    return result.length > 0 ? result[0] : null;
                }
            },
            computed: {
                loadingTypes: function() {
                    return @json(collect(config('contenttypes.loadingTypes'))->mapWithKeys(function ($item) {
                        return [$item['id'] => __($item['label'])];
                    }));
                },
            }
        });

        var payment_term_id_jsSelect2 = $('#payment_term_id_select').select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('CategoryController@select') }}';
                },
                data: function (params) {
                    var query = {
                        search      : params.term,
                        type        : 'payment-term',
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

        var trucks_type_id_jsSelect2 = $('#trucks_type_id_select').select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('CategoryController@select') }}';
                },
                data: function (params) {
                    var query = {
                        nullable    : 'N',
                        search      : params.term,
                        type        : 'truck-type',
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

        var commodity_id_jsSelect2 = $('#commodity_id_select').select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('CategoryController@select') }}';
                },
                data: function (params) {
                    var query = {
                        nullable    : 'N',
                        search      : params.term,
                        type        : 'commodity',
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

        var truck_type_id_jsSelect2 = $('#truck_type_id_select').select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('CategoryController@select') }}';
                },
                data: function (params) {
                    var query = {
                        nullable    : 'N',
                        search      : params.term,
                        type        : 'truck-type',
                        page        : params.page || 1
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
            CrudApp.current_item.truck_type = _data?.id ? _data.data : null;
            console.log(CrudApp.current_item);
        });

        var loading_type_jsSelect2 = $('#loading_type_select').select2()
        .on("select2:select", function() {
            let _data = $(this).select2('data')[0];
            CrudApp.current_item.loading_type = _data?.id ? _data.id : null;
            if (CrudApp.current_item.loading_type && CrudApp.current_item.loading_type != 'CITY_TO_CITY_MULTIPLE_LOCATIONS') {
                CrudApp.current_item.pre_additional_location_price = 0;
            }
            console.log(CrudApp.current_item);
        });

        var from_city_JsSelect2 = $('#from_city_select').select2({
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
                        country_id  : 191
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
            CrudApp.current_item.from_city = _data?.id ? _data.data : null;
            console.log(CrudApp.current_item);
        });

        var to_city_JsSelect2 = $('#to_city_select').select2({
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
                        country_id  : 191
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
            CrudApp.current_item.to_city = _data?.id ? _data.data : null;
            console.log(CrudApp.current_item);
        });
    </script>
@endpush