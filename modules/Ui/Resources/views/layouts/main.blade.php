<!DOCTYPE html>
<html lang="{{ $_LOCALE_ }}" class="js">

<head>
    @php
        $options = array_merge([
            'width'   => null,
            'margin'  => null,
            'padding' => null,
        ], isset($options) ? $options : [])
    @endphp
    @include('ui::partials.meta')
    @include('ui::includes.style')
    <style>
        .nk-footer.is-dark:not([class*=bg-]) {
            background: #101924;
        }
        /* .nk-header-fixed + .nk-content {
            background-color: #fff;
            -webkit-box-shadow: 0px 10px 30px 0px rgb(82 63 105 / 8%);
            box-shadow: 0px 10px 30px 0px rgb(82 63 105 / 8%);
        } */
        .responsive-tr {
            display: table-row; 
            text-overflow: ellipsis; 
            white-space: nowrap;
        }
        
        body.has-rtl .tb-tnx-head {
            letter-spacing: unset;
        }
        body.has-rtl .nk-menu-trigger {
            margin-right: -0.25rem;
            margin-left: 1.125rem;
        }
        .image-input .actions {
            position: absolute;
            /* bottom: -12px; */
        }
        .loader-wrap {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        .loader-wrap .loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .nk-sidebar-head {
            height: 130px;
        }
        /* .logo-img {
            max-height: 69px;
        } */
        .nk-sidebar-content {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 130px);
        }
        .rtl-input {
            direction: ltr;
            text-align: right;
        }
        .invalid {
            color: #e85347;
            font-size: 11px;
            font-style: italic;
        }
        .nk-sidebar-brand {
            width: calc(100% - 1.625rem);
            position: absolute;
            text-align: center;
        }
        .g-3 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }
        div.dataTables_wrapper div.dataTables_paginate {
            padding: 1rem 1rem 0 1rem;
        }
        div.dataTables_wrapper div.dataTables_info {
            padding: 1rem 1rem 0 1rem;
        }

        /* .nk-sidebar {
            border-left: unset;
        } */
        .is-dark .nk-sidebar-head {
            /* border-color: #1c2f50; */
            border-bottom: 1px dashed #8699b2;
            border-left: unset;
        }
        .nk-menu-heading {
            padding: 0.5rem 24px 0.5rem !important;
        }
        .nk-menu-sub .nk-menu-text:before {
            content: "";
            width: 6px;
            height: 2px;
            background-color: #8699b2;
            color: #8699b2;
            position: absolute;
            left: 33px;
            top: 16.5px;
            -webkit-transition: all .4s ease-in-out;
            transition: all .4s ease-in-out;
            opacity: .8;
        }
        .card .table .tb-tnx-action {
            vertical-align: inherit;
        }

        body.dark-mode .modal-header {
            background-color: #11171f !important;
        }
        body.dark-mode .tox .tox-collection__item,
        body.dark-mode .tox .tox-collection__item:hover {
            color: #8094ae;
        }
        body.dark-mode .mce-content-body {
            background: #fff;
        }
        body.dark-mode .bs-datatable-card .bs-datatable thead {
            background-color: unset;
        }
        body.dark-mode .nk-sidebar {
            border-right: 1px solid #e5e9f2;
            border-left: unset;
        }
        .overline-title {
            letter-spacing: unset;
        }
        .bs-scroll {
            height: 380px;
            overflow-y: scroll;
            padding-right: 10px;
        }
    </style>
    @if ($_DIR_ == 'rtl')
        <style>
            .nk-menu-sub .nk-menu-text:before {
                left: unset;
                right: 33px;
            }
            body.dark-mode .nk-sidebar {
                border-right: unset;
                border-left: 1px solid #e5e9f2;
            }
            .overline-title-alt {
                letter-spacing: unset;
                font-size: 12px;
            }
            .bs-scroll {
                padding-left: 10px;
                padding-right: unset;
            }
        </style>
    @endif
    @stack('head')
</head>

<body dir="{{ $_DIR_ }}" class="nk-body bg-lighter npc-general has-sidebar ui-softy {{ $_DIR_ == 'rtl' ? 'has-rtl' : '' }} d-none">

    {{-- <div class="loader-wrap" style="background-color: #0f192a; z-index: 100000; display: flex; justify-content: center;">
        <img class="" src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.white.publicPath') ) }}" alt="logo" style="width: auto; position: absolute; top: 20%">
        <div class="loader">
            <div class="sk-chase">
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
                <div class="sk-chase-dot"></div>
            </div>
        </div>
    </div> --}}
    
    <div class="nk-app-root">
        <div class="nk-main ">
            @include('ui::includes.sidebar')
            <div class="nk-wrap">
                @include('ui::includes.header')
                <div class="nk-content {{ $options['padding'] === 0 ? 'p-0' : '' }}">
                    <div class="{{ $options['padding'] === 0 ? '' : 'container-fluid' }}">
                        <div class="nk-content-inner">
                            <div class="nk-content-body {{ $options['width'] ? 'wide-' . $options['width'] : '' }} {{ $options['margin'] ? 'mx-' . $options['margin'] : '' }}">
                                @yield('breadcrumb')
                                <div class="nk-block">
                                    @yield('beforeContent')
                                    @yield('content')
                                    @yield('afterContent')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nk-footer is-light">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> 
                                &copy; {{ date('Y') }} {{ __( config('app.name') ) }}. {{ __('ui::strings.all_rights_reserved') }}.
                            </div>
                            <div class="nk-footer-links">
                                @if (config('systemcore.developer.active'))
                                    <div class="nk-footer-copyright"> 
                                        {{ __('ui::strings.powered_by') }} <a href="{{ __( config('systemcore.developer.url') ) }}" target="_blank"><strong>{{ __( config('systemcore.developer.name') ) }}</strong></a>
                                    </div>
                                @endif
                                {{-- <ul class="nav nav-sm">
                                    <li class="nav-item"><a class="nav-link" href="#">Terms</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#">Privacy</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#">Help</a></li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stack('body')
    <script>
        const dataTableTranslations = {!! json_encode(__('ui::strings.datatable')) !!};
    </script>
    @include('ui::includes.script')
    @include('ui::includes.scriptHelpers')
    @include('ui::components.vue')
    @stack('script')
    <script src="{{ asset('modules/ui/dashlite-3x/js/charts/gd-analytics.js') }}"></script>
    <script src="{{ asset('modules/ui/dashlite-3x/js/libs/jqvmap.js') }}"></script>
</body>

</html>