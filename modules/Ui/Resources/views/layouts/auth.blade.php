<!DOCTYPE html>
<html lang="{{ $_LOCALE_ }}" class="js">

<head>
    @include('ui::partials.meta')
    @include('ui::includes.style')
    @stack('head')
    <style>
        @media (max-width: 600px) {
            .d-xs-none {
                display: none;
            }
            .bg-abstract {
                display: none;
            }
        }
        @media (min-width: 992px) {
            .nk-auth-container {
                width: 50%;
            }
        }
        @media (min-width: 1540px) {
            .nk-split .nk-auth-body, .nk-split .nk-auth-footer {
                margin-left: auto;
            }
        }
    </style>
</head>

<body dir="{{ $_DIR_ }}" class="nk-body npc-default pg-auth {{ $_DIR_ == 'rtl' ? 'has-rtl' : '' }}">
    <div class="nk-app-root">
        <div class="nk-main">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content">
                    <div class="nk-split nk-split-page nk-split-md">
                        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-white">
                            <div class="nk-block nk-block-middle nk-auth-body">
                                <div class="brand-logo pb-5">
                                    <a href="javascript:;" class="logo-link">
                                        <img class="logo-light logo-img logo-img-lg" src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.white.publicPath') ) }}" alt="logo" style="max-width: {{ config('ui.logos.' . $_LOCALE_ . '.white.maxWidth') }}; width: {{ config('ui.logos.' . $_LOCALE_ . '.white.maxWidth') }}">
                                        <img class="logo-dark logo-img logo-img-lg" src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.dark.publicPath') ) }}" alt="logo-dark" style="max-width: {{ config('ui.logos.' . $_LOCALE_ . '.white.maxWidth') }}; width: {{ config('ui.logos.' . $_LOCALE_ . '.white.maxWidth') }}">
                                    </a>
                                </div>
                                @yield('content')
                                {{-- <div class="text-center pt-4 pb-3">
                                    <h6 class="overline-title overline-title-sap"><span>OR</span></h6>
                                </div>
                                <ul class="nav justify-center gx-4">
                                    <li class="nav-item"><a class="nav-link" href="#">Facebook</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#">Google</a></li>
                                </ul>
                                <div class="text-center mt-5">
                                    <span class="fw-500">I don't have an account <a href="#">Try 15 days free</a></span>
                                </div> --}}
                            </div>
                            <div class="nk-block nk-auth-footer">
                                <div class="nk-block-between">
                                    <ul class="nav nav-sm">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">Terms & Condition</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">Privacy Policy</a>
                                        </li>
                                        @if(count($_ALL_DISPLAY_LOCALES_) > 1)
                                            <li class="nav-item dropup">
                                                <a class="dropdown-toggle dropdown-indicator has-indicator nav-link" data-toggle="dropdown" data-offset="0,10">
                                                    <small>{{ $_ALL_DISPLAY_LOCALES_[ $_LOCALE_ ]['native'] }}</small>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                    <ul class="language-list">
                                                        @foreach ($_ALL_DISPLAY_LOCALES_ as $_LOCALE_BASE_CODE => $_LOCALE_DETAILS)
                                                            @if (in_array($_LOCALE_BASE_CODE, config('ui.display_language')))
                                                                <li>
                                                                    <a href="{{  LaravelLocalization::getLocalizedURL($_LOCALE_BASE_CODE, request()->fullUrl(), []) }}" class="language-item">
                                                                        <img src="{{ asset( config('ui.logos.' . $_LOCALE_BASE_CODE . '.flag.publicPath') ) }}" alt="" class="language-flag" style="border-radius: 50%;">
                                                                        <span class="language-name">{{ $_LOCALE_DETAILS['native'] }}</span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </li>
                                        @endif
                                        
                                    </ul><!-- .nav -->
                                </div>
                                <div class="mt-3">
                                    <p>&copy; {{ date('Y') }} {{ __( config('app.name') ) }}. {{ __('ui::strings.all_rights_reserved') }}.</p>
                                </div>
                            </div>
                        </div>
                        <div class="nk-split-content nk-split-stretch bg-abstract" style="background-image: linear-gradient(to right, #2c3782 calc(60% - 150px), #39469f calc(60% - 150px), #39469f 60%, #4856b5 60%, #4856b5 calc(60% + 150px), #5b6ac6 calc(60% + 150px), #5b6ac6 100%);">
                            {{-- <img class="d-xs-none" src="{{ asset('logos/auth-side.png') }}" alt="" style="margin-top: 80px; width: 90%;"> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('ui::includes.script')
    @include('ui::includes.scriptHelpers')
    @stack('script')
</body>
</html>