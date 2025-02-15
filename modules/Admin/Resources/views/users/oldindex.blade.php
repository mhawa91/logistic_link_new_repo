@extends('ui::layouts.main')

@component('ui::components.breadcrumb', [
    'options' => [
        'title' => __('admin::strings.users.list'),
        'items' => [
            [
                'title' => __('admin::strings.users.menu'),
                'link'  => route('UsersController@index')
            ],
            [
                'title' => __('admin::strings.users.list'),
                'link'  => route('UsersController@index')
            ]
        ],
    ]
])
    @if ($Service->can('create'))        
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu">
                    <em class="icon ni ni-menu-alt-r"></em>
                </a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li>
                            <a href="{{ route('UsersController@create') }}" class="btn btn-primary">
                                <em class="icon ni ni-plus"></em><span>{{ __('admin::strings.users.create') }}</span>
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

            <div class="row">
                <div class="col-md-12">
                    @include('ui::components.select', [
                        'options' => [
                            'view'              => 'INLINE',
                            'name'              => 'type',
                            'label'             => __('admin::inputs.type.label'),
                            'placeholder'       => __('admin::inputs.type.placeholder'),
                            'description'       => __('admin::inputs.type.description'),
                            'attributes'        => [],
                            'value'             => '0',
                            'search'            => true,
                            'multiple'          => true,
                            'nullable'          => true,
                            'nullable_value'    => '0',
                            'options'           => $Roles,
                            'option_text'       => function($key, $option)                   { return $option->__('title', app()->getLocale()); },
                            'option_value'      => function($key, $option)                   { return $option->name; },
                            'option_attributes' => function($key, $option)                   { return []; },
                            'selected'          => function($options, $key, $option, $value) { return $option->name == $value; },
                            'disabled'          => function($options, $key, $option, $value) { return false; },
                        ],
                    ])
                </div>
            </div>
            <hr class="preview-hr">
            <div class="row">
                <div class="col-md-12">
                    @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'name',
                            'label'       => __('admin::inputs.name.label'),
                            'description' => __('admin::inputs.name.description'),
                        ],
                    ])

                    @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'username',
                            'label'       => __('admin::inputs.username.label'),
                            'description' => __('admin::inputs.username.description'),
                        ],
                    ])

                    @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'email',
                            'label'       => __('admin::inputs.email.label'),
                            'description' => __('admin::inputs.email.description'),
                        ],
                    ])

                    @include('ui::components.input', [
                        'options' => [
                            'view'        => 'INLINE',
                            'type'        => 'text',
                            'name'        => 'phone_number',
                            'label'       => __('admin::inputs.phone_number.label'),
                            'description' => __('admin::inputs.phone_number.description'),
                        ],
                    ])
                </div>
            </div>

            
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

    <div class="card card-bordered card-stretch">
        <div class="card-inner-group">
            <div class="card-inner position-relative card-tools-toggle">
                <div class="card-title-group">
                    <div class="card-tools">
                        {{-- <div class="btn-wrap">
                            <span class="d-none d-md-block">
                                <button class="btn btn-sm btn-dim btn-outline-light">
                                    <em class="fas fa-sync m-1"></em> Refresh
                                </button>
                            </span>
                            <span class="d-md-none">
                                <button class="btn btn-dim btn-outline-light btn-icon">
                                    <em class="icon fas fa-sync"></em>
                                </button>
                            </span>
                        </div> --}}
                    </div>
                    <div class="card-tools mr-n1">
                        <ul class="btn-toolbar gx-1">
                            <li>
                                <a href="javascript:;" class="btn btn-icon search-toggle toggle-search" data-target="search" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.search') }}">
                                    <em class="icon ni ni-search"></em>
                                </a>
                            </li>
                            <li>
                                <div data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.advanced_filter') }}">
                                    <a href="javascript:;" class="btn btn-trigger btn-icon" data-toggle="modal" data-target="#searchModal">
                                        <em class="icon ni ni-filter-alt"></em>
                                    </a>
                                </div>
                            </li>
                            <li class="btn-toolbar-sep"></li>
                            <li>
                                <div class="toggle-wrap">
                                    <a href="javascript:;" class="btn btn-icon btn-trigger toggle" data-target="cardTools">
                                        <em class="icon ni ni-menu-right"></em>
                                    </a>
                                    <div class="toggle-content" data-content="cardTools">
                                        <ul class="btn-toolbar gx-1">
                                            <li class="toggle-close">
                                                <a href="javascript:;" class="btn btn-icon btn-trigger toggle" data-target="cardTools">
                                                    <em class="icon ni ni-arrow-left"></em>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;" onclick="setRecordsType('AVAILABLE')" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.available') }}">
                                                    <em class="icon ni ni-list"></em>
                                                </a>
                                            </li>
                                            @if ($Service->can('restore') || $Service->can('permaDelete'))
                                                <li>
                                                    <a href="javascript:;" onclick="setRecordsType('TRASHED')" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.trashed') }}">
                                                        <em class="icon ni ni-trash"></em>
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a href="javascript:;" onclick="BS.reloadDatatable(datatable)" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('ui::strings.refresh') }}">
                                                    <em class="icon ni ni-reload"></em>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-search search-wrap" data-search="search">
                    <div class="card-body">
                        <div class="search-content">
                            <a href="javascript:;" class="search-back btn btn-icon toggle-search datatable-search-cancel" data-target="search">
                                <em class="icon ni ni-arrow-left"></em>
                            </a>
                            <input type="text" class="form-control border-transparent form-focus-none datatable-search-input" placeholder="{{ __('ui::strings.search_for_somthing') }}">
                            <button class="search-submit btn btn-icon datatable-search-button">
                                <em class="icon ni ni-search"></em>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-inner p-0 pb-3 bs-datatable-card">

                <table class="bs-datatable bs-hidden-search nowrap nk-tb-list nk-tb-ulist is-compact table-responsive" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col fit">{{ __('ui::inputs.id.label') }}</th>
                            <th class="nk-tb-col"><span class="sub-text">{{ __('admin::inputs.name.label') }}</span></th>
                            <th class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('admin::inputs.email.label') }}</span></th>
                            <th class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('admin::inputs.phone_number.label') }}</span></th>
                            <th class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('admin::inputs.username.label') }}</span></th>
                            <th class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('admin::inputs.type.label') }}</span></th>
                            <th class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('admin::inputs.status.label') }}</span></th>
                            <th class="nk-tb-col tb-col-lg"><span class="sub-text">{{ __('ui::inputs.created_at.label') }}</span></th>
                            <th class="nk-tb-col tb-col-lg"><span class="sub-text">{{ __('ui::inputs.updated_at.label') }}</span></th>
                            <th class="nk-tb-col nk-tb-col-tools text-right fit"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        var recordsType = 'AVAILABLE';
        var datatable = BS.DATATABLE('.bs-datatable', {
            ajax : {
                url  : "{!! route('UsersController@postIndex') !!}",
                type : "POST",
                data : function(data){
                    $('#searchModal').find('.close').first().click();
                    data.records_type = recordsType;
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
                    className  : "nk-tb-col",
                    data       : 'id',
                    name       : 'id',
                    orderable  : true,
                    searchable : true,
                    // visible    : false,
                    render     : function(data,type,Row,meta) {
                        return `<span class="text-danger">${Row.id}</span>`;
                    }
                },
                {
                    className  : "nk-tb-col",
                    data       : "name",
                    name       : 'name',
                    render     : function(data,type,Row,meta) {
                        return `
                            <div class="user-card">
                                <div class="user-avatar xs bg-dark">
                                    <span>AB</span>
                                </div>
                                <div class="user-name">
                                    <span class="tb-lead">${Row.name}</span>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    className  : "nk-tb-col tb-col-lg",
                    data       : "email",
                    name       : 'email',
                    render     : function(data,type,Row,meta) {
                        return `${Row.email || '-------' }`;
                    }
                },
                {
                    className  : "nk-tb-col tb-col-lg",
                    data       : "phone_number",
                    name       : 'phone_number',
                    render     : function(data,type,Row,meta) {
                        return `<span dir="ltr">${Row.phone_number || '-------' }</span>`;
                    }
                },
                {
                    className  : "nk-tb-col tb-col-lg",
                    data       : "username",
                    name       : 'username',
                    render     : function(data,type,Row,meta) {
                        return `${Row.username || '-------' }`;
                    }
                },
                {
                    className  : "nk-tb-col tb-col-lg",
                    data       : "type",
                    name       : 'type',
                    render     : function(data,type,Row,meta) {
                        return `<span class="badge badge-dot badge-warning">${Row.type ? Row.type.title : 'N/A'}</span>`;
                    }
                },
                {
                    className  : "nk-tb-col tb-col-lg",
                    data       : "sys_status",
                    name       : 'sys_status',
                    render     : function(data,type,Row,meta) {
                        return `<span class="badge badge-dot badge-success">${Row.sys_status}</span>`;
                    }
                },
                {
                    className  : "nk-tb-col tb-col-lg",
                    data       : "created_at",
                    name       : 'created_at',
                    render     : function(data,type,Row,meta) {
                        return `<span>${Row.created_at}</span>`;
                    }
                },
                {
                    className  : "nk-tb-col tb-col-lg",
                    data       : "updated_at",
                    name       : 'updated_at',
                    visible    : false,
                    render     : function(data,type,Row,meta) {
                        return `<span>${Row.updated_at}</span>`;
                    }
                },
                {
                    className      : "nk-tb-col nk-tb-col-tools",
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
                                                    ${(! Row.actions['delete']      ? `` : `<li><a href="javascript:;" onclick="BS.ajaxDelete('{{ route('UsersController@postDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete"></em><span>${Row.actions['delete']['label']}</span></a></li>`)}
                                                    ${(! Row.actions['restore']     ? `` : `<li><a href="javascript:;" onclick="BS.ajaxRestore('{{ route('UsersController@postRestore') }}', ${Row.id}, datatable)"><em class="icon ni ni-redo"></em><span>${Row.actions['restore']['label']}</span></a></li>`)}
                                                    ${(! Row.actions['permaDelete'] ? `` : `<li><a href="javascript:;" onclick="BS.ajaxPermaDelete('{{ route('UsersController@postPermaDelete') }}', ${Row.id}, datatable)"><em class="icon ni ni-delete text-danger"></em><span>${Row.actions['permaDelete']['label']}</span></a></li>`)}
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

        var searchTimeout = null;
        var datatableKeywordSearch = function(elem) {
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            searchTimeout = setTimeout(() => {
                BS.resetForm(null, $('#datatable-advanced-search-form'));
                datatable.search( $(elem).val() ).draw();
            }, 500);
        };
        $(document).on('keyup', '.datatable-search-input', function(e) {
            datatableKeywordSearch($(this));
        });
        $(document).on('click', '.datatable-search-button', function(e) {
            datatableKeywordSearch($('.datatable-search-input'));
        });
        $(document).on('click', '.datatable-search-cancel', function(e) {
            $('.datatable-search-input').val('');
            datatableKeywordSearch($('.datatable-search-input'));
        });
        var setRecordsType = function(status) {
            recordsType = status;
            datatable.ajax.reload();
        }
    </script>
@endpush