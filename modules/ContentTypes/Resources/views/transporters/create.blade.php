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
    <style>
        .previous-company-search {
            background: linear-gradient(135deg, rgba(101, 118, 255, 0.08), rgba(255, 255, 255, 0.95));
            border-color: rgba(101, 118, 255, 0.22) !important;
        }
        .previous-company-search .company-search-icon {
            align-items: center;
            background: rgba(101, 118, 255, 0.14);
            border-radius: 50%;
            color: #6576ff;
            display: inline-flex;
            flex: 0 0 42px;
            font-size: 21px;
            height: 42px;
            justify-content: center;
            width: 42px;
        }
        .previous-company-search .select2-container {
            width: 100% !important;
        }
        .previous-company-search .select2-selection--single {
            align-items: center;
            display: flex;
            min-height: 44px;
        }
        .previous-company-search .select2-selection__arrow {
            height: 42px !important;
        }
        .previous-company-search .select2-selection__clear {
            margin-inline: 0.55rem;
        }
        .transporter-account-dropdown .select2-results__option {
            padding: 0.65rem 0.75rem;
        }
        .transporter-account-result {
            min-width: 0;
        }
        .transporter-account-result .company-details {
            direction: ltr;
            overflow-wrap: anywhere;
        }
        .transporter-account-result .company-details .icon {
            margin-inline-end: 0.4rem;
        }
    </style>

    @component('ui::components.form', [
        'options' => [
            'id'                => null,
            'action'            => route('TransportersController@postCreate'),
            'method'            => 'POST',
            'class'             => '',
            'attributes'        => [
                'onsubmit'                     => 'CrudApp.submit(event)',
                'data-keep-loading-on-success' => 'true',
            ],
        ],
    ])
    <div class="row" id="vue-form">
        <input ref="itemsJson" type="hidden" name="items_json">
        <!-- Form Fields Section -->
        <div class="col-md-12 mb-4">
            <div class="nk-block nk-block-lg">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="previous-company-search rounded border p-3">
                                    <div class="row align-items-center g-3">
                                        <div class="col-lg-5">
                                            <div class="d-flex align-items-center">
                                                <span class="company-search-icon mr-3">
                                                    <em class="icon ni ni-building"></em>
                                                </span>
                                                <div>
                                                    <label class="form-label mb-1" for="transporter_account_select">
                                                        {{ __('contenttypes::inputs.transporter_account.label') }}
                                                    </label>
                                                    <span class="form-note">
                                                        {{ __('contenttypes::inputs.transporter_account.description') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-control-wrap">
                                                <select
                                                    id="transporter_account_select"
                                                    name="transporter_user_id"
                                                    class="form-control"
                                                    data-placeholder="{{ __('contenttypes::inputs.transporter_account.placeholder') }}">
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">

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

                            </div>
                            <div class="col-md-3">

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

                            </div>
                            <div class="col-md-3">

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
                                        'select_all'        => true,
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

                            </div>
                            <div class="col-md-3">

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

                                @include('ui::components.input', [
                                    'options' => [
                                        'view'        => 'DEFAULT',
                                        'type'        => 'file',
                                        'name'        => 'TRANSPORTER_CONTRACT',
                                        'label'       => __('contenttypes::inputs.contract_file.label'),
                                        'description' => __('contenttypes::inputs.contract_file.description'),
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

                            </div>
                        </div>
                    </div>
                </div>        
            </div>
        </div>
        
        <!-- Table Section -->
        <div class="col-md-12 mb-4">
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
                                    <th style="min-width: 200px;">
                                        <span>{{ __('contenttypes::inputs.price.label') }}</span>
                                    </th>
                                    <th style="min-width: 200px;">
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
                                                        'data-json-only' => 'true',
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
                                                        'data-json-only' => 'true',
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
                                                        'data-json-only' => 'true',
                                                        ':name'   => "'items[' + item_index + '][loading_type_id]'",
                                                        ':value'  => 'item.loading_type',
                                                    ],
                                                ],
                                            ])
                                            @endcomponent
                                        </td>
                                        <td style="vertical-align: middle;">
                                            @{{ item.from_city['name_' + locale] }}<br>
                                            <small class="text-primary">
                                                @{{ item.from_city?.country?.['name_' + locale] }}
                                            </small>
                                            @component('ui::components.input', [
                                                'options' => [
                                                    'type'        => 'hidden',
                                                    'name'        => '--',
                                                    'attributes'  => [
                                                        'data-json-only' => 'true',
                                                        ':name'   => "'items[' + item_index + '][from_city_id]'",
                                                        ':value'  => 'item.from_city?.id',
                                                    ],
                                                ],
                                            ])
                                            @endcomponent
                                        </td>
                                        <td style="vertical-align: middle;">
                                            @{{ item.to_city['name_' + locale] }}<br>
                                            <small class="text-primary">
                                                @{{ item.to_city?.country?.['name_' + locale] }}
                                            </small>
                                            @component('ui::components.input', [
                                                'options' => [
                                                    'type'        => 'hidden',
                                                    'name'        => '--',
                                                    'attributes'  => [
                                                        'data-json-only' => 'true',
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
                                                        'data-json-only' => 'true',
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
                                                            'data-json-only' => 'true',
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
        
        <!-- Textareas and Save Button Section -->
        <div class="col-md-12">
            <div class="nk-block nk-block-lg">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="row">
                            <div class="col-md-4">
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
                            </div>
                            <div class="col-md-4">
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
                            </div>
                            <div class="col-md-4">
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
                        
                        <hr class="preview-hr">
                        
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-lg btn-primary">
                                <span>{{ __('ui::strings.save') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>        @component('ui::components.modal', [
            'options' => [
                'id'        => 'itemsModal',
                'title'     => __('contenttypes::strings.contract_items.header') . ' (' . __('contenttypes::strings.multiple_selection') . ')',
                'size'      => 'lg',
                'fit'       => false,
            ]
        ])

            @include('ui::components.select', [
                'options' => [
                    'id'                => 'truck_type_id_select',
                    'jsSelect2'         => 'truck_type_id_jsSelect2',
                    'view'              => 'INLINE',
                    'name'              => 'truck_type_id[]',
                    'label'             => __('contenttypes::inputs.truck_type_id.label') . ' ({{ __("contenttypes::strings.multiple_selection") }})',
                    'placeholder'       => __('contenttypes::inputs.truck_type_id.placeholder'),
                    'attributes'        => [],
                    'value'             => null,
                    'search'            => true,
                    'multiple'          => true,
                    'sync_from'         => '#trucks_type_id_select',
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
                    'name'              => 'type[]',
                    'label'             => __('contenttypes::inputs.loading_type.label') . ' ({{ __("contenttypes::strings.multiple_selection") }})',
                    'placeholder'       => __('contenttypes::inputs.loading_type.placeholder'),
                    'attributes'        => [],
                    'value'             => null,
                    'search'            => true,
                    'multiple'          => true,
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
                    'name'              => 'from_city_id[]',
                    'label'             => __('contenttypes::inputs.from_city_id.label') . ' ({{ __("contenttypes::strings.multiple_selection") }})',
                    'placeholder'       => __('contenttypes::inputs.from_city_id.placeholder'),
                    'attributes'        => [],
                    'value'             => null,
                    'search'            => true,
                    'multiple'          => true,
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
                    'name'              => 'to_city_id[]',
                    'label'             => __('contenttypes::inputs.to_city_id.label') . ' ({{ __("contenttypes::strings.multiple_selection") }})',
                    'placeholder'       => __('contenttypes::inputs.to_city_id.placeholder'),
                    'attributes'        => [],
                    'value'             => null,
                    'search'            => true,
                    'multiple'          => true,
                    'options'           => [],
                    'option_text'       => function($key, $option)                   { return $option['text']; },
                    'option_value'      => function($key, $option)                   { return $option['id']; },
                    'option_attributes' => function($key, $option)                   { return []; },
                    'selected'          => function($options, $key, $option, $value) { return $option['id'] == $value; },
                    'disabled'          => function($options, $key, $option, $value) { return false; },
                ],
            ])

            <hr class="preview-hr">
            
            <!-- Combination Preview -->
            <div class="alert alert-info" v-if="combinationsCount > 0">
                <div class="d-flex align-items-center">
                    <em class="icon ni ni-info-fill me-2"></em>
                    <div>
                        <strong>{{ __('contenttypes::strings.bulk_creation_info') }}</strong><br>
                        <small>
                            @{{ combinationsCount }} {{ __('contenttypes::strings.combinations_will_be_created') }}
                            <template v-if="combinationsCount > 50">
                                <span class="text-warning">⚠️ {{ __('contenttypes::strings.large_combinations_warning') }}</span>
                            </template>
                        </small>
                    </div>
                </div>
            </div>

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
                        ':disabled' => "!current_item.loading_types.includes('CITY_TO_CITY_MULTIPLE_LOCATIONS')",
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
                <button type="button" class="btn btn-sm btn-success" v-on:click="addItem()" :disabled="combinationsCount === 0">
                    <template v-if="combinationsCount > 0">
                        <span>{{ __('contenttypes::strings.contract_items.add') }} (@{{ combinationsCount }})</span>
                    </template>
                    <template v-else>
                        <span>{{ __('contenttypes::strings.contract_items.add') }}</span>
                    </template>
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
        function formatTransporterAccountResult(item) {
            if (item.loading || !item.id) {
                return item.text;
            }

            const $result = $('<div>', { class: 'transporter-account-result d-flex align-items-center py-1' });
            const $avatar = $('<span>', { class: 'user-avatar user-avatar-sm bg-primary-dim text-primary mr-3' });
            const $fallbackIcon = $('<em>', { class: 'icon ni ni-building' }).appendTo($avatar);

            if (item.logo) {
                $fallbackIcon.hide();
                $('<img>', {
                    src: item.logo,
                    alt: item.name || item.text || '',
                    css: { width: '100%', height: '100%', objectFit: 'cover' }
                }).on('error', function() {
                    $(this).remove();
                    $fallbackIcon.show();
                }).appendTo($avatar);
            }
            $avatar.appendTo($result);

            const $content = $('<div>', { class: 'flex-grow-1' });
            $('<div>', { class: 'font-weight-bold text-dark', text: item.name || item.text }).appendTo($content);

            const $details = $('<div>', { class: 'company-details small text-soft mt-1' });
            $('<div>').append($('<em>', { class: 'icon ni ni-mail' }), document.createTextNode(item.email || '')).appendTo($details);
            $('<div>').append($('<em>', { class: 'icon ni ni-call' }), document.createTextNode(item.phone_display || '')).appendTo($details);
            $details.appendTo($content);
            $content.appendTo($result);

            return $result;
        }

        window.CrudApp = new Vue({
            el: '#vue-form',
            data: {
                locale       : @json(app()->getLocale()),
                items        : [],
                is_loading   : false,
                current_item : {
                    id                            : null,
                    truck_types                   : [],
                    loading_types                 : [],
                    from_cities                   : [],
                    to_cities                     : [],
                    price                         : null,
                    pre_additional_location_price : null,
                },
            },
            methods: {
                submit: function(event) {
                    event.preventDefault();
                    this.$refs.itemsJson.value = this.itemsJson;
                    return BS.AJAX_FORM(event);
                },
                removeItem: function(index) {
                    this.items.splice(index, 1);
                },
                resetCurrentItem: function() {
                    this.current_item = {
                        id                            : null,
                        truck_types                   : [],
                        loading_types                 : [],
                        from_cities                   : [],
                        to_cities                     : [],
                        price                         : null,
                        pre_additional_location_price : null,
                    };
                },
                itemKey: function(item) {
                    return [item.truck_type?.id, item.loading_type, item.from_city?.id, item.to_city?.id].join(':');
                },
                addItem: function() {
                    BS.RESET_FORM_VALIDATION_ERRORS($('#itemsModal'));
                    var validations = []
                    
                    if (!this.current_item.truck_types || this.current_item.truck_types.length === 0) {
                        validations.push({
                            field   : 'truck_type_id[]',
                            message : '{{  __('validation.required', ['attribute' => __('contenttypes::inputs.truck_type_id.label')]) }}'
                        });
                    }
                    if (!this.current_item.loading_types || this.current_item.loading_types.length === 0) {
                        validations.push({
                            field   : 'type[]',
                            message : '{{  __('validation.required', ['attribute' => __('contenttypes::inputs.loading_type.label')]) }}'
                        });
                    }
                    if (!this.current_item.from_cities || this.current_item.from_cities.length === 0) {
                        validations.push({
                            field   : 'from_city_id[]',
                            message : '{{  __('validation.required', ['attribute' => __('contenttypes::inputs.from_city_id.label')]) }}'
                        });
                    }
                    if (!this.current_item.to_cities || this.current_item.to_cities.length === 0) {
                        validations.push({
                            field   : 'to_city_id[]',
                            message : '{{  __('validation.required', ['attribute' => __('contenttypes::inputs.to_city_id.label')]) }}'
                        });
                    }

                    if (validations.length > 0) {
                        BS.SHOW_FORM_VALIDATION_ERRORS($('#itemsModal'), validations);
                        return;
                    }

                    const itemsByKey = new Map();
                    this.items.forEach(item => {
                        const key = this.itemKey(item);
                        if (!itemsByKey.has(key)) {
                            itemsByKey.set(key, item);
                        }
                    });

                    this.current_item.truck_types.forEach(truckType => {
                        this.current_item.loading_types.forEach(loadingType => {
                            this.current_item.from_cities.forEach(fromCity => {
                                this.current_item.to_cities.forEach(toCity => {
                                    const item = {
                                        id: null,
                                        truck_type: truckType,
                                        loading_type: loadingType,
                                        from_city: fromCity,
                                        to_city: toCity,
                                        price: this.current_item.price,
                                        pre_additional_location_price: this.current_item.pre_additional_location_price
                                    };
                                    const key = this.itemKey(item);
                                    const existingItem = itemsByKey.get(key);

                                    if (existingItem) {
                                        existingItem.price = item.price;
                                        existingItem.pre_additional_location_price = item.pre_additional_location_price;
                                    } else {
                                        this.items.push(item);
                                        itemsByKey.set(key, item);
                                    }
                                });
                            });
                        });
                    });

                    // Close modal and reset form
                    $('#itemsModal').find('.modal-close').click();
                    this.resetCurrentItem();
                    
                    // Clear the select2 components
                    $('#truck_type_id_select').val(null).trigger('change');
                    $('#loading_type_select').val(null).trigger('change');
                    $('#from_city_select').val(null).trigger('change');
                    $('#to_city_select').val(null).trigger('change');
                }
            },
            computed: {
                itemsJson: function() {
                    return JSON.stringify(this.items.map(item => ({
                        id: item.id || null,
                        truck_type_id: item.truck_type?.id || null,
                        loading_type_id: item.loading_type,
                        from_city_id: item.from_city?.id || null,
                        to_city_id: item.to_city?.id || null,
                        price: item.price,
                        pre_additional_location_price: item.pre_additional_location_price
                    })));
                },
                loadingTypes: function() {
                    return @json(collect(config('contenttypes.loadingTypes'))->mapWithKeys(function ($item) {
                        return [$item['id'] => __($item['label'])];
                    }));
                },
                combinationsCount: function() {
                    const truckTypesCount = this.current_item.truck_types ? this.current_item.truck_types.length : 0;
                    const loadingTypesCount = this.current_item.loading_types ? this.current_item.loading_types.length : 0;
                    const fromCitiesCount = this.current_item.from_cities ? this.current_item.from_cities.length : 0;
                    const toCitiesCount = this.current_item.to_cities ? this.current_item.to_cities.length : 0;
                    
                    if (truckTypesCount === 0 || loadingTypesCount === 0 || fromCitiesCount === 0 || toCitiesCount === 0) {
                        return 0;
                    }
                    
                    return truckTypesCount * loadingTypesCount * fromCitiesCount * toCitiesCount;
                }
            }
        });

        var transporter_account_jsSelect2 = $('#transporter_account_select').select2({
            width: '100%',
            allowClear: true,
            placeholder: '{{ __('contenttypes::inputs.transporter_account.placeholder') }}',
            dropdownCssClass: 'transporter-account-dropdown',
            ajax: {
                delay: 250,
                url: '{{ route('TransportersController@companyAccountsSelect') }}',
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1,
                    };
                },
                processResults: function(data) {
                    return data;
                },
            },
            templateResult: formatTransporterAccountResult,
            templateSelection: function(item) {
                return item.name || item.text;
            },
        }).on('select2:select', function(event) {
            const company = event.params.data;

            $('[name="name"]').val(company.name || '').prop('readonly', true).trigger('input').trigger('change');
            $('[name="email"]').val(company.email || '').trigger('input').trigger('change');
            $('[name="phone_number"]').val(company.phone_number || '').trigger('input').trigger('change');
        }).on('select2:clear select2:unselect', function() {
            $('[name="name"]').prop('readonly', false);
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
        .on("select2:select select2:unselect", function() {
            let selectedData = $(this).select2('data');
            CrudApp.current_item.truck_types = selectedData.map(item => item.data || item);
            console.log('Truck Types Selected:', CrudApp.current_item.truck_types);
        });

        var loading_type_jsSelect2 = $('#loading_type_select').select2()
        .on("select2:select select2:unselect", function() {
            let selectedData = $(this).select2('data');
            CrudApp.current_item.loading_types = selectedData.map(item => item.id);
            
            // Reset pre_additional_location_price if no CITY_TO_CITY_MULTIPLE_LOCATIONS selected
            const hasMultipleLocations = CrudApp.current_item.loading_types.includes('CITY_TO_CITY_MULTIPLE_LOCATIONS');
            if (!hasMultipleLocations) {
                CrudApp.current_item.pre_additional_location_price = 0;
            }
            
            console.log('Loading Types Selected:', CrudApp.current_item.loading_types);
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
        .on("select2:select select2:unselect", function() {
            let selectedData = $(this).select2('data');
            CrudApp.current_item.from_cities = selectedData.map(item => item.data || item);
            console.log('From Cities Selected:', CrudApp.current_item.from_cities);
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
        .on("select2:select select2:unselect", function() {
            let selectedData = $(this).select2('data');
            CrudApp.current_item.to_cities = selectedData.map(item => item.data || item);
            console.log('To Cities Selected:', CrudApp.current_item.to_cities);
        });
    </script>
@endpush
