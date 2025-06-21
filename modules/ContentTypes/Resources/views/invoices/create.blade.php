@extends('ui::layouts.main', [
    'options' => [
        'width'  => 'full',
        'margin' => 'auto',
    ],
])

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.invoices.create'),
        'items' => [
            [
                'title' => __('contenttypes::strings.invoices.menu'),
                'link'  => route('InvoicesController@index')
            ],
            [
                'title' => __('contenttypes::strings.invoices.create'),
                'link'  => route('InvoicesController@create')
            ]
        ],
    ]
])
<style>
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endcomponent

@section('content')

    <div class="nk-block nk-block-lg" id="vue-form">
        @component('ui::components.form', [
            'options' => [
                'id'                => null,
                'action'            => route('InvoicesController@postCreate'),
                'method'            => 'POST',
                'class'             => '',
                'attributes'        => [
                    'onsubmit'                     => 'BS.AJAX_FORM(event)',
                    'data-keep-loading-on-success' => 'true',
                ],
            ],
        ])
            <div class="row mb-1">
                <div class="col-md-3">
                    @component('ui::components.input', [
                        'options' => [
                            'view'        => 'DEFAULT',
                            'type'        => 'text',
                            'name'        => 'invoice_number',
                            'label'       => __('contenttypes::inputs.invoice_number.label'),
                            'placeholder' => __('contenttypes::inputs.invoice_number.placeholder'),
                            'description' => __('contenttypes::inputs.invoice_number.description'),
                            'attributes'  => []
                        ],
                    ])
                    @endcomponent
                </div>
                <div class="col-md-3">
                    @include('ui::components.select', [
                        'options' => [
                            'id'                => 'client_id_select',
                            'jsSelect2'         => 'client_id_select2',
                            'view'              => 'DEFAULT',
                            'name'              => 'client_id',
                            'label'             => __('contenttypes::inputs.client.label'),
                            'placeholder'       => __('contenttypes::inputs.client.placeholder'),
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
                            'type'        => 'text',
                            'name'        => 'note',
                            'label'       => __('contenttypes::inputs.invoice_note.label'),
                            'placeholder' => __('contenttypes::inputs.invoice_note.placeholder'),
                            'description' => __('contenttypes::inputs.invoice_note.description'),
                            'attributes'  => []
                        ],
                    ])
                    @endcomponent
                </div>
            </div>

            <div class="row mb-1">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date" class="form-label">{{ __('contenttypes::inputs.invoiced_at.label') }}</label>
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-right">
                                <em class="icon ni ni-calendar-alt"></em>
                            </div>
                            <ui-datepicker name="invoiced_at" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></ui-datepicker>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date" class="form-label">{{ __('contenttypes::inputs.invoice_due_at.label') }}</label>
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-right">
                                <em class="icon ni ni-calendar-alt"></em>
                            </div>
                            <ui-datepicker name="due_at" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"></ui-datepicker>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-bordered mt-3" style="padding-top: 0 !important; padding-bottom: 0 !important;">
                <div class="table-responsive">
                    <table class="table table-tranx">
                        <thead>
                            <tr class="tb-tnx-head" style="display: table-row; text-overflow: ellipsis; white-space: nowrap">
                                <th>
                                    <span>{{ __('ui::inputs.id.label') }}</span>
                                </th>
                                <th>
                                    <span>{{ __('contenttypes::inputs.from.label') }}</span>
                                </th>
                                <th>
                                    <span>{{ __('contenttypes::inputs.to.label') }}</span>
                                </th>
                                <th>
                                    <span>{{ __('contenttypes::inputs.loading_type.label') }}</span>
                                </th>
                                <th>
                                    <span>{{ __('contenttypes::inputs.is_rfq.label') }}</span>
                                </th>
                                <th>
                                    <span>{{ __('contenttypes::inputs.sub_cost.label') }}</span>
                                </th>
                                <th>
                                    <span>{{ __('contenttypes::inputs.discount.label') }}</span>
                                </th>
                                <th>
                                    <span class="text-danger">{{ __('contenttypes::inputs.cost.label') }}</span>
                                </th>
                                <th class="tb-tnx-action">
                                    <span>&nbsp;</span>
                                </th>
                            </tr>
                        </thead>
                        <template v-if="items.length == 0">
                            <tbody>
                                <tr class="responsive-tr">
                                    <td colspan="9">
                                        <div class="text-center m-5">
                                            {{-- <em class="icon ni ni-list-index text-primary" style="font-size: 120px;"></em> --}}
                                            <em class="icon ni ni-file-docs text-primary" style="font-size: 120px;"></em>
                                            <div class="nk-block nk-block-lg">
                                                <div class="nk-block-head">
                                                    <div class="nk-block-head-content mt-4">
                                                        <h4 class="nk-block-title">{{ __('contenttypes::strings.invoice_items.no_items') }}</h4>
                                                        <p>{{ __('contenttypes::strings.invoice_items.no_items_desc') }}</p>
                                                        <button type="button" class="btn btn-md btn-primary" data-toggle="modal" data-target="#itemsModal">
                                                            <em class="icon ni ni-plus"></em>
                                                            <span>{{ __('contenttypes::strings.invoice_items.add_new') }}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </template>
                        <template v-if="items.length != 0">
                            <tbody>
                                <tr v-for="(item, item_index) in items" :dd="`item_${item_index}`">
                                    <td>
                                        @include('ui::components.input', [
                                            'options' => [
                                                'type'        => 'hidden',
                                                'name'        => 'shippment_id',
                                                'attributes'  => [
                                                    'readonly' => 'true',
                                                    'v-model'  => 'item.shippment.id',
                                                    ':name'   => "'items[' + item_index + '][shippment_id]'",
                                                ],
                                            ],
                                        ])
                                        <span class="text-danger">@{{ item.shippment.id }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <span>@{{item.shippment.from_city_name}}</span> <br>
                                            <strong class="text-primary">@{{item.shippment.from_country_name}}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>@{{item.shippment.to_city_name}}</strong> <br>
                                            <strong class="text-primary">@{{item.shippment.to_country_name}}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span v-if="item.shippment.type == 'CITY_TO_CITY'">
                                                <strong>{{ __('contenttypes::inputs.loading_type.options_short.CITY_TO_CITY') }}</strong> <br>
                                            </span>
                                            <span v-if="item.shippment.type == 'CITY_TO_CITY_MULTIPLE_LOCATIONS'">
                                                <strong>{{ __('contenttypes::inputs.loading_type.options_short.CITY_TO_CITY') }}</strong> 
                                                <small>({{ __('contenttypes::inputs.loading_type.options_short.MULTIPLE_LOCATIONS') }})</small>    
                                                <br>
                                            </span>
                                            <strong style="color: #FB4F28 !important;">@{{item.shippment.offloading_locations_count}} {{ __('contenttypes::inputs.loading_type.options_short.locations') }}</strong>
                                            <small v-if="item.shippment.round_trip == 'YES'" class="text-primary">({{ __('contenttypes::inputs.loading_type.options_short.ROUND_TRIP.YES') }})</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-md" :class="'badge-' + item.shippment.new_is_rfq_color">
                                            @{{ item.shippment.new_is_rfq ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="w-max-100px">
                                        @component('ui::components.input', [
                                            'options' => [
                                                'class'       => 'text-danger',
                                                'view'        => 'TABLE',
                                                'type'        => 'hidden',
                                                'name'        => 'sub_cost',
                                                'placeholder' => '0.00',
                                                'attributes'  => [
                                                    'readonly' => 'true',
                                                    'v-model'  => 'item.sub_cost',
                                                    ':name'   => "'items[' + item_index + '][sub_cost]'",
                                                ],
                                            ],
                                        ])
                                            <x-slot name="after">
                                                <div class="form-text-hint">
                                                    <small class="overline-title">SAR</small>
                                                </div>
                                            </x-slot>
                                        @endcomponent
                                        <div>
                                            <strong class="text-danger">@{{item.sub_cost}}</strong> SAR
                                        </div>
                                    </td>
                                    <td class="w-min-100px w-max-100px">
                                        @component('ui::components.input', [
                                            'options' => [
                                                'class'       => 'text-success',
                                                'view'        => 'TABLE',
                                                'type'        => 'number',
                                                'name'        => 'discount',
                                                'placeholder' => '0.00',
                                                'attributes'  => [
                                                    '@input' => 'enforceMax(item, item.sub_cost)',
                                                    'v-model' => 'item.discount',
                                                    ':name'   => "'items[' + item_index + '][discount]'",
                                                ],
                                            ],
                                        ])
                                            <x-slot name="after">
                                                <div class="form-text-hint">
                                                    <small class="overline-title">SAR</small>
                                                </div>
                                            </x-slot>
                                        @endcomponent
                                    </td>
                                    <td class="w-max-100px">
                                        @component('ui::components.input', [
                                            'options' => [
                                                'class'       => 'text-danger',
                                                'view'        => 'TABLE',
                                                'type'        => 'hidden',
                                                'name'        => 'cost',
                                                'placeholder' => '0.00',
                                                'attributes'  => [
                                                    'readonly' => 'true',
                                                    'v-model'  => 'item.cost',
                                                    ':name'   => "'items[' + item_index + '][cost]'",
                                                ],
                                            ],
                                        ])
                                            <x-slot name="after">
                                                <div class="form-text-hint">
                                                    <small class="overline-title">SAR</small>
                                                </div>
                                            </x-slot>
                                        @endcomponent
                                        <div>
                                            <strong class="text-danger">@{{item.cost}}</strong> SAR
                                        </div>
                                    </td>
                                    <td class="tb-tnx-action">
                                        <button type="button" class="text-soft btn btn-icon btn-trigger" v-on:click="removeItem(item_index)">
                                            <em class="text-danger icon ni ni-trash"></em>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="tb-tnx-head">
                                    <td colspan="5"></td>
                                    <th colspan="1" class="text-muted">{{ __('contenttypes::inputs.invoice_sub_total.label') }} (<span class="text-danger">DR</span>)</th>
                                    <th colspan="1" class="text-muted">{{ __('contenttypes::inputs.invoice_discounts_total.label') }} (<span class="text-success">CR</span>)</th>
                                    <th colspan="1" class="text-muted">{{ __('contenttypes::inputs.invoice_total.label') }} (<span class="text-danger">DR</span>)</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                    <td colspan="1">
                                        @component('ui::components.input', [
                                            'options' => [
                                                'class'       => 'text-danger',
                                                'view'        => 'TABLE',
                                                'type'        => 'hidden',
                                                'name'        => 'sub_total',
                                                'placeholder' => '0.00',
                                                'attributes'  => [
                                                    'readonly' => 'true',
                                                    ':value'   => 'items.reduce((acc, item) => acc + Number(item.sub_cost), 0)',
                                                    ':name'    => "'sub_total'",
                                                ],
                                            ],
                                        ])
                                            <x-slot name="after">
                                                <div class="form-text-hint">
                                                    <small class="overline-title">SAR</small>
                                                </div>
                                            </x-slot>
                                        @endcomponent
                                        <div>
                                            <strong class="text-danger">@{{items.reduce((acc, item) => acc + Number(item.sub_cost), 0)}}</strong> SAR
                                        </div>
                                    </td>
                                    <td colspan="1">
                                        @component('ui::components.input', [
                                            'options' => [
                                                'class'       => 'text-success',
                                                'view'        => 'TABLE',
                                                'type'        => 'hidden',
                                                'name'        => 'discounts_total',
                                                'placeholder' => '0.00',
                                                'attributes'  => [
                                                    'readonly' => 'true',
                                                    ':value'  => 'items.reduce((acc, item) => acc + Number(item.discount), 0)',
                                                    ':name'   => "'discounts_total'",
                                                ],
                                            ],
                                        ])
                                            <x-slot name="after">
                                                <div class="form-text-hint">
                                                    <small class="overline-title">SAR</small>
                                                </div>
                                            </x-slot>
                                        @endcomponent
                                        <div>
                                            <strong class="text-success">@{{items.reduce((acc, item) => acc + Number(item.discount), 0)}}</strong> SAR
                                        </div>
                                    </td>
                                    <td colspan="1">
                                        @component('ui::components.input', [
                                            'options' => [
                                                'class'       => 'text-danger',
                                                'view'        => 'TABLE',
                                                'type'        => 'hidden',
                                                'name'        => 'total',
                                                'placeholder' => '0.00',
                                                'attributes'  => [
                                                    'readonly' => 'true',
                                                    ':value'  => 'items.reduce((acc, item) => acc + (Number(item.sub_cost) - Number(item.discount)), 0)',
                                                    ':name'   => "'total'",
                                                ],
                                            ],
                                        ])
                                            <x-slot name="after">
                                                <div class="form-text-hint">
                                                    <small class="overline-title">SAR</small>
                                                </div>
                                            </x-slot>
                                        @endcomponent
                                        <div>
                                            <strong class="text-danger">@{{items.reduce((acc, item) => acc + (Number(item.sub_cost) - Number(item.discount)), 0)}}</strong> SAR
                                        </div>
                                    </td>
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
            
            <div class="form-group mt-2">
                <button type="submit" class="btn btn-md btn-primary">
                    <span>{{ __('ui::strings.save') }}</span>
                </button>
            </div>
        @endcomponent
    </div>

    @component('ui::components.modal', [
        'options' => [
            'id'        => 'itemsModal',
            'title'     => __('contenttypes::strings.invoice_items.add_new'),
            'size'      => 'xl',
            'fit'       => true,
        ]
    ])

        @component('ui::components.datatable', [
            'options' => [
                'id'           => null,
                'callable'     => 'datatableTools',
                'jsDatatable'  => 'datatable',
                'searchModal'  => false,
                'attributes'   => [],
                'class'        => [],
                'size'         => 'compact',
                'tools'        => [
                    'search'    => true,
                    'available' => true,
                    'trashed'   => false,
                    'refresh'   => true,
                    'export'    => false,
                ],
                'columns'      => [
                    [
                        'label' => '<span class="sub-text">' . __('ui::inputs.id.label') . '</span>', 
                        'class' => ['fit']
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('contenttypes::inputs.from.label') . '</span>', 
                        'class' => []
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('contenttypes::inputs.to.label') . '</span>', 
                        'class' => []
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('contenttypes::inputs.loading_type.label') . '</span>', 
                        'class' => ['fit']
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('contenttypes::inputs.shippment_total_price.label') . '</span>', 
                        'class' => ['fit']
                    ],
                    [
                        'label' => '<span class="sub-text">' . __('contenttypes::inputs.is_rfq.label') . '</span>',
                        'class' => ['fit'],
                    ],
                    [
                        'label' => '',
                        'class' => ['nk-tb-col-tools', 'text-right', 'fit'],
                    ]
                ],
            ]
        ])
            <x-slot name="script">
                <script>
                    var datatableTools = BS.loadCallable('datatableTools');
                    var datatable      = BS.DATATABLE('.bs-datatable', {
                        ajax : {
                            url  : "{!! route('ShippmentsController@postIndex') !!}",
                            type : "POST",
                            data : function(data){
                                $('#searchModal').find('.close').first().click();
                                data.records_type = datatableTools.getRecordsType();
                                data._token       = `{!! csrf_token() !!}`;
                                data.advanced_search = $('#datatable-advanced-search-form').first().serializeObject();
                                data.client_id       = $('#client_id_select').val();
                                data.force_client    = 'YES';
                                data.force_invoice   = 'YES';
                                return data;
                            }
                        },
                        responsive: {
                            details  : true,
                            language : dataTableTranslations
                        },
                        searching    : true,
                        bLengthChange: false,
                        processing   : true,
                        serverSide   : true,
                        pageLength   : 50,
                        order        :  [],
                        drawCallback: function () {
                            $('.bs-datatable tbody tr').addClass('nk-tb-item');
                            $('.bs-datatable-card .pagination').addClass('pagination-sm');
                        },
                        columns: [
                            {
                                className  : BS.CONFIG('defaultTableCellClass'),
                                data       : 'id',
                                name       : 'id',
                                orderable  : true,
                                searchable : true,
                                // visible    : false,
                                render     : function(data,type,Row,meta) {
                                    let isChecked = isSelected(Row.id);
                                    return `
                                        <div class="custom-control custom-switch">
                                            <input name="selected[]" type="checkbox" class="custom-control-input select-shippment" id="customSwitch${Row.id}" value="${Row.id}" ${isChecked ? 'checked' : ''}>
                                            <label class="custom-control-label" for="customSwitch${Row.id}"></label>
                                        </div>
                                    `;
                                }
                            },
                            {
                                className  : BS.CONFIG('defaultTableCellClass'),
                                data       : 'from_city_name',
                                name       : 'from_city_name',
                                orderable  : true,
                                searchable : true,
                                // visible    : false,
                                render     : function(data,type,Row,meta) {
                                    return `<div>
                                        <span>${Row.from_city_name}</span> <br>
                                        <strong class="text-primary">${Row.from_country_name}</strong>
                                    </div>`;
                                }
                            },
                            {
                                className  : BS.CONFIG('defaultTableCellClass'),
                                data       : 'to_city_name',
                                name       : 'to_city_name',
                                orderable  : true,
                                searchable : true,
                                // visible    : false,
                                render     : function(data,type,Row,meta) {
                                    return `<div>
                                        <strong>${Row.to_city_name}</strong> <br>
                                        <strong class="text-primary">${Row.to_country_name}</strong>
                                    </div>`;
                                }
                            },
                            {
                                className  : BS.CONFIG('defaultTableCellClass'),
                                data       : 'type',
                                name       : 'type',
                                orderable  : true,
                                searchable : true,
                                // visible    : false,
                                render     : function(data,type,Row,meta) {
                                    var label     = '';
                                    var roundTrip = '';
                                    if (Row.type == 'CITY_TO_CITY') {
                                        label = '<strong>{{ __('contenttypes::inputs.loading_type.options_short.CITY_TO_CITY') }}</strong>'; 
                                    }
                                    if (Row.type == 'CITY_TO_CITY_MULTIPLE_LOCATIONS') {
                                        label = `
                                            <strong>{{ __('contenttypes::inputs.loading_type.options_short.CITY_TO_CITY') }}</strong> 
                                            <small>({{ __('contenttypes::inputs.loading_type.options_short.MULTIPLE_LOCATIONS') }})</small>
                                        `; 
                                    }
                                    if (Row.round_trip == 'YES') {
                                        roundTrip = '<small class="text-primary">({{ __('contenttypes::inputs.loading_type.options_short.ROUND_TRIP.YES') }})</small>'; 
                                    }
                                    return `<div>
                                        ${label} <br>
                                        <strong style="color: #FB4F28 !important;">${Row.offloading_locations_count} {{ __('contenttypes::inputs.loading_type.options_short.locations') }}</strong>
                                        ${roundTrip}
                                    </div>`;
                                }
                            },
                            {
                                className  : BS.CONFIG('defaultTableCellClass'),
                                data       : 'total_price',
                                name       : 'total_price',
                                orderable  : true,
                                searchable : true,
                                // visible    : false,
                                render     : function(data,type,Row,meta) {
                                    return `<div>
                                        <strong>${Row.formated_total_price}</strong> SAR
                                    </div>`;
                                }
                            },
                            {
                                className  : BS.CONFIG('lgTableCellClass'),
                                data       : "new_is_rfq",
                                name       : 'new_is_rfq',
                                render     : function(data,type,Row,meta) {
                                    return `<span class="badge badge-md badge-${Row.new_is_rfq_color}">${Row.new_is_rfq ?? 'N/A'}</span>`;
                                }
                            },
                            {
                                className      : BS.CONFIG('actionsTableCellClass'),
                                data           : "actions",
                                defaultContent : '',
                                orderable      : false,
                                searchable     : false,
                                render     : function(data,type,Row,meta) {
                                    var actions = ``;
                                    if (Row.actions) {
                                        actions += `
                                            <ul class="nk-tb-actions gx-2">
                                                <li>
                                                    <div class="dropdown">
                                                        <a href="javascript:;" class="btn btn-sm btn-icon btn-trigger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                            <em class="icon ni ni-more-h"></em>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <ul class="link-list-plain no-bdr sm">
                                                                ${(! Row.actions['read']        ? `` : `<li><a href="${Row.actions['read']['action']}"><em class="icon ni ni-eye"></em><span>${Row.actions['read']['label']}</span></a></li>`)}
                                                                ${(! Row.actions['rfq_list']    ? `` : `<li><a href="${Row.actions['rfq_list']['action']}"><em class="icon ni ni-list"></em><span>${Row.actions['rfq_list']['label']}</span></a></li>`)}
                                                                ${(! Row.actions['update']      ? `` : `<li><a href="${Row.actions['update']['action']}"><em class="icon ni ni-update"></em><span>${Row.actions['update']['label']}</span></a></li>`)}
                                                                <li class="divider"></li>
                                                                ${(! Row.actions['delete']      ? `` : `<li><a href="javascript:;" onclick="BS.ajaxDelete('{{ route('ShippmentsController@postDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete"></em><span>${Row.actions['delete']['label']}</span></a></li>`)}
                                                                ${(! Row.actions['restore']     ? `` : `<li><a href="javascript:;" onclick="BS.ajaxRestore('{{ route('ShippmentsController@postRestore') }}', ${Row.id}, datatable)"><em class="icon ni ni-redo"></em><span>${Row.actions['restore']['label']}</span></a></li>`)}
                                                                ${(! Row.actions['permaDelete'] ? `` : `<li><a href="javascript:;" onclick="BS.ajaxPermaDelete('{{ route('ShippmentsController@postPermaDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete text-danger"></em><span>${Row.actions['permaDelete']['label']}</span></a></li>`)}
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        `;
                                    }
                                    return actions;
                                }
                            },
                        ],
                    });
                </script>
            </x-slot>
        @endcomponent
        
        <x-slot name="footer">
            <button type="button" class="btn btn-sm btn-success" data-dismiss="modal">
                <span>{{ __('contenttypes::strings.contract_items.save') }}</span>
            </button>
        </x-slot>
    @endcomponent

@endsection

@push('script')
    <script>
        const CrudApp = new Vue({
            el: '#vue-form',
            data: {
                items   : [],
                selected: [],
            },
            methods: {
                enforceMax: function(item, _max) {
                    const max = Number(_max)
                    const val = Number(item.discount)

                    if (val > max) {
                        item.discount = max
                    }
                },
                addItem(shippment) {
                    this.items.push({
                        shippment: shippment,
                        sub_cost : Number(shippment?.total_price || 0),
                        discount : 0.00,
                        cost     : Number(shippment?.total_price || 0),
                    });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                isSelected: function (id) {
                    return this.selected.includes(id);
                },
                toggleSelect: function (data_row) {
                    if (this.isSelected(data_row?.id)) {
                        this.items    = this.items.filter(item => item.shippment.id !== data_row.id);
                        this.selected = this.selected.filter(item => item !== data_row.id);
                    } 
                    else {
                        this.selected.push(data_row.id);
                        this.addItem(data_row);
                    }
                    console.log(this.items);
                },
                clear: function () {
                    this.items    = [];
                    this.selected = [];
                },
                refreshTable: function () {
                    datatable.ajax.reload();
                },
            },
        });

        $(document).on('click', '.select-shippment', function() {
            var data_row = datatable.row($(this).closest('tr')).data()
            CrudApp.toggleSelect(data_row);
        });

        const isSelected = function (id) {
            return CrudApp.isSelected(id);
        }

        var client_id_select2 = $('#client_id_select').select2({
            ajax: {
                delay: 250,
                url: function (params) {
                    return '{{ route('InvoicesController@select') }}';
                },
                data: function (params) {
                    var query = {
                        search                 : params.term,
                        page                   : params.page || 1,
                        has_uninvoiced_invoices: 'YES',
                        transportar_id         : '{{ auth()->id() }}',
                    }
                    return query;
                },
                processResults: function (data) {
                    return data;
                }
            }
        })
        .on("select2:select", function() {
            var data = $(this).select2('data')[0];
            CrudApp.clear();
            CrudApp.refreshTable();
        });
    </script>
@endpush