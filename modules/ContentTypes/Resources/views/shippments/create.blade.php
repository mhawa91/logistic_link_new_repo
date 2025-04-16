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
    <div class="nk-block-head-content">
        <div class="toggle-wrap nk-block-tools-toggle">
            <a href="javascript:;" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu">
                <em class="icon ni ni-menu-alt-r"></em>
            </a>
            <div class="toggle-expand-content" data-content="pageMenu">
                <ul class="nk-block-tools g-3">
                    <li>
                        <div class="stepper">
                            <div class="step active">
                                <div class="circle">1</div>
                                <div class="label">{{ __('contenttypes::strings.shippments.steps.step1') }}</div>
                            </div>
                            <div class="arrow">›</div>
                            <div class="step">
                                <div class="circle inactive">2</div>
                                <div class="label text-muted">{{ __('contenttypes::strings.shippments.steps.step2') }}</div>
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
                    'id'                => null,
                    'action'            => route('ShippmentsController@postCreate'),
                    'method'            => 'POST',
                    'class'             => '',
                    'attributes'        => [
                        'onsubmit'                     => 'BS.AJAX_FORM(event)',
                        'data-keep-loading-on-success' => 'true',
                    ],
                ],
            ])

                <div>
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
                                <label class="form-label">{{ __('contenttypes::inputs.weight.label') }}</label>
                                <div class="slider-wrapper">
                                    <span class="min">0<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
                                    <input
                                        type="range"
                                        min="0"
                                        max="27"
                                        value="5"
                                        class="slider"
                                        id="weightRange"
                                    />
                                    <span class="max">27<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
                                </div>
                                <div class="marks">
                                    <span>5<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
                                    <span>10<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
                                    <span>20<span class="unit">{{ __('contenttypes::inputs.weight.tons') }}</span></span>
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
                                    'step'       => '1',
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
                                    'name'        => 'ADDITIONAL_DATA',
                                    'label'       => __('contenttypes::inputs.attachment_1.label'),
                                    'description' => __('contenttypes::inputs.attachment_1.description'),
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

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-md btn-primary">
                        <span>{{ __('ui::strings.next') }}</span>
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
    </script>
@endpush