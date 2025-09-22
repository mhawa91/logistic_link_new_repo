@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('contenttypes::strings.public-transportars.list'),
        'items' => [
            [
                'title' => __('contenttypes::strings.public-transportars.menu'),
                'link'  => route('PublicTransportarsController@index')
            ],
            [
                'title' => __('contenttypes::strings.public-transportars.list'),
                'link'  => route('PublicTransportarsController@index')
            ]
        ],
    ]
])
    
    <div class="nk-block-head-content" id="select-app">
        <div class="toggle-wrap nk-block-tools-toggle">
            <a href="javascript:;" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu">
                <em class="icon ni ni-menu-alt-r"></em>
            </a>
            <div class="toggle-expand-content" data-content="pageMenu">
                <ul class="nk-block-tools g-3">
                    @if ($Service->can('create'))
                        <li>
                            <a href="{{ route('TransportersController@create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em><span>{{ __('contenttypes::strings.contracts.create') }}</span>
                            </a>
                        </li>
                    @endif
                    <li v-if="selected.length > 0">
                        <form action="{{ route('PublicTransportarsController@postRfq') }}" method="POST" onsubmit="BS.AJAX_FORM(event);">
                            @csrf
                            <input type="hidden" name="shippment_id" value="{{ request()->suitable_for_shippment }}">
                            <input v-for="(item, item_index) in selected" type="hidden" name="transportar[]" :value="item">
                            <button type="submit" class="btn btn-danger">
                                <em class="icon ni ni-edit"></em><span>{{ __('contenttypes::strings.send_rfq_for_selected') }}</span>
                            </button>
                        </form>
                    </li>                
                </ul>
            </div>
        </div>
    </div>
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
                'size'      => 'md',
                'fit'       => false,
            ]
        ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'text',
                    'name'        => 'transportar_name',
                    'label'       => __('contenttypes::inputs.transportar_name.label'),
                    'placeholder' => __('contenttypes::inputs.transportar_name.placeholder'),
                ],
            ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'text',
                    'name'        => 'transportar_email',
                    'label'       => __('contenttypes::inputs.transportar_email.label'),
                    'placeholder' => __('contenttypes::inputs.transportar_email.placeholder'),
                ],
            ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'text',
                    'name'        => 'transportar_phone_number',
                    'label'       => __('contenttypes::inputs.transportar_phone_number.label'),
                    'placeholder' => __('contenttypes::inputs.transportar_phone_number.placeholder'),
                ],
            ])

            @include('ui::components.input', [
                'options' => [
                    'view'        => 'INLINE',
                    'type'        => 'hidden',
                    'name'        => 'suitable_for_shippment',
                    'label'       => __('contenttypes::inputs.transportar_email.label'),
                    'placeholder' => __('contenttypes::inputs.transportar_email.placeholder'),
                    'value'       => request()->suitable_for_shippment ?? null,
                ],
            ])
        
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
                'available' => false,
                'trashed'   => $Service->can('restore') || $Service->can('permaDelete'),
                'refresh'   => true,
                'export'    => false,
            ],
            'columns'      => [
                [
                    'label' => '<span class="sub-text">' . __('ui::inputs.id.label') . '</span>', 
                    'class' => ['fit']
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.transportar.label') . '</span>', 
                    'class' => []
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.contact_name.label') . '</span>',
                    'class' => [],
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.company_phone_number.label') . '</span>',
                    'class' => [],
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.country_id.label') . ' / ' . __('contenttypes::inputs.city_id.label') . '</span>',
                    'class' => ['fit'],
                ],
                [
                    'label' => '<span class="sub-text">' . __('contenttypes::inputs.reviews.label') . '</span>',
                    'class' => ['fit'],
                ],
                [
                    'label' => '<span class="sub-text">' . __('ui::inputs.created_at.label') . '</span>',
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
                        url  : "{!! route('PublicTransportarsController@postIndex') !!}",
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
                            // visible    : false,
                            render     : function(data,type,Row,meta) {
                                @if (! request()->suitable_for_shippment)
                                    return `<span class="text-danger">${Row.id}</span>`;
                                @endif
                                let isChecked = isSelected(Row.id);
                                return `
                                    <div class="custom-control custom-switch">
                                        <input name="selected[]" type="checkbox" class="custom-control-input public-transportar" id="customSwitch${Row.id}" value="${Row.id}" ${isChecked ? 'checked' : ''}>
                                        <label class="custom-control-label" for="customSwitch${Row.id}"></label>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : "id",
                            name       : 'id',
                            orderable  : true,
                            searchable : false,
                            visible    : true,
                            render     : function(data,type,Row,meta) {
                                let _img = '<span>AB</span>';
                                if (Row.logo) {
                                    _img = `<img src="${Row.logo}" alt="${Row.company_name}">`;
                                }
                                
                                // Check if company info should be hidden
                                let displayEmail = Row.company_email || '---------';
                                if (Row.hide_info === 'YES' && displayEmail !== '---------') {
                                    displayEmail = displayEmail.substring(0, 2) + '***';
                                }
                                
                                return `
                                    <div class="user-card">
                                        <div class="user-avatar sq sm bg-light">
                                            ${_img}
                                        </div>
                                        <div class="user-name">
                                            <span>${Row.company_name}</span>
                                            <br>
                                            <span dir="ltr">${displayEmail}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : "id",
                            name       : 'id',
                            orderable  : false,
                            searchable : false,
                            visible    : true,
                            render     : function(data,type,Row,meta) {
                                let displayName = Row.address?.primary_contact_name || '---------';
                                if (Row.hide_info === 'YES' && displayName !== '---------') {
                                    displayName = displayName.substring(0, 2) + '***';
                                }
                                return `
                                    <span>${displayName}</span>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : "id",
                            name       : 'id',
                            orderable  : true,
                            searchable : false,
                            visible    : true,
                            render     : function(data,type,Row,meta) {
                                let displayPhone = Row.full_phone_number || '---------';
                                if (Row.hide_info === 'YES' && displayPhone !== '---------') {
                                    displayPhone = displayPhone.substring(0, 2) + '***';
                                }
                                return `
                                    <span dir="ltr">${displayPhone}</span>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('defaultTableCellClass'),
                            data       : "id",
                            name       : 'id',
                            orderable  : false,
                            searchable : false,
                            visible    : true,
                            render     : function(data,type,Row,meta) {
                                return `
                                    <span>
                                        ${Row.address?.country_name || '---------'} / 
                                        ${Row.address?.city_name || '---------'}</span>    
                                    </span>
                                `;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "reviews_rate",
                            name       : 'reviews_rate',
                            render     : function(data,type,Row,meta) {
                                return `<div class="rating-wrap my-1 d-flex items-center">
                                    <ul class="rating">
                                        <li><em class="icon ni ${Row.reviews_rate >= 1 ? 'ni-star-fill' : 'ni-star'}"></em></li>
                                        <li><em class="icon ni ${Row.reviews_rate >= 2 ? 'ni-star-fill' : 'ni-star'}"></em></li>
                                        <li><em class="icon ni ${Row.reviews_rate >= 3 ? 'ni-star-fill' : 'ni-star'}"></em></li>
                                        <li><em class="icon ni ${Row.reviews_rate >= 4 ? 'ni-star-fill' : 'ni-star'}"></em></li>
                                        <li><em class="icon ni ${Row.reviews_rate >= 5 ? 'ni-star-fill' : 'ni-star'}"></em></li>
                                    </ul>
                                    <span class="amount mx-2">${Row.reviews_rate} {{ __('contenttypes::inputs.reviews.outof') }} 5</span>
                                    <span class="amount text-danger">(${Row.reviews_count})</span>
                                </div>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "created_at",
                            name       : 'created_at',
                            render     : function(data,type,Row,meta) {
                                return `<span dir="ltr">${Row.new_created_at || Row.created_at}</span>`;
                            }
                        },
                        {
                            className  : BS.CONFIG('lgTableCellClass'),
                            data       : "created_at",
                            name       : 'created_at',
                            orderable  : false,
                            searchable : false,
                            render     : function(data,type,Row,meta) {
                                return `
                                    <div class="btn-group-vertical" style="width: 100%;">
                                        <a href="{{ route('PublicTransportarsController@read') }}/${Row.id}" class="btn btn-sm btn-primary mb-1" target="_blank">
                                            <i class="fas fa-eye" style="margin-left: 5px; margin-right: 5px;"></i>
                                            <span>{{ __('contenttypes::strings.company_profile') }}</span>
                                        </a>
                                        <a href="{{ route('TransportersController@createByUserId') }}?user_id=${Row.user_id}" class="btn btn-sm btn-success">
                                            <i class="fas fa-file-contract" style="margin-left: 5px; margin-right: 5px;"></i>
                                            <span>{{ __('contenttypes::main.add_new_contract') }}</span>
                                        </a>
                                    </div>
                                `;
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
        const selectApp = new Vue({
            el: '#select-app',
            data: {
                selected: [],
            },
            methods: {
                isSelected: function (id) {
                    return this.selected.includes(id);
                },
                toggleSelect: function (id) {
                    if (this.isSelected(id)) {
                        this.selected = this.selected.filter(item => item !== id);
                    } 
                    else {
                        this.selected.push(id);
                    }
                    console.log(this.selected);
                },
            }
        });

        $(document).on('click', '.public-transportar', function() {
            const id = $(this).val();
            selectApp.toggleSelect(id);
        });

        const isSelected = function (id) {
            return selectApp.isSelected(id);
        }

        function exportData() {
            // create form using jquery and submit
            var form = $('<form action="{{ route('TransportersController@postExport') }}" method="POST" style="display: none;"></form>');

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