<!-- main header @s -->
<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ml-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="{{ route('AdminDashboardController@index') }}" class="logo-link">
                    <img class="logo-light logo-img" src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.favicon.publicPath') ) }}" alt="logo" style="width: {{ config('ui.logos.' . $_LOCALE_ . '.favicon.widthMobile') }}">
                    <img class="logo-dark logo-img" src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.favicon.publicPath') ) }}" alt="logo-dark" style="width: {{ config('ui.logos.' . $_LOCALE_ . '.favicon.widthMobile') }}">
                </a>
            </div><!-- .nk-header-brand -->
            {{-- <div class="nk-header-news d-none d-xl-block">
                <div class="nk-news-list">
                    <a class="nk-news-item" href="#">
                        <div class="nk-news-icon">
                            <em class="icon ni ni-card-view"></em>
                        </div>
                        <div class="nk-news-text">
                            <p>Do you know the latest update of 2019? <span> A overview of our is now available on YouTube</span></p>
                            <em class="icon ni ni-external"></em>
                        </div>
                    </a>
                </div>
            </div><!-- .nk-header-news --> --}}
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
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
                    {{-- <li class="dropdown notification-dropdown mr-n1">
                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-toggle="dropdown">
                            <div class="icon-status icon-status-info"><em class="icon ni ni-bell"></em></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right dropdown-menu-s1">
                            <div class="dropdown-head">
                                <span class="sub-title nk-dropdown-title">Notifications</span>
                                <a href="#">Mark All as Read</a>
                            </div>
                            <div class="dropdown-body">
                                <div class="nk-notification">
                                    <div class="nk-notification-item dropdown-inner">
                                        <div class="nk-notification-icon">
                                            <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
                                        </div>
                                        <div class="nk-notification-content">
                                            <div class="nk-notification-text">You have requested to <span>Widthdrawl</span></div>
                                            <div class="nk-notification-time">2 hrs ago</div>
                                        </div>
                                    </div>
                                    <div class="nk-notification-item dropdown-inner">
                                        <div class="nk-notification-icon">
                                            <em class="icon icon-circle bg-success-dim ni ni-curve-down-left"></em>
                                        </div>
                                        <div class="nk-notification-content">
                                            <div class="nk-notification-text">Your <span>Deposit Order</span> is placed</div>
                                            <div class="nk-notification-time">2 hrs ago</div>
                                        </div>
                                    </div>
                                    <div class="nk-notification-item dropdown-inner">
                                        <div class="nk-notification-icon">
                                            <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
                                        </div>
                                        <div class="nk-notification-content">
                                            <div class="nk-notification-text">You have requested to <span>Widthdrawl</span></div>
                                            <div class="nk-notification-time">2 hrs ago</div>
                                        </div>
                                    </div>
                                    <div class="nk-notification-item dropdown-inner">
                                        <div class="nk-notification-icon">
                                            <em class="icon icon-circle bg-success-dim ni ni-curve-down-left"></em>
                                        </div>
                                        <div class="nk-notification-content">
                                            <div class="nk-notification-text">Your <span>Deposit Order</span> is placed</div>
                                            <div class="nk-notification-time">2 hrs ago</div>
                                        </div>
                                    </div>
                                    <div class="nk-notification-item dropdown-inner">
                                        <div class="nk-notification-icon">
                                            <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
                                        </div>
                                        <div class="nk-notification-content">
                                            <div class="nk-notification-text">You have requested to <span>Widthdrawl</span></div>
                                            <div class="nk-notification-time">2 hrs ago</div>
                                        </div>
                                    </div>
                                    <div class="nk-notification-item dropdown-inner">
                                        <div class="nk-notification-icon">
                                            <em class="icon icon-circle bg-success-dim ni ni-curve-down-left"></em>
                                        </div>
                                        <div class="nk-notification-content">
                                            <div class="nk-notification-text">Your <span>Deposit Order</span> is placed</div>
                                            <div class="nk-notification-time">2 hrs ago</div>
                                        </div>
                                    </div>
                                </div><!-- .nk-notification -->
                            </div><!-- .nk-dropdown-body -->
                            <div class="dropdown-foot center">
                                <a href="#">View All</a>
                            </div>
                        </div>
                    </li> --}}
                    @if (auth()->check())
                        <li class="dropdown user-dropdown">
                            
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <div class="user-toggle">
                                    <div class="user-avatar sm">
                                        <em class="icon ni ni-user-alt"></em>
                                    </div>
                                    <div class="user-info d-none d-md-block">
                                        <div class="user-status">{{ auth()->user()->Type->__('title') }}</div>
                                        <div class="user-name dropdown-indicator">{{ (auth()->user()->name ? ucwords(auth()->user()->name) : null) ?? auth()->user()->username ?? auth()->user()->email ?? auth()->user()->phone_number }}</div>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-md dropdown-menu-right dropdown-menu-s1">
                                <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                    <div class="user-card">
                                        <div class="user-avatar">
                                            <span>{{ app(\Modules\SystemCore\System\Classes\Helpers::class)->shortName(auth()->user()->name ?? auth()->user()->email ?? auth()->user()->phone_number ?? auth()->user()->username) }}</span>
                                        </div>
                                        <div class="user-info">
                                            <span class="lead-text">{{ (auth()->user()->name ? ucwords(auth()->user()->name) : '-------') }}</span>
                                            <span class="sub-text">{{ auth()->user()->email ?? auth()->user()->phone_number ?? auth()->user()->username }}</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="dropdown-inner user-account-info">
                                    <h6 class="overline-title-alt">Account Balance</h6>
                                    <div class="user-balance">
                                        1,494.23 <small class="currency currency-usd">USD</small>
                                    </div>
                                    <div class="user-balance-sub">
                                        Locked 
                                        <span>
                                            15,495.39 <span class="currency currency-usd">USD</span>
                                        </span>
                                    </div>
                                    <a href="#" class="link">
                                        <span>Withdraw Balance</span> <em class="icon ni ni-wallet-out"></em>
                                    </a>
                                </div> --}}
                                <div class="dropdown-inner">
                                    <ul class="link-list">
                                        {{-- <li>
                                            <a href="html/user-profile-regular.html">
                                                <em class="icon ni ni-user-alt"></em>
                                                <span>View Profile</span>
                                            </a>
                                        </li> --}}
                                        <li>
                                            <a href="{{ route('UsersController@profile') }}">
                                                <em class="icon ni ni-setting-alt"></em>
                                                <span>{{ __('admin::strings.users.profile') }}</span>
                                            </a>
                                        </li>
                                        {{-- <li><a href="html/user-profile-activity.html"><em class="icon ni ni-activity-alt"></em><span>Login Activity</span></a></li> --}}
                                        <li>
                                            <a class="dark-switch" href="javascript:;">
                                                <em class="icon ni ni-moon"></em>
                                                <span>{{ __('ui::strings.dark-mode') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dropdown-inner">
                                    <ul class="link-list">
                                        <li>
                                            <a href="javascript:;" onclick="$('#logout-form').submit();">
                                                <em class="icon ni ni-signout"></em><span>{{ __('ui::strings.signout') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <form action="{!! route('AuthController@postLogout') !!}" method="POST" class="hidden" id="logout-form">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </li><!-- .dropdown -->
                    @endif
                   
                    
                </ul><!-- .nk-quick-nav -->
            </div><!-- .nk-header-tools -->
        </div><!-- .nk-header-wrap -->
    </div><!-- .container-fliud -->
</div>
<!-- main header @e -->