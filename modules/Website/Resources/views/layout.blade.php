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
    @include('website::includes.meta')
    @include('website::includes.style')
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
        .uppercase {
            text-transform: uppercase;
        }
        .title-primary {
            line-height: 56px;
            font-weight: 700;
        }
        .text-green {
            color: #24BDA6;
        }
        .bg-green {
            background: #24BDA6;
        }
        .bg-orange {
            background: #fb4f28 !important;
        }
        .text-white {
            color: #fff
        }
        .header-logo {
            margin-right: -30px;
            margin-left: -30px;
        }

        .custom-border::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, #24bda6 50%, #fb4f28 50%);
        }

        .footer .link-inline a,
        .footer .social li .icon {
            color: #ffffff;
        }
        .underline {
            text-decoration: underline;
        }
        .locales.menu-btns {
            display: flex;
            align-items: center;
        }
        .locales.menu-btns li {
            padding: 0 0;
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
            .title {
                font-weight: 700;
            }
        </style>
    @endif
    @stack('head')
</head>

<body dir="{{ $_DIR_ }}" class="nk-body bg-white npc-landing {{ $_DIR_ == 'rtl' ? 'has-rtl' : '' }} d-none">

    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main">

            <header class="header has-header-main-s1 mb-5 mb-sm-6 mb-md-7" id="home" style="position: relative; background-image: url({{ asset('landing/images/custom/hero-bg.jpeg') }}); background-position: center; background-size: cover;">
                <img src="{{ asset('landing/images/custom/shape-1.svg') }}" alt="" style="position: absolute; top: 150px; right: 375px;">
                <img src="{{ asset('landing/images/custom/shape-2.svg') }}" alt="" style="position: absolute; top: 250px; right: 175px;">

                <img src="{{ asset('landing/images/custom/shape-1.svg') }}" alt="" style="position: absolute; top: 150px; left: 375px;">
                <img src="{{ asset('landing/images/custom/shape-2.svg') }}" alt="" style="position: absolute; top: 250px; left: 175px;">

                <img src="{{ asset('landing/images/custom/shape-5.svg') }}" alt="" style="position: absolute; bottom: 250px; right: 175px;">
                <img src="{{ asset('landing/images/custom/shape-4.svg') }}" alt="" style="position: absolute; bottom: 150px; right: 375px;">

                <img src="{{ asset('landing/images/custom/shape-3.svg') }}" alt="" style="position: absolute; bottom: 150px; left: 375px;">
                <img src="{{ asset('landing/images/custom/shape-5.svg') }}" alt="" style="position: absolute; bottom: 250px; left: 175px;">

                <div class="header-main header-main-s1 is-sticky custom-border">
                    <div class="container header-container">
                        <div class="header-wrap">
                            <div class="header-logo">
                                <a href="{{ route('WebsiteController@index') }}" class="logo-link">
                                    <img class="logo-light logo-img" src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.dark.publicPath') ) }}" alt="logo" style="max-width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; max-height: 50px; width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; height: 40px;">
                                    <img class="logo-dark logo-img" src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.dark.publicPath') ) }}" alt="logo-dark" style="max-width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; max-height: 50px; width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; height: 40px;">                    
                                </a>
                            </div>
                            <div class="header-toggle">
                                <button class="menu-toggler" data-target="mainNav">
                                    <em class="menu-on icon ni ni-menu"></em>
                                    <em class="menu-off icon ni ni-cross"></em>
                                </button>
                            </div><!-- .header-nav-toggle -->
                            <nav class="header-menu ps-xl-5" data-content="mainNav">
                                <ul class="menu-list">
                                    <li class="menu-item"><a href="#home" class="menu-link nav-link">{{ __('website::strings.menu.home') }}</a></li>
                                    <li class="menu-item"><a href="#features" class="menu-link nav-link">{{ __('website::strings.menu.features') }}</a></li>
                                    <li class="menu-item"><a href="#how-it-works" class="menu-link nav-link">{{ __('website::strings.menu.how-it-works') }}</a></li>
                                    <li class="menu-item"><a href="#success-stories" class="menu-link nav-link">{{ __('website::strings.menu.success-stories') }}</a></li>
                                    <li class="menu-item"><a href="#faq" class="menu-link nav-link">{{ __('website::strings.menu.faq') }}</a></li>
                                    <li class="menu-item"><a href="#contact" class="menu-link nav-link">{{ __('website::strings.menu.contact') }}</a></li>
                                </ul>
                                <ul class="menu-btns locales">
                                    @if(count($_ALL_DISPLAY_LOCALES_) > 1)
                                        <li class="dropdown">
                                            <a class="dropdown-toggle nk-quick-nav-icon" data-toggle="dropdown" data-offset="0,10">
                                                <img src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.flag.publicPath') ) }}" alt="" style="border-radius: 30%; width: 24px; max-width: unset;">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                <ul class="language-list">
                                                    @foreach ($_ALL_DISPLAY_LOCALES_ as $_LOCALE_BASE_CODE => $_LOCALE_DETAILS)
                                                        @if (in_array($_LOCALE_BASE_CODE, config('ui.display_language')))
                                                            <li>
                                                                <a href="{{  LaravelLocalization::getLocalizedURL($_LOCALE_BASE_CODE, request()->fullUrl(), []) }}" class="language-item">
                                                                    <img src="{{ asset( config('ui.logos.' . $_LOCALE_BASE_CODE . '.flag.publicPath') ) }}" alt="" class="language-flag" style="border-radius: 30%;">
                                                                    <span class="language-name">{{ $_LOCALE_DETAILS['native'] }}</span>
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                                <ul class="menu-btns">
                                    <li>
                                        <a href="#contact" class="btn btn-custom btn-lg">{{ __('website::strings.request-for-demo') }}</a>
                                    </li>
                                </ul>
                            </nav><!-- .nk-nav-menu -->
                        </div><!-- .header-warp-->
                    </div><!-- .container-->
                </div><!-- .header-main-->
                <div class="header-content my-auto py-5 is-dark">
                    <div class="container">
                        <div class="row justify-content-center text-center">
                            <div class="col-lg-10 col-md-10">
                                <div class="header-caption">
                                    <h1 class="header-title uppercase title-primary">
                                        {!! __('website::strings.hero_title') !!}
                                    </h1>
                                    <div class="header-text">
                                        <p class="text-white">
                                            {!! __('website::strings.hero_subtitle') !!}
                                        </p>
                                    </div>
                                    <ul class="header-action btns-inline">
                                        <li><a href="#" class="btn btn-custom btn-lg"><span>{{ __('website::strings.request-for-demo') }}</span></a></li>
                                    </ul>
                                </div><!-- .header-caption -->
                            </div><!-- .col -->
                        </div><!-- .row -->
                    </div><!-- .container -->
                </div><!-- .header-content -->
                <div class="header-image mb-n5 mb-sm-n6 mb-md-n7">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-xl-10">
                                <div class="card overflow-hidden p-2 bg-light">
                                    <img src="{{ asset('landing') }}/images/header/gfx-c.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header><!-- .header -->

            @yield('content')

            <footer class="footer bg-orange" id="footer">
                <div class="container">
                    <div class="row g-y-5 align-items-center justify-content-md-between py-4 py-md-5">
                        <div class="col-md-3">
                            <div class="footer-logo header-logo">
                                <a href="{{ route('WebsiteController@index') }}" class="logo-link">
                                    <img class="logo-light logo-img" src="{{ asset('logos/footer-logo-'. $_LOCALE_ .'.svg') }}" alt="logo" style="max-width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; max-height: 100px; width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; height: 40px;">
                                    <img class="logo-dark logo-img" src="{{ asset('logos/footer-logo-'. $_LOCALE_ .'.svg') }}" alt="logo-dark" style="max-width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; max-height: 100px; width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; height: 40px;">
                                </a>
                            </div><!-- .footer-logo -->
                        </div><!-- .col -->
                        <div class="col-md-9 d-flex justify-content-md-end">
                            <ul class="link-inline gx-4 text-white">
                                <li><a href="#home">{{ __('website::strings.menu.home') }}</a></li>
                                <li><a href="#features">{{ __('website::strings.menu.features') }}</a></li>
                                <li><a href="#how-it-works">{{ __('website::strings.menu.how-it-works') }}</a></li>
                                <li><a href="#success-stories">{{ __('website::strings.menu.success-stories') }}</a></li>
                                <li><a href="#faq">{{ __('website::strings.menu.faq') }}</a></li>
                                <li><a href="#contact">{{ __('website::strings.menu.contact') }}</a></li>

                            </ul><!-- .footer-nav -->
                        </div><!-- .col -->
                    </div>
                    <hr class="hr border-light mb-0 mt-n1">
                    <div class="row g-y-5 align-items-center justify-content-md-between py-4">
                        <div class="col-md-8">
                            <div class="text-white">
                                {!! __('website::strings.copyrights') !!} &copy; {{ now()->format('Y') }}. 
                            </div>
                        </div><!-- .col -->
                        <div class="col-md-4 d-flex justify-content-md-end">
                            {{-- <ul class="social">
                                <li><a href="#"><em class="icon ni ni-twitter"></em></a></li>
                                <li><a href="#"><em class="icon ni ni-facebook-f"></em></a></li>
                                <li><a href="#"><em class="icon ni ni-instagram"></em></a></li>
                                <li><a href="#"><em class="icon ni ni-pinterest"></em></a></li>
                            </ul><!-- .footer-icon --> --}}
                            <span class="text-white">{!! __('website::strings.technology-partner') !!}</span>
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .container -->
            </footer><!-- .footer -->

        </div>
    </div>

    <script>
        const dataTableTranslations = {!! json_encode(__('ui::strings.datatable')) !!};
    </script>
    @include('ui::includes.script')
    @include('ui::includes.scriptHelpers')
    <script src="{{ asset('landing/js/bundle.js') }}"></script>
    <script src="{{ asset('landing/js/scripts.js') }}"></script>
    @stack('script')
</body>

</html>