@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.clients.list'),
        'items' => [
            [
                'title' => __('contenttypes::strings.clients.menu'),
                'link'  => route('InvoicesController@groupedIndex')
            ],
            [
                'title' => __('contenttypes::strings.clients.list'),
                'link'  => route('InvoicesController@groupedIndex')
            ]
        ],
    ]
])
    @if ($Service->can('create'))
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="javascript:;" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu">
                    <em class="icon ni ni-menu-alt-r"></em>
                </a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="{{ route('InvoicesController@create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em><span>{{ __('contenttypes::strings.invoices.create') }}</span>
                            </a>
                        </li>
                        {{-- <li class="nk-block-tools-opt">
                            <a href="#" class="btn btn-white btn-outline-light">
                                <em class="icon ni ni-download-cloud"></em><span>Export</span>
                            </a>
                        </li> --}}
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endcomponent

@section('content')

    @component('ui::components.form', [
        'options' => [
            'id'                => 'datatable-advanced-search-form',
            'action'            => 'javascript:;',
            'method'            => 'POST',
            'class'             => '',
            'attributes'        => [
                'onsubmit'                     => 'datatable.ajax.reload()',
                'data-keep-loading-on-success' => 'false',
            ],
        ],
    ])

        @component('ui::components.modal', [
            'options' => [
                'id'        => 'searchModal',
                'title'     => __('ui::strings.advanced_filter'),
                'size'      => '',
                'fit'       => false,
            ]
        ])
            <hr class="preview-hr">
            <x-slot name="footer">
                <button type="submit" class="btn btn-sm btn-success">
                    <span>{{ __('ui::strings.search') }}</span>
                </button>

                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger" onclick="BS.resetForm(this); datatable.ajax.reload();">
                    <span>{{ __('ui::strings.cancel') }}</span>
                </button>
            </x-slot>
        @endcomponent
    @endcomponent

    @component('ui::components.datatable', [
        'options' => [
            'id'           => null,
            'callable'     => 'datatableTools',
            'jsDatatable'  => 'datatable',
            'searchModal'  => 'searchModal',
            'attributes'   => [],
            'class'        => [],
            'size'         => 'compact',
            'tools'        => [
                'search'    => true,
                'available' => true,
                'trashed'   => false,
                'refresh'   => true,
                'export'    => 'exportData()',
            ],
            'columns'      => [
                [
                    'label' => '<span class="sub-text">' . __('ui::inputs.id.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.client.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.transportar.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::strings.finance.shippments_uninvoiced_count') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::strings.finance.shippments_uninvoiced_total') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::strings.finance.shippments_invoiced_count') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::strings.finance.shippments_invoiced_total') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::strings.finance.invoices_dues_count') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::strings.finance.invoices_dues_total') . '</span>', 
                    'class' => ['fit']
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
                        url  : "{!! route('InvoicesController@postGroupedIndexIndex') !!}",
                        type : "POST",
                        data : function(data){
                            $('#searchModal').find('.close').first().click();
                            data.records_type = datatableTools.getRecordsType();
                            data._token       = `{!! csrf_token() !!}`;
                            data.advanced_search = $('#datatable-advanced-search-form').first().serializeObject();
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
                            visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<span class="text-danger">${Row.id}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'client_company_name',
                            name       : 'client_company_name',
                            orderable  : true,
                            searchable : true,
                            visible    : {{ auth()->check() && (auth()->user()->type == 'TRANSPORTER' || ! in_array(auth()->user()->type, ['TRANSPORTER', 'DEFAULT'])) ? 'true' : 'false' }},
                            render     : function(data,type,Row,meta) {
                                return `
                                    <div class="user-card">
                                        <div class="user-avatar sq sm bg-light">
                                            <img src="${Row.client_company_logo}" alt="${Row.client_company_name}">
                                        </div>
                                        <div class="user-name">
                                            <span>${Row.client_company_name}</span>
                                            <br>
                                            <span class="text-muted" dir="ltr">${Row.client_company_email || Row.client_user_email || '---------'}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'transportar_company_name',
                            name       : 'transportar_company_name',
                            orderable  : true,
                            searchable : true,
                            visible    : {{ auth()->check() && (auth()->user()->type == 'DEFAULT' || ! in_array(auth()->user()->type, ['TRANSPORTER', 'DEFAULT'])) ? 'true' : 'false' }},
                            render     : function(data,type,Row,meta) {
                                return `
                                    <div class="user-card">
                                        <div class="user-avatar sq sm bg-light">
                                            <img src="${Row.transportar_company_logo}" alt="${Row.transportar_company_name}">
                                        </div>
                                        <div class="user-name">
                                            <span>${Row.transportar_company_name}</span>
                                            <br>
                                            <span class="text-muted" dir="ltr">${Row.transportar_company_email || Row.transportar_user_email || '---------'}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'shippments_uninvoiced_count',
                            name       : 'shippments_uninvoiced_count',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<strong class="text-danger">${Row.formated_shippments_uninvoiced_count}</strong>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'shippments_uninvoiced_total',
                            name       : 'shippments_uninvoiced_total',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<strong><span class="text-danger">${Row.formated_shippments_uninvoiced_total}</span> SAR</strong>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'shippments_invoiced_count',
                            name       : 'shippments_invoiced_count',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<strong class="text-success">${Row.formated_shippments_invoiced_count}</strong>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'shippments_invoiced_count',
                            name       : 'shippments_invoiced_count',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<strong><span class="text-success">${Row.formated_shippments_invoiced_total}</span> SAR</strong>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'invoices_dues_count',
                            name       : 'invoices_dues_count',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<strong class="text-warning">${Row.formated_invoices_dues_count}</strong>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : 'invoices_dues_count',
                            name       : 'invoices_dues_count',
                            orderable  : true,
                            searchable : true,
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                return `<strong><span class="text-warning">${Row.formated_invoices_dues_total}</span> SAR</strong>`;
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
                                                            ${(! Row.actions['update']      ? `` : `<li><a href="${Row.actions['update']['action']}"><em class="icon ni ni-update"></em><span>${Row.actions['update']['label']}</span></a></li>`)}
                                                            <li class="divider"></li>
                                                            ${(! Row.actions['delete']      ? `` : `<li><a href="javascript:;" onclick="BS.ajaxDelete('{{ route('InvoicesController@postDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete"></em><span>${Row.actions['delete']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['restore']     ? `` : `<li><a href="javascript:;" onclick="BS.ajaxRestore('{{ route('InvoicesController@postRestore') }}', ${Row.id}, datatable)"><em class="icon ni ni-redo"></em><span>${Row.actions['restore']['label']}</span></a></li>`)}
                                                            ${(! Row.actions['permaDelete'] ? `` : `<li><a href="javascript:;" onclick="BS.ajaxPermaDelete('{{ route('InvoicesController@postPermaDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete text-danger"></em><span>${Row.actions['permaDelete']['label']}</span></a></li>`)}
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
@endsection

@push('script')
    <script>
        function exportData() {
            // create form using jquery and submit
            var form = $('<form action="{{ route('InvoicesController@postExport') }}" method="POST" style="display: none;"></form>');

            let data = {};
            data.advanced_search = $('#datatable-advanced-search-form').first().serializeObject();

            form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            form.append('<input type="hidden" name="records_type" value="'+ datatableTools.getRecordsType() +'">');

            // append inputs to form from data object
            for (let key in data.advanced_search) {
                form.append(`<input type="hidden" name="advanced_search[${key}]" value="${data[key] || ''}">`);
            }
            form.appendTo('body').submit().remove();
        }
    </script>
@endpush