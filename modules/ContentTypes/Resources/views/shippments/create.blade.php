@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'md',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.shippments.create'),
        'items' => [
            [
                'title' => __('contenttypes::strings.shippments.menu'),
                'link'  => route('ShippmentsController@index')
            ],
            [
                'title' => __('contenttypes::strings.shippments.create'),
                'link'  => route('ShippmentsController@create')
            ]
        ],
    ]
])
    <div class="nk-block-head-content" id="vue-header">
        <div class="toggle-wrap nk-block-tools-toggle">
            <a href="javascript:;" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu">
                <em class="icon ni ni-menu-alt-r"></em>
            </a>
            <div class="toggle-expand-content" data-content="pageMenu">
                <ul class="nk-block-tools g-3">
                    <li>
                        <div class="stepper">
                            <div class="step" :class="{'active': step == 1}">
                                <div class="circle" :class="{'inactive': step == 2}">1</div>
                                <div class="label" :class="{'text-muted': step == 2}">
                                    {{ __('contenttypes::strings.shippments.steps.step1') }}
                                    <span v-if="stepErrors[1]" class="badge badge-danger ml-1">@{{ stepErrors[1] }}</span>
                                </div>
                            </div>
                            <div class="arrow">›</div>
                            <div class="step" :class="{'active': step == 2}">
                                <div class="circle" :class="{'inactive': step == 1}">2</div>
                                <div class="label" :class="{'text-muted': step == 1}">
                                    {{ __('contenttypes::strings.shippments.steps.step2') }}
                                    <span v-if="stepErrors[2]" class="badge badge-danger ml-1">@{{ stepErrors[2] }}</span>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endcomponent

@section('content')

<div class="nk-block nk-block-lg" id="vue-form">
    <div class="card card-bordered">
        <div class="card-inner">

            @component('ui::components.form', [
                'options' => [
                    'id'                => 'shippment_form',
                    'action'            => route('ShippmentsController@postCreate'),
                    'method'            => 'POST',
                    'class'             => '',
                    'attributes'        => [
                        'onsubmit'                     => 'BS.AJAX_FORM(event)',
                        'data-keep-loading-on-success' => 'true',
                    ],
                ],
            ])

                <div data-shippment-step="1" :style="{ 'display': step == 2 ? 'none' : 'block' }">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h4 class="nk-block-title">{{ __('contenttypes::strings.shippments.steps.step1') }}</h4>
                            <p>{{ __('contenttypes::strings.shippments.steps.step1_desc') }}</p>
                        </div>
                    </div>
                    <hr class="preview-hr" style="margin-bottom: 0.5rem">
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="weight-slider-container" id="weightSliderWrapper" dir="rtl">
                                <label class="form-label">
                                    {{ __('contenttypes::inputs.weight.label') }}
                                    (
                                        <span style="color: #ff4b2b">
                                            @{{ Number(total_weight).toFixed(2) }} {{ __('contenttypes::inputs.weight.tons') }}
                                        </span>
                                    )
                                </label>
                                <div class="slider-wrapper">
                                    <span class="min">0<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
                                    <input
                                        name="total_weight"
                                        type="range"
                                        min="0"
                                        max="48"
                                        step="0.5"
                                        value="5"
                                        class="slider"
                                        id="weightRange"
                                        v-model="total_weight"
                                    />
                                    <span class="max">48<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
                                </div>
                                <div class="marks">
                                    <span>5<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
                                    <span>24<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
                                    <span>48<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row g-3">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            @component('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'number',
                                    'name'        => 'declared_order_value',
                                    'label'       => __('contenttypes::inputs.declared_order_value.label'),
                                    'placeholder' => __('contenttypes::inputs.declared_order_value.placeholder'),
                                    'description' => __('contenttypes::inputs.declared_order_value.description'),
                                    'attributes'  => [
                                        'v-model' => 'declared_order_value',
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
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            @include('ui::components.radios', [
                                'options' => [
                                    'view'              => 'DEFAULT',
                                    'type'              => 'radio',
                                    'name'              => 'load_size_id',
                                    'label'             => __('contenttypes::inputs.load_size_id.label'),
                                    'placeholder'       => __('contenttypes::inputs.load_size_id.placeholder'),
                                    'description'       => __('contenttypes::inputs.load_size_id.description'),
                                    'attributes'        => [],
                                    'value'             => null,
                                    'search'            => true,
                                    'multiple'          => false,
                                   'options'           => $loadSizes->map(function($item) {
                                        return [
                                            'id'       => 'ls_' . $item->id,
                                            'value'    => $item->id,
                                            'label'    => $item->__('title'),
                                            'selected' => false,
                                        ];
                                    })->toArray(),
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

                        <div class="col-md-6">
                            @component('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'number',
                                    'step'       => '1',
                                    'name'        => 'number_of_pallets',
                                    'label'       => __('contenttypes::inputs.number_of_pallets.label'),
                                    'placeholder' => __('contenttypes::inputs.number_of_pallets.placeholder'),
                                    'description' => __('contenttypes::inputs.number_of_pallets.description'),
                                    'attributes'  => [
                                        'v-model' => 'number_of_pallets',
                                    ]
                                ],
                            ])
                                <x-slot name="after">
                                    <div class="form-text-hint">
                                        <small class="overline-title">{{ __('contenttypes::inputs.number_of_pallets.pallet') }}</small>
                                    </div>
                                </x-slot>
                            @endcomponent
                        </div>

                        <div class="col-md-6">
                            @include('ui::components.radios', [
                                'options' => [
                                    'view'              => 'DEFAULT',
                                    'type'              => 'radio',
                                    'name'              => 'load_type_id',
                                    'label'             => __('contenttypes::inputs.load_type_id.label'),
                                    'placeholder'       => __('contenttypes::inputs.load_type_id.placeholder'),
                                    'description'       => __('contenttypes::inputs.load_type_id.description'),
                                    'attributes'        => [],
                                    'value'             => null,
                                    'search'            => true,
                                    'multiple'          => false,
                                    'options'           => $loadTypes->map(function($item) {
                                        return [
                                            'id'       => 'lt_' . $item->id,
                                            'value'    => $item->id,
                                            'label'    => $item->__('title'),
                                            'selected' => false,
                                        ];
                                    })->toArray(),
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
                    
                    <hr class="preview-hr" style="margin-bottom: 0.5rem">
                    <br>

                    <div class="row g-3">
                        <div class="col-md-6">
                            @include('ui::components.select', [
                                'options' => [
                                    'id'                => 'truck_type_id_select',
                                    'jsSelect2'         => 'truck_type_id_jsSelect2',
                                    'view'              => 'DEFAULT',
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
                        </div>
                        <div class="col-md-6">
                            @component('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'number',
                                    'name'        => 'number_of_trucks',
                                    'label'       => __('contenttypes::inputs.number_of_trucks.label'),
                                    'placeholder' => __('contenttypes::inputs.number_of_trucks.placeholder'),
                                    'description' => __('contenttypes::inputs.number_of_trucks.description'),
                                    'attributes'  => [
                                        'v-model' => 'number_of_trucks',
                                    ]
                                ],
                            ])
                                <x-slot name="after">
                                    <div class="form-text-hint">
                                        <small class="overline-title">{{ __('contenttypes::inputs.number_of_trucks.truck') }}</small>
                                    </div>
                                </x-slot>
                            @endcomponent
                            <strong class="form-note text-warning">{!! __('contenttypes::inputs.number_of_trucks.description') !!}</strong>
                        </div>
                        <div class="col-md-6">
                            @component('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'number',
                                    'name'        => 'equipment_length',
                                    'label'       => __('contenttypes::inputs.equipment_length.label'),
                                    'placeholder' => __('contenttypes::inputs.equipment_length.placeholder'),
                                    'description' => __('contenttypes::inputs.equipment_length.description'),
                                    'attributes'  => [
                                        'v-model' => 'equipment_length',
                                        'min'     => 0,
                                    ]
                                ],
                            ])
                                <x-slot name="after">
                                    <div class="form-text-hint">
                                        <small class="overline-title">{{ __('contenttypes::inputs.equipment_length.meters') }}</small>
                                    </div>
                                </x-slot>
                            @endcomponent
                        </div>
                        <div class="col-md-6">
                            @component('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'number',
                                    'name'        => 'equipment_width',
                                    'label'       => __('contenttypes::inputs.equipment_width.label'),
                                    'placeholder' => __('contenttypes::inputs.equipment_width.placeholder'),
                                    'description' => __('contenttypes::inputs.equipment_width.description'),
                                    'attributes'  => [
                                        'v-model' => 'equipment_width',
                                        'min'     => 0,
                                    ]
                                ],
                            ])
                                <x-slot name="after">
                                    <div class="form-text-hint">
                                        <small class="overline-title">{{ __('contenttypes::inputs.equipment_width.meters') }}</small>
                                    </div>
                                </x-slot>
                            @endcomponent
                        </div>
                        <div class="col-md-6">
                            @component('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'number',
                                    'step'       => '1',
                                    'name'        => 'temperature',
                                    'label'       => __('contenttypes::inputs.temperature.label'),
                                    'placeholder' => __('contenttypes::inputs.temperature.placeholder'),
                                    'description' => __('contenttypes::inputs.temperature.description'),
                                    'attributes'  => [
                                        'v-model' => 'temperature',
                                    ]
                                ],
                            ])
                                <x-slot name="after">
                                    <div class="form-text-hint">
                                        <small class="overline-title">{{ __('contenttypes::inputs.temperature.celsius') }}</small>
                                    </div>
                                </x-slot>
                            @endcomponent
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_required_at" class="form-label">{{ __('contenttypes::inputs.delivery_required_at.label') }}</label>

                                <div class="form-control-wrap">
                                    <div class="form-icon form-icon-right">
                                        <em class="icon ni ni-calendar-alt"></em>
                                    </div>
                                    <ui-datepicker id="delivery_required_at" name="delivery_required_at"></ui-datepicker>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <hr class="preview-hr" style="margin-bottom: 0.5rem">
                    <br>

                    <div class="row g-3">
                        <div class="col-md-6">
                            @include('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'textarea',
                                    'name'        => 'public_notes',
                                    'label'       => __('contenttypes::inputs.public_notes.label'),
                                    'placeholder' => __('contenttypes::inputs.public_notes.placeholder'),
                                    'description' => __('contenttypes::inputs.public_notes.description'),
                                ],
                            ])
                            @include('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'textarea',
                                    'name'        => 'private_notes',
                                    'label'       => __('contenttypes::inputs.private_notes.label'),
                                    'placeholder' => __('contenttypes::inputs.private_notes.placeholder'),
                                    'description' => __('contenttypes::inputs.private_notes.description'),
                                ],
                            ])
                        </div>
                        <div class="col-md-6">
                            @include('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'file',
                                    'name'        => 'ATTACHMENT_1',
                                    'file_preview' => true,
                                    'label'       => __('contenttypes::inputs.attachment_1.label'),
                                    'description' => __('contenttypes::inputs.attachment_1.description'),
                                    'placeholder' => null,
                                    'value'       => null,
                                ],
                            ])
                            @include('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'file',
                                    'name'        => 'ATTACHMENT_2',
                                    'file_preview' => true,
                                    'label'       => __('contenttypes::inputs.attachment_2.label'),
                                    'description' => __('contenttypes::inputs.attachment_2.description'),
                                    'placeholder' => null,
                                    'value'       => null,
                                ],
                            ])
                            @include('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'file',
                                    'name'        => 'ATTACHMENT_3',
                                    'file_preview' => true,
                                    'label'       => __('contenttypes::inputs.attachment_3.label'),
                                    'description' => __('contenttypes::inputs.attachment_3.description'),
                                    'placeholder' => null,
                                    'value'       => null,
                                ],
                            ])
                            @include('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'file',
                                    'name'        => 'ATTACHMENT_4',
                                    'file_preview' => true,
                                    'label'       => __('contenttypes::inputs.attachment_4.label'),
                                    'description' => __('contenttypes::inputs.attachment_4.description'),
                                    'placeholder' => null,
                                    'value'       => null,
                                ],
                            ])
                            {{-- @include('ui::components.input', [
                                'options' => [
                                    'view'        => 'DEFAULT',
                                    'type'        => 'file',
                                    'name'        => 'WAY_BILL',
                                    'label'       => __('contenttypes::inputs.WAY_BILL.label'),
                                    'description' => __('contenttypes::inputs.WAY_BILL.description'),
                                    'placeholder' => null,
                                    'value'       => null,
                                ],
                            ]) --}}
                        </div>
                    </div>
                </div>
                <div data-shippment-step="2" :style="{ 'display': step == 2 ? 'block' : 'none' }">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h4 class="nk-block-title">{{ __('contenttypes::strings.shippments.steps.step2') }}</h4>
                            <p>{{ __('contenttypes::strings.shippments.steps.step2_desc') }}</p>
                        </div>
                    </div>
                    <hr class="preview-hr" style="margin-bottom: 0.5rem">
                    <br>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city_select" class="form-label">{{ __('contenttypes::inputs.from_city_id.label') }}</label>
                                <div class="form-control-wrap">
                                    <ui-select2 class="form-control" 
                                        :name="`from_city_id`"
                                        :selected="loading_address.city"
                                        v-on:options="citySelectOptions" 
                                        v-on:change="citySelected($event)">
                                    </ui-select2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city_select" class="form-label">{{ __('contenttypes::inputs.to_city_id.label') }}</label>
                                <div class="form-control-wrap">
                                    <ui-select2 class="form-control" 
                                        :name="`to_city_id`"
                                        :selected="to_address.city"
                                        v-on:options="citySelectOptions" 
                                        v-on:change="toCitySelected($event)">
                                    </ui-select2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @include('ui::components.radios', [
                                'options' => [
                                    'view'              => 'DEFAULT',
                                    'type'              => 'radio',
                                    'name'              => 'loading_type',
                                    'label'             => __('contenttypes::inputs.loading_type.label'),
                                    'placeholder'       => __('contenttypes::inputs.loading_type.placeholder'),
                                    'description'       => __('contenttypes::inputs.loading_type.description'),
                                    'attributes'        => [],
                                    'value'             => null,
                                    'search'            => true,
                                    'multiple'          => false,
                                    'options'           => collect(config('contenttypes.loadingTypes'))->filter(fn($item) => $item['id'] != 'CITY_TO_CITY_ROUND_TRIP')->map(function($item) {
                                        return [
                                            'id'       => 'st_' . $item['id'],
                                            'value'    => $item['id'],
                                            'label'    => __($item['label']),
                                            'selected' => $item['id'] == 'CITY_TO_CITY',
                                        ];
                                    })->toArray(),
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

                    <br>
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h6 style="color: #FB4F28">{{ __('contenttypes::strings.shippments.loading_address') }}</h6>
                        </div>
                    </div>
                    <hr class="preview-hr" style="margin-bottom: 0.5rem">
                    <br>

                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="row gy-4">
                                <div class="col-md-12">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'INLINE',
                                            'type'        => 'hidden',
                                            'name'        => 'loading_address_id',
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'loading_address.id',
                                                ':name'   => "'loading_address[id]'",
                                            ]
                                        ],
                                    ])
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'text',
                                            'name'        => 'loading_address[primary_contact_company_name]',
                                            'label'       => __('contenttypes::inputs.address_name.label'),
                                            'placeholder' => __('contenttypes::inputs.address_name.placeholder'),
                                            'description' => __('contenttypes::inputs.address_name.description'),
                                            'value'       => $loadingAddressDefaults['primary_contact_company_name'],
                                        ],
                                    ])
                                </div>
                                <div class="col-md-12">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'text',
                                            'name'        => 'loading_address[primary_contact_name]',
                                            'label'       => __('contenttypes::inputs.address_primary_contact.label'),
                                            'placeholder' => __('contenttypes::inputs.address_primary_contact.placeholder'),
                                            'description' => __('contenttypes::inputs.address_primary_contact.description'),
                                            'value'       => $loadingAddressDefaults['primary_contact_name'],
                                        ],
                                    ])
                                </div>
                                <div class="col-md-12">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'email',
                                            'name'        => 'loading_address[primary_contact_email]',
                                            'label'       => __('contenttypes::inputs.address_email.label'),
                                            'placeholder' => __('contenttypes::inputs.address_email.placeholder'),
                                            'description' => __('contenttypes::inputs.address_email.description'),
                                            'value'       => $loadingAddressDefaults['primary_contact_email'],
                                        ],
                                    ])
                                </div>
                                <div class="col-md-12">
                                    @component('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'text',
                                            'name'        => 'loading_address[primary_contact_phone_number]',
                                            'label'       => __('contenttypes::inputs.address_phone_number.label'),
                                            'placeholder' => __('contenttypes::inputs.address_phone_number.placeholder'),
                                            'description' => __('contenttypes::inputs.address_phone_number.description'),
                                            'value'       => $loadingAddressDefaults['primary_contact_phone_number'],
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
                                <div class="col-md-12">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'text',
                                            'name'        => 'loading_address[lat_lng]',
                                            'label'       => __('contenttypes::inputs.lat.label') . ' / ' . __('contenttypes::inputs.lng.label'),
                                            'placeholder' => __('contenttypes::inputs.lat.placeholder') . ' / ' . __('contenttypes::inputs.lng.placeholder'),
                                            'description' => __('contenttypes::inputs.lat.description') . ' / ' . __('contenttypes::inputs.lng.description'),
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model'     => 'loading_address.latlng',
                                                'v-on:change' => 'setLatlng($event.target.value)',
                                            ],
                                        ],
                                    ])
                                    <span class="form-note text-primary mt-1">
                                        <em class="icon ni ni-link mr-1"></em>
                                        {{ __('contenttypes::strings.shippments.google_maps_link_hint') }}
                                    </span>
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'INLINE',
                                            'type'        => 'hidden',
                                            'name'        => 'lat',
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'loading_address.lat',
                                                ':name'   => "'loading_address[lat]'",
                                            ]
                                        ],
                                    ])
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'INLINE',
                                            'type'        => 'hidden',
                                            'name'        => 'lng',
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'loading_address.lng',
                                                ':name'   => "'loading_address[lng]'",
                                            ]
                                        ],
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div dir="ltr" class="bs-map-lg" id="geo_map"></div>
                        </div>
                        <div class="col-md-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'textarea',
                                            'name'        => 'loading_address[full_address]',
                                            'label'       => __('contenttypes::inputs.full_address.label'),
                                            'placeholder' => __('contenttypes::inputs.full_address.placeholder'),
                                            'description' => __('contenttypes::inputs.full_address.description'),
                                            'value'       => $loadingAddressDefaults['full_address']
                                        ],
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'textarea',
                                            'name'        => 'loading_address[public_notes]',
                                            'label'       => __('contenttypes::inputs.public_notes.label'),
                                            'placeholder' => __('contenttypes::inputs.public_notes.placeholder'),
                                            'description' => __('contenttypes::inputs.public_notes.description'),
                                            'value'       => null
                                        ],
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'textarea',
                                            'name'        => 'loading_address[private_notes]',
                                            'label'       => __('contenttypes::inputs.private_notes.label'),
                                            'placeholder' => __('contenttypes::inputs.private_notes.placeholder'),
                                            'description' => __('contenttypes::inputs.private_notes.description'),
                                            'value'       => null
                                        ],
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h6 style="color: #FB4F28">{{ __('contenttypes::strings.shippments.offloading_address') }}</h6>
                        </div>
                    </div>
                    <hr class="preview-hr" style="margin-bottom: 0.5rem">
                    <br>

                    <div class="row gy-4" v-for="(address, address_index) in to_addresses" :style="{ 'display': address.removed == 'Y' ? 'none' : 'flex' }">
                        <div class="col-md-12">
                            <previous-delivery-address-search
                                :city-id="to_address.city ? to_address.city.id : null"
                                v-on:selected="applyPreviousDeliveryAddress(address, $event)">
                            </previous-delivery-address-search>
                        </div>
                        <div class="col-md-4">
                            <div class="row gy-4">
                                <div class="col-md-12">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'INLINE',
                                            'type'        => 'hidden',
                                            'name'        => 'offloading_address_id',
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.id',
                                                ':name'   => "'offloading_address[' + address_index + '][id]'",
                                            ]
                                        ],
                                    ])
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'text',
                                            'name'        => 'offloading_address[primary_contact_company_name]',
                                            'label'       => __('contenttypes::inputs.address_name.label'),
                                            'placeholder' => __('contenttypes::inputs.address_name.placeholder'),
                                            'description' => __('contenttypes::inputs.address_name.description'),
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.primary_contact_company_name',
                                                ':name'   => "'offloading_address[' + address_index + '][primary_contact_company_name]'",
                                            ]
                                        ],
                                    ])
                                </div>
                                <div class="col-md-12">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'text',
                                            'name'        => 'offloading_address[primary_contact_name]',
                                            'label'       => __('contenttypes::inputs.address_primary_contact.label'),
                                            'placeholder' => __('contenttypes::inputs.address_primary_contact.placeholder'),
                                            'description' => __('contenttypes::inputs.address_primary_contact.description'),
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.primary_contact_name',
                                                ':name'   => "'offloading_address[' + address_index + '][primary_contact_name]'",
                                            ]
                                        ],
                                    ])
                                </div>
                                <div class="col-md-12">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'email',
                                            'name'        => 'offloading_address[primary_contact_email]',
                                            'label'       => __('contenttypes::inputs.address_email.label'),
                                            'placeholder' => __('contenttypes::inputs.address_email.placeholder'),
                                            'description' => __('contenttypes::inputs.address_email.description'),
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.primary_contact_email',
                                                ':name'   => "'offloading_address[' + address_index + '][primary_contact_email]'",
                                            ]
                                        ],
                                    ])
                                </div>
                                <div class="col-md-12">
                                    @component('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'text',
                                            'name'        => 'offloading_address[primary_contact_phone_number]',
                                            'label'       => __('contenttypes::inputs.address_phone_number.label'),
                                            'placeholder' => __('contenttypes::inputs.address_phone_number.placeholder'),
                                            'description' => __('contenttypes::inputs.address_phone_number.description'),
                                            'attributes'  => ['dir' => 'ltr', 'style' => 'text-align: ' . ($_DIR_ == 'ltr' ? 'left' : 'right')],
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.primary_contact_phone_number',
                                                ':name'   => "'offloading_address[' + address_index + '][primary_contact_phone_number]'",
                                            ]
                                        ],
                                    ])
                                        <x-slot name="after">
                                            <div class="form-text-hint">
                                                <strong class="overline-title" dir="ltr">+966</strong>
                                            </div>
                                        </x-slot>
                                    @endcomponent
                                </div>
                                <div class="col-md-12">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'text',
                                            'name'        => 'offloading_address[lat_lng]',
                                            'label'       => __('contenttypes::inputs.lat.label') . ' / ' . __('contenttypes::inputs.lng.label'),
                                            'placeholder' => __('contenttypes::inputs.lat.placeholder') . ' / ' . __('contenttypes::inputs.lng.placeholder'),
                                            'description' => __('contenttypes::inputs.lat.description') . ' / ' . __('contenttypes::inputs.lng.description'),
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model'     => 'address.latlng',
                                                'v-on:change' => 'setLatlng($event.target.value, address)',
                                                ':name'       => "'offloading_address[' + address_index + '][lat_lng]'",
                                            ],
                                        ],
                                    ])
                                    <span class="form-note text-primary mt-1">
                                        <em class="icon ni ni-link mr-1"></em>
                                        {{ __('contenttypes::strings.shippments.google_maps_link_hint') }}
                                    </span>
                                    <span v-if="address.city_validation_status === 'pending'" class="form-note text-warning mt-1">
                                        <span class="spinner-border spinner-border-sm mr-1" role="status"></span>
                                        {{ __('contenttypes::strings.shippments.checking_destination_city') }}
                                    </span>
                                    <span v-else-if="address.city_validation_status === 'valid'" class="form-note text-success mt-1">
                                        <em class="icon ni ni-check-circle mr-1"></em>
                                        {{ __('contenttypes::strings.shippments.address_inside_destination_city') }}
                                    </span>
                                    <span v-else-if="address.city_validation_status === 'invalid'" class="form-note text-danger mt-1">
                                        <em class="icon ni ni-cross-circle mr-1"></em>
                                        {{ __('contenttypes::strings.shippments.address_outside_destination_city') }}
                                    </span>
                                    <span v-else-if="address.city_validation_status === 'error'" class="form-note text-danger mt-1">
                                        <em class="icon ni ni-alert-circle mr-1"></em>
                                        {{ __('contenttypes::strings.shippments.address_city_check_failed') }}
                                    </span>
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'INLINE',
                                            'type'        => 'hidden',
                                            'name'        => 'lat',
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.lat',
                                                ':name'   => "'offloading_address[' + address_index + '][lat]'",
                                            ]
                                        ],
                                    ])
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'INLINE',
                                            'type'        => 'hidden',
                                            'name'        => 'lng',
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.lng',
                                                ':name'   => "'offloading_address[' + address_index + '][lng]'",
                                            ]
                                        ],
                                    ])
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'INLINE',
                                            'type'        => 'hidden',
                                            'name'        => 'destination_city_token',
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.destination_city_token',
                                                ':name'   => "'offloading_address[' + address_index + '][destination_city_token]'",
                                            ]
                                        ],
                                    ])
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'INLINE',
                                            'type'        => 'hidden',
                                            'name'        => 'removed',
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.removed',
                                                ':name'   => "'offloading_address[' + address_index + '][removed]'",
                                            ]
                                        ],
                                    ])
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('contenttypes::inputs.round_trip.label') }}</label>
                                        <div class="form-control-wrap">
                                            <ul class="custom-control-group g-3 align-center flex-wrap">
                                                <li>
                                                    <div class="custom-control custom-radio" :class="{'checked': address.round_trip == 'NO' ? 'checked' : ''}">
                                                        <input :id="'rt_no' + address_index" type="radio" :name="`offloading_address[${address_index}][round_trip]`" value="NO" :checked="address.round_trip == 'NO'" class="custom-control-input"> 
                                                        <label :for="'rt_no' + address_index" class="custom-control-label">
                                                            {{ __('contenttypes::inputs.round_trip.options.NO') }}
                                                        </label>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="custom-control custom-radio" :class="{'checked': address.round_trip == 'YES' ? 'checked' : ''}">
                                                        <input :id="'rt_yes' + address_index" type="radio" :name="`offloading_address[${address_index}][round_trip]`" value="YES" :checked="address.round_trip == 'YES'" class="custom-control-input"> 
                                                        <label :for="'rt_yes' + address_index" class="custom-control-label">
                                                            {{ __('contenttypes::inputs.round_trip.options.YES') }} 
                                                        </label>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div dir="ltr" class="bs-map-lg" :id="address.el"></div>
                        </div>
                        <div class="col-md-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'textarea',
                                            'name'        => 'offloading_address[full_address]',
                                            'label'       => __('contenttypes::inputs.full_address.label'),
                                            'placeholder' => __('contenttypes::inputs.full_address.placeholder'),
                                            'description' => __('contenttypes::inputs.full_address.description'),
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.full_address',
                                                ':name'   => "'offloading_address[' + address_index + '][full_address]'",
                                            ]
                                        ],
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'textarea',
                                            'name'        => 'offloading_address[public_notes]',
                                            'label'       => __('contenttypes::inputs.public_notes.label'),
                                            'placeholder' => __('contenttypes::inputs.public_notes.placeholder'),
                                            'description' => __('contenttypes::inputs.public_notes.description'),
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.public_notes',
                                                ':name'   => "'offloading_address[' + address_index + '][public_notes]'",
                                            ]
                                        ],
                                    ])
                                </div>
                                <div class="col-md-4">
                                    @include('ui::components.input', [
                                        'options' => [
                                            'view'        => 'DEFAULT',
                                            'type'        => 'textarea',
                                            'name'        => 'offloading_address[private_notes]',
                                            'label'       => __('contenttypes::inputs.private_notes.label'),
                                            'placeholder' => __('contenttypes::inputs.private_notes.placeholder'),
                                            'description' => __('contenttypes::inputs.private_notes.description'),
                                            'value'       => null,
                                            'attributes'  => [
                                                'v-model' => 'address.private_notes',
                                                ':name'   => "'offloading_address[' + address_index + '][private_notes]'",
                                            ]
                                        ],
                                    ])
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 d-flex justify-end">
                            <a class="link link-danger link-sm text-danger" tabindex="-1" href="javascript:;" v-on:click="removeToAddress(address_index)">
                                <i class="fas fa-trash" style="margin-left: 5px; margin-right: 5px;"></i>
                                {{ __('contenttypes::strings.shippments.remove_offloading_address') }}
                            </a>
                        </div>
                        <div class="col-md-12">
                            <hr class="preview-hr" style="margin-bottom: 0.5rem">
                            <br>
                        </div>
                    </div>

                    <button type="button" class="btn btn-link link link-primary link-sm p-0" style="color: #FB4F28 !important;"
                        :disabled="!hasDestinationCity() || loading_type !== 'CITY_TO_CITY_MULTIPLE_LOCATIONS'"
                        v-on:click="addToAddress()">
                        <i class="fas fa-plus" style="margin-left: 5px; margin-right: 5px;"></i>
                        {{ __('contenttypes::strings.shippments.add_offloading_address') }}
                    </button>
                    <span v-if="!hasDestinationCity()" class="form-note text-warning mt-1">
                        {{ __('contenttypes::strings.shippments.select_destination_city_first') }}
                    </span>
                    <span v-else-if="loading_type !== 'CITY_TO_CITY_MULTIPLE_LOCATIONS'" class="form-note text-muted mt-1">
                        <em class="icon ni ni-info mr-1"></em>
                        {{ __('contenttypes::strings.shippments.multiple_locations_type_required') }}
                    </span>
                </div>

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'is_draft',
                        'value'       => '',
                        'attributes'  => [
                            'v-model' => 'is_draft',
                            ':name'   => "'is_draft'",
                        ]
                    ],
                ])

                @include('ui::components.input', [
                    'options' => [
                        'view'        => 'INLINE',
                        'type'        => 'hidden',
                        'name'        => 'is_rfq',
                        'value'       => '',
                        'attributes'  => [
                            'v-model' => 'is_rfq',
                            ':name'   => "'is_rfq'",
                        ]
                    ],
                ])

                <div class="form-group mt-2 d-flex justify-between">
                    <a href="{{ route('ShippmentsController@index') }}" class="btn btn-md btn-wider btn-dim btn-secondary" v-show="step == 1">
                        <em class="icon ni ni-cross-circle"></em>
                        <span>{{ __('ui::strings.cancel') }}</span>
                    </a>
                    <button type="button" class="btn btn-md btn-dim btn-secondary btn-wider" v-show="step == 2" v-on:click="switchStep(1)">
                        <em class="icon ni ni-arrow-left"></em>
                        <span>{{ __('ui::strings.previous') }}</span>
                    </button>
                    <div>
                        <button type="button" class="btn btn-md btn-primary" v-show="step == 1" v-on:click="switchStep(2)">
                            <span>{{ __('ui::strings.next') }}</span>
                            <em class="icon ni ni-arrow-right"></em>
                        </button>
                        {{-- <button type="button" id="save_as_draft" class="btn btn-md btn-secondary" v-show="step == 2" data-type="submit">
                            <em class="icon ni ni-done"></em>
                            <span>{{ __('contenttypes::strings.shippments.save_as_draft') }}</span>
                        </button>
                        <button type="button" id="save_and_send" class="btn btn-md btn-primary" v-show="step == 2" data-type="submit">
                            <em class="icon ni ni-done"></em>
                            <span>{{ __('contenttypes::strings.shippments.save_and_send') }}</span>
                        </button> --}}
                        <div class="dropdown" v-show="step == 2">
                            <button type="button" class="btn btn-primary btn-wider" data-toggle="dropdown" style="min-width: 275px;" data-type="submit">
                                <span>{{ __('contenttypes::strings.shippments.save') }}</span>
                                <em class="icon ni ni-chevron-down"></em>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-auto mt-1">
                                <ul class="link-list-opt no-bdr">
                                    <li>
                                        <a href="javascript:;" id="save_as_draft" class="text-muted">
                                            <em class="icon ni ni-alert"></em>
                                            <span>{{ __('contenttypes::strings.shippments.save_as_draft') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" id="save_and_send" class="text-success">
                                            <em class="icon ni ni-done"></em>
                                            <span>{{ __('contenttypes::strings.shippments.save_and_send') }}</span>
                                        </a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="javascript:;" id="save_and_send_rfq" class="text-danger">
                                            <em class="icon ni ni-info"></em>
                                            <span>{{ __('contenttypes::strings.shippments.save_and_send_rfq') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent

        </div>
    </div>
</div>

@endsection

@push('script')
    @include('contenttypes::shippments.partials.previous-delivery-address-search')
    <script>
        var companyMap = null;
        var destinationCityValidationQueue = Promise.resolve();
        var destinationCityValidationLastRequestAt = 0;

        const HeaderApp = new Vue({
            el: '#vue-header',
            data: {
                step: 1,
                stepErrors: { 1: 0, 2: 0 },
            },
            methods: {
                switchStep: function(step) {
                    this.step = step
                },
                setStepErrors: function(stepErrors) {
                    this.stepErrors = stepErrors;
                }
            }
        });

        const CrudApp = new Vue({
            el: '#vue-form',
            data: {
                total_weight     : 5,
                equipment_length: 0,
                equipment_width : 0,
                temperature     : 0,
                step             : 1,
                loading_type     : 'CITY_TO_CITY',
                is_draft         : 'N',
                is_rfq           : 'N',
                loading_address : {
                    id      : null,
                    city    : @json($loadingAddressDefaults['city']),
                    lat     : @json($loadingAddressDefaults['lat']),
                    lng     : @json($loadingAddressDefaults['lng']),
                    latlng  : @json($loadingAddressDefaults['latlng']),
                },
                to_address : {
                    city : null,
                },
                destination_city_center: null,
                destination_city_location_request_id: 0,
                to_addresses : [],
            },
            methods: {
                hasDestinationCity: function() {
                    return this.to_address.city && this.to_address.city.id && this.to_address.city.id != 0;
                },
                focusDestinationCity: function() {
                    const requestId = ++this.destination_city_location_request_id;
                    this.destination_city_center = null;
                    if (!this.hasDestinationCity()) {
                        return;
                    }

                    const selectedCityId = String(this.to_address.city.id);
                    $.ajax({
                        url: @json(route('ShippmentsController@destinationCityLocation')),
                        method: 'POST',
                        data: {
                            _token : @json(csrf_token()),
                            city_id: selectedCityId,
                        },
                    }).done((response) => {
                        if (requestId !== this.destination_city_location_request_id
                            || !this.hasDestinationCity()
                            || String(this.to_address.city.id) !== selectedCityId) {
                            return;
                        }

                        const center = {
                            lat: Number(response.lat),
                            lng: Number(response.lng),
                        };
                        if (!Number.isFinite(center.lat) || !Number.isFinite(center.lng)) {
                            return;
                        }

                        this.destination_city_center = center;
                        this.to_addresses
                            .filter((address) => address.removed === 'N'
                                && address.map
                                && typeof address.map.focusMap === 'function')
                            .forEach((address) => address.map.focusMap(center.lat, center.lng, 10));
                    }).fail(() => {
                        if (requestId === this.destination_city_location_request_id) {
                            NioApp.Toast(
                                @json(__('contenttypes::strings.shippments.destination_city_location_failed')),
                                'error',
                                {ui: 'is-dark'}
                            );
                        }
                    });
                },
                verifyAddressDestinationCity: function(address) {
                    address.destination_city_token = null;
                    if (address.removed === 'Y' || address.lat === null || address.lng === null) {
                        address.city_validation_status = 'idle';
                        return;
                    }
                    if (!this.hasDestinationCity()) {
                        address.city_validation_status = 'invalid';
                        return;
                    }

                    const validationId = ++address.city_validation_request_id;
                    const selectedCityId = String(this.to_address.city.id);
                    address.city_validation_status = 'pending';

                    destinationCityValidationQueue = destinationCityValidationQueue.then(() => new Promise((resolve) => {
                        if (validationId !== address.city_validation_request_id
                            || address.removed === 'Y'
                            || !this.hasDestinationCity()
                            || String(this.to_address.city.id) !== selectedCityId) {
                            resolve();
                            return;
                        }

                        const wait = Math.max(0, 1100 - (Date.now() - destinationCityValidationLastRequestAt));
                        window.setTimeout(() => {
                            if (validationId !== address.city_validation_request_id
                                || !this.hasDestinationCity()
                                || String(this.to_address.city.id) !== selectedCityId) {
                                resolve();
                                return;
                            }

                            destinationCityValidationLastRequestAt = Date.now();
                            $.ajax({
                                url: @json(route('ShippmentsController@verifyDestinationCity')),
                                method: 'POST',
                                data: {
                                    _token : @json(csrf_token()),
                                    city_id: selectedCityId,
                                    lat    : address.lat,
                                    lng    : address.lng,
                                },
                            }).done((response) => {
                                if (validationId !== address.city_validation_request_id
                                    || !this.hasDestinationCity()
                                    || String(this.to_address.city.id) !== selectedCityId) {
                                    return;
                                }

                                address.city_validation_status = response.valid ? 'valid' : 'invalid';
                                address.destination_city_token = response.valid ? response.token : null;
                            }).fail(() => {
                                if (validationId === address.city_validation_request_id) {
                                    address.city_validation_status = 'error';
                                }
                            }).always(resolve);
                        }, wait);
                    }));
                },
                extractMapCoordinates: function(value) {
                    let decodedValue = String(value || '').trim();
                    try {
                        decodedValue = decodeURIComponent(decodedValue);
                    } catch (error) {}

                    const patterns = [
                        /^(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)$/,
                        /@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/,
                        /\/maps\/(?:search|place)\/(-?\d+(?:\.\d+)?),\+?(-?\d+(?:\.\d+)?)/,
                        /[?&](?:q|query|ll)=(-?\d+(?:\.\d+)?),\+?(-?\d+(?:\.\d+)?)/,
                        /!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/,
                    ];

                    for (const pattern of patterns) {
                        const match = decodedValue.match(pattern);
                        if (match) {
                            const lat = Number(match[1]);
                            const lng = Number(match[2]);
                            if (lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                                return { lat: lat, lng: lng };
                            }
                        }
                    }
                    return null;
                },
                applyMapCoordinates: function(coordinates, item = undefined) {
                    const latlng = `${coordinates.lat},${coordinates.lng}`;
                    if (item) {
                        item.lat = coordinates.lat;
                        item.lng = coordinates.lng;
                        item.latlng = latlng;
                        item.map.placeMarker(new google.maps.LatLng(coordinates.lat, coordinates.lng));
                        this.verifyAddressDestinationCity(item);
                    } else {
                        this.loading_address.lat = coordinates.lat;
                        this.loading_address.lng = coordinates.lng;
                        this.loading_address.latlng = latlng;
                        companyMap.placeMarker(new google.maps.LatLng(coordinates.lat, coordinates.lng));
                    }
                },
                setLatlng: function(value, item = undefined) {
                    const coordinates = this.extractMapCoordinates(value);
                    if (coordinates) {
                        this.applyMapCoordinates(coordinates, item);
                        return;
                    }

                    if (!/^https:\/\//i.test(String(value || '').trim())) {
                        return;
                    }

                    $.ajax({
                        url: @json(route('ShippmentsController@resolveGoogleMapsUrl')),
                        method: 'GET',
                        data: { url: String(value).trim() }
                    }).done((response) => {
                        const resolvedCoordinates = this.extractMapCoordinates(response.url);
                        if (resolvedCoordinates) {
                            this.applyMapCoordinates(resolvedCoordinates, item);
                            return;
                        }
                        NioApp.Toast(@json(__('contenttypes::strings.shippments.invalid_google_maps_link')), 'error', {ui: 'is-dark'});
                    }).fail(() => {
                        NioApp.Toast(@json(__('contenttypes::strings.shippments.invalid_google_maps_link')), 'error', {ui: 'is-dark'});
                    });
                },
                switchStep: function(step) {
                    this.step = step
                    HeaderApp.switchStep(step);
                    // scroll to top
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth' // or 'auto' for instant scroll
                    });
                },
                citySelected: function(selected, item = undefined) {
                    if (item) {
                        item.city = selected || null;
                    }
                    this.loading_address.city = selected;
                },
                toCitySelected: function(selected, item = undefined) {
                    this.to_address.city = selected || null;
                    this.to_addresses.forEach((address) => {
                        address.destination_city_token = null;
                        address.city_validation_status = address.lat !== null && address.lng !== null ? 'pending' : 'idle';
                    });

                    if (!this.hasDestinationCity()) {
                        this.destination_city_center = null;
                        this.destination_city_location_request_id++;
                        this.to_addresses.forEach((address) => {
                            address.city_validation_request_id++;
                            if (address.lat !== null && address.lng !== null) {
                                address.city_validation_status = 'invalid';
                            }
                        });
                        return;
                    }

                    this.focusDestinationCity();
                    if (!this.to_addresses.some((address) => address.removed === 'N')) {
                        this.addToAddress();
                    } else {
                        this.to_addresses
                            .filter((address) => address.removed === 'N' && address.lat !== null && address.lng !== null)
                            .forEach((address) => this.verifyAddressDestinationCity(address));
                    }
                },
                citySelectOptions: function(options) {
                    options.ajax = {
                        delay: 250,
                        url: function (params) {
                            return '{{ route('CitiesController@select') }}';
                        },
                        data: function (params) {
                            return {
                                nullable    : 'true',
                                search      : params.term,
                                page        : params.page || 1,
                                country_id  : 191
                            };
                        },
                        processResults: function (data) {
                            return data;
                        }
                    };
                },
                applyPreviousDeliveryAddress: function(address, selected) {
                    window.fillPreviousDeliveryAddress(this, address, selected);
                },
                addToAddress: function() {
                    if (!this.hasDestinationCity()) {
                        NioApp.Toast(@json(__('contenttypes::strings.shippments.select_destination_city_first')), 'warning', {ui: 'is-dark'});
                        return;
                    }
                    const notRemoved = this.to_addresses.filter(address => address.removed == 'N');
                    if (this.loading_type == 'CITY_TO_CITY' && notRemoved.length >= 1) {
                        return;
                    }
                    const el = 'geo_map_' + this.to_addresses.length + 1;
                    const address = {
                        el                           : el,
                        id                           : null,
                        primary_contact_company_name : null,
                        primary_contact_name         : null,
                        primary_contact_email        : null,
                        primary_contact_phone_number : null,
                        lat                          : null,
                        lng                          : null,
                        latlng                       : null,
                        round_trip                   : 'NO',
                        full_address                 : null,
                        public_notes                 : null,
                        private_notes                : null,
                        removed                      : 'N',
                        destination_city_token       : null,
                        city_validation_status       : 'idle',
                        city_validation_request_id   : 0,
                        map: () => {
                            address.map = createGeoMap({
                                el      : el,
                                lat     : this.destination_city_center
                                    ? this.destination_city_center.lat
                                    : Number('{{ config("address.map.lat") }}'),
                                lng     : this.destination_city_center
                                    ? this.destination_city_center.lng
                                    : Number('{{ config("address.map.lng") }}'),
                                zoom    : Number('{{ config("address.map.zoom") }}'),
                                selected: null,
                                onMapReady: () => {
                                    if (this.destination_city_center) {
                                        address.map.focusMap(
                                            this.destination_city_center.lat,
                                            this.destination_city_center.lng,
                                            10
                                        );
                                    }
                                    console.log('mapReady');
                                },
                                onReverseGeocode: (lat, lng, state) => {
                                    address.lat    = lat;
                                    address.lng    = lng;
                                    address.latlng = `${lat},${lng}`
                                    this.verifyAddressDestinationCity(address);
                                    console.log('deliveryAddress: onReverseGeocode', `${lat},${lng}`);
                                }
                            })
                        }
                    }

                    this.to_addresses.push(address)
                    this.$nextTick(() => {
                        address.map();
                        address.map.init();
                    })
                },
                removeToAddress: function(index) {
                    if (! confirm('{{ __('contenttypes::strings.shippments.remove_offloading_address_confirm') }}')) {
                        return;
                    }
                    if (this.to_addresses[index].removed == 'Y') {
                        return;
                    }
                    this.to_addresses[index].removed = 'Y';
                    this.to_addresses[index].destination_city_token = null;
                    this.to_addresses[index].city_validation_request_id++;
                },
                leaveOnlyTheFirstAddress: function() {
                    this.to_addresses.filter(address => address.removed == 'N').forEach((address, index) => {
                        if (index > 0) {
                            address.removed = 'Y';
                        } else {
                            address.removed = 'N';
                        } 
                    });
                },
                submit: function() {
                    if (!this.hasDestinationCity()) {
                        this.switchStep(2);
                        NioApp.Toast(@json(__('contenttypes::strings.shippments.select_destination_city_first')), 'warning', {ui: 'is-dark'});
                        return;
                    }

                    const locatedAddresses = this.to_addresses.filter((address) =>
                        address.removed === 'N' && address.lat !== null && address.lng !== null
                    );
                    if (locatedAddresses.some((address) => address.city_validation_status === 'pending')) {
                        this.switchStep(2);
                        NioApp.Toast(@json(__('contenttypes::strings.shippments.wait_for_address_city_check')), 'warning', {ui: 'is-dark'});
                        return;
                    }
                    const invalidAddress = locatedAddresses.find((address) => address.city_validation_status !== 'valid');
                    if (invalidAddress) {
                        this.switchStep(2);
                        const message = invalidAddress.city_validation_status === 'error'
                            ? @json(__('contenttypes::strings.shippments.address_city_check_failed'))
                            : @json(__('contenttypes::strings.shippments.address_outside_destination_city'));
                        NioApp.Toast(message, 'error', {ui: 'is-dark'});
                        return;
                    }

                    HeaderApp.setStepErrors({ 1: 0, 2: 0 });
                    this.$nextTick(() => {
                        $('#shippment_form').submit();
                    });
                }
            },
        });

        $('#shippment_form').on('bs:validation-errors', function(event, errors) {
            const $form = $(this);
            const stepErrors = { 1: 0, 2: 0 };
            let $firstStepOneError = null;
            let $firstStepTwoError = null;

            errors.forEach(function(error) {
                const inputName = error.field.replace(/\.(.+?)(?=\.|$)/g, (match, name) => `[${name}]`);
                const $input = $form.find("[name^='" + inputName + "']").first();
                const step = Number($input.closest('[data-shippment-step]').data('shippment-step'));

                if (step === 1 || step === 2) {
                    stepErrors[step]++;
                    if (step === 1 && ! $firstStepOneError) $firstStepOneError = $input;
                    if (step === 2 && ! $firstStepTwoError) $firstStepTwoError = $input;
                }
            });

            HeaderApp.setStepErrors(stepErrors);
            const targetStep = stepErrors[1] > 0 ? 1 : (stepErrors[2] > 0 ? 2 : CrudApp.step);
            const $targetInput = targetStep === 1 ? $firstStepOneError : $firstStepTwoError;
            CrudApp.switchStep(targetStep);
            CrudApp.$nextTick(function() {
                if ($targetInput && $targetInput.length) {
                    $targetInput[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    $targetInput.focus();
                }
            });
        });

        const slider = document.getElementById('weightRange');
        const wrapper = document.getElementById('weightSliderWrapper');

        function updateSliderDirection() {
            const min = +slider.min;
            const max = +slider.max;
            const val = +slider.value;
            const percent = ((val - min) / (max - min)) * 100;
            const dir = wrapper.getAttribute('dir');

            const gradientDir = dir === 'rtl' ? 'to left' : 'to right';
            slider.setAttribute(
                'style',
                `background: linear-gradient(${gradientDir}, #ff5a3c 0%, #ff5a3c ${percent}%, #e2e6f0 ${percent}%, #e2e6f0 100%);`
            );
        }

        slider.addEventListener('input', updateSliderDirection);
        updateSliderDirection(); // Initial render

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
        .on("select2:select", function() {});

        $(document).on('change', '[name=loading_type]', function() {
            console.log($(this).val());
            CrudApp.$data.loading_type = $(this).val();
            if (CrudApp.$data.loading_type == 'CITY_TO_CITY') {
                CrudApp.leaveOnlyTheFirstAddress();
            }
        });

        function createGeoMap(options) {
            const state = {
                el       : options.el,
                lat      : Number(options.lat ?? 25.2048),
                lng      : Number(options.lng ?? 55.2708),
                zoom     : Number(options.zoom ?? 10),
                selected : options.selected ?? null,
                map      : null,
                marker   : null,
            };

            const methods = {
                init: async function () {
                    const { Map } = await google.maps.importLibrary("maps");

                    state.map = new Map(document.getElementById(state.el), {
                        center: { lat: state.lat, lng: state.lng },
                        zoom  : state.zoom,
                    });

                    const locationButton = document.createElement('button');
                    locationButton.type = 'button';
                    locationButton.className = 'btn btn-white btn-icon shadow-sm m-2';
                    locationButton.title = @json(__('contenttypes::strings.shippments.current_location'));
                    locationButton.setAttribute('aria-label', @json(__('contenttypes::strings.shippments.current_location')));
                    locationButton.innerHTML = '<em class="icon ni ni-location"></em>';
                    locationButton.addEventListener('click', function(event) {
                        event.stopPropagation();
                        if (!navigator.geolocation) {
                            NioApp.Toast(@json(__('contenttypes::strings.shippments.location_unavailable')), 'error', {ui: 'is-dark'});
                            return;
                        }

                        let locationWatchId = null;
                        let locationWatchTimeout = null;
                        let locationRequestFinished = false;
                        const finishLocationRequest = function() {
                            if (locationRequestFinished) {
                                return false;
                            }

                            locationRequestFinished = true;
                            if (locationWatchId !== null) {
                                navigator.geolocation.clearWatch(locationWatchId);
                            }
                            if (locationWatchTimeout !== null) {
                                window.clearTimeout(locationWatchTimeout);
                            }
                            locationButton.disabled = false;
                            locationButton.removeAttribute('aria-busy');
                            return true;
                        };
                        const usePosition = function(position) {
                            if (!finishLocationRequest()) {
                                return;
                            }

                            methods.placeMarker(new google.maps.LatLng(
                                position.coords.latitude,
                                position.coords.longitude
                            ), true);
                        };
                        const showLocationError = function(error) {
                            if (!finishLocationRequest()) {
                                return;
                            }

                            const message = error && error.code === 1
                                ? @json(__('contenttypes::strings.shippments.location_permission_denied'))
                                : @json(__('contenttypes::strings.shippments.location_unavailable'));
                            NioApp.Toast(message, 'error', {ui: 'is-dark'});
                        };
                        const watchForPosition = function() {
                            locationWatchId = navigator.geolocation.watchPosition(usePosition, function(error) {
                                if (error.code === 1) {
                                    showLocationError(error);
                                }
                            }, {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0,
                            });
                            locationWatchTimeout = window.setTimeout(function() {
                                showLocationError(null);
                            }, 30000);
                        };

                        locationButton.disabled = true;
                        locationButton.setAttribute('aria-busy', 'true');
                        navigator.geolocation.getCurrentPosition(usePosition, function(error) {
                            if (error.code === 1) {
                                showLocationError(error);
                                return;
                            }

                            watchForPosition();
                        }, {
                            enableHighAccuracy: false,
                            timeout: 15000,
                            maximumAge: 300000,
                        });
                    });
                    state.map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(locationButton);

                    state.map.addListener("click", (e) => {
                        methods.placeMarker(e.latLng, true);
                    });

                    if (state.selected) {
                        methods.placeMarker(new google.maps.LatLng(
                            state.selected.lat,
                            state.selected.lng
                        ));
                    }

                    if (typeof options.onMapReady === 'function') {
                        options.onMapReady();
                    }
                },

                placeMarker: function (coordinates, runReverse = false) {
                    methods.clearMarker();

                    state.lat = coordinates.lat();
                    state.lng = coordinates.lng();
                    state.zoom = 18.25;
                    state.selected = { lat: state.lat, lng: state.lng };

                    state.marker = new google.maps.Marker({
                        position: coordinates,
                        map     : state.map,
                    });

                    state.map.panTo(coordinates);
                    state.map.setZoom(state.zoom);

                    if (runReverse && typeof options.onReverseGeocode === 'function') {
                        options.onReverseGeocode(state.lat, state.lng, state);
                    }
                },

                clearMarker: function () {
                    if (state.marker) {
                        state.marker.setMap(null);
                        state.marker = null;
                    }
                },

                focusMap: function (lat, lng, zoom = 15) {
                    state.map.panTo(new google.maps.LatLng(lat, lng));
                    state.map.setZoom(zoom);
                },

                getState: function () {
                    return { ...state };
                }
            };

            return methods;
        }

        $(document).ready(function() {
            companyMap = createGeoMap({
                el      : 'geo_map',
                lat     : Number(@json($loadingAddressDefaults['lat'] ?? (float) config('address.map.lat'))),
                lng     : Number(@json($loadingAddressDefaults['lng'] ?? (float) config('address.map.lng'))),
                zoom    : Number('{{ config("address.map.zoom") }}'),
                selected: @json($loadingAddressDefaults['map_selected']),
                onMapReady: () => {
                    // CrudApp.mapReady();
                    console.log('mapReady');
                },
                onReverseGeocode: (lat, lng) => {
                    // CrudApp.addressReverse(lat, lng);
                    // CrudApp.latlng = `${lat},${lng}`;
                    CrudApp.loading_address.latlng = `${lat},${lng}`
                    CrudApp.loading_address.lat    = lat;
                    CrudApp.loading_address.lng    = lng;
                    console.log('companyMap: onReverseGeocode', `${lat},${lng}`);
                }
            });

            companyMap.init();
        });

        $(document).on('click', '#save_as_draft', function() {
            CrudApp.$data.is_draft = 'Y';
            CrudApp.$data.is_rfq   = 'N';
            CrudApp.submit();
        });
        $(document).on('click', '#save_and_send', function() {
            CrudApp.$data.is_draft = 'N';
            CrudApp.$data.is_rfq   = 'N';
            CrudApp.submit();
        });
        $(document).on('click', '#save_and_send_rfq', function() {
            CrudApp.$data.is_rfq   = 'Y';
            CrudApp.$data.is_draft = 'N';
            CrudApp.submit();
        });
    </script>
@endpush
