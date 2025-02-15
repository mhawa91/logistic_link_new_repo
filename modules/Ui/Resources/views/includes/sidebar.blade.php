<!-- sidebar @s -->
<div class="nk-sidebar nk-sidebar-fixed is-light " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="javascript:;" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
            <a href="javascript:;" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
        </div>
        <div class="nk-sidebar-brand">
            <a href="{{ route('AdminDashboardController@index') }}" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.white.publicPath') ) }}" alt="logo" style="max-width: {{ config('ui.logos.' . $_LOCALE_ . '.white.maxWidth') }}; max-height: {{ config('ui.logos.' . $_LOCALE_ . '.white.maxHeight') }}; width: {{ config('ui.logos.' . $_LOCALE_ . '.white.maxWidth') }}; height: {{ config('ui.logos.' . $_LOCALE_ . '.white.maxHeight') }};">
                <img class="logo-dark logo-img" src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.dark.publicPath') ) }}" alt="logo-dark" style="max-width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; max-height: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxHeight') }}; width: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxWidth') }}; height: {{ config('ui.logos.' . $_LOCALE_ . '.dark.maxHeight') }};">
            </a>
        </div>
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    @foreach (app('Ui')->mainMenu() as $item)
                        @if ($item['type'] == 'HEADER')
                            @if (! empty($item['text']) && ! empty($item['sub_items']))
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">{{ $item['text'] }}</h6>
                                </li>
                            @endif
                            @foreach ($item['sub_items'] as $item)
                                @if ($item['type'] == 'MENU')
                                    <li class="nk-menu-item {{ empty($item['sub_items']) ? '' : 'has-sub' }}">
                                        <a href="{{ $item['link'] }}" class="nk-menu-link {{ empty($item['sub_items']) ? '' : 'nk-menu-toggle' }}">
                                            <span class="nk-menu-icon">
                                                @if ($item['icon']['type'] == 'em')
                                                    <em class="icon {{ $item['icon']['data'] }}"></em>
                                                @endif
                                                @if ($item['icon']['type'] == 'svg')
                                                    {!! $item['icon']['data'] !!}
                                                @endif
                                                @if ($item['icon']['type'] == 'span')
                                                    <span class="icon {{ $item['icon']['data'] }}"></span>
                                                @endif
                                                @if ($item['icon']['type'] == 'i')
                                                    <i class="icon {{ $item['icon']['data'] }}"></i>
                                                @endif
                                            </span>
                                            <span class="nk-menu-text">{{ $item['text'] }}</span>
                                        </a>
                                        @if (! empty($item['sub_items']))
                                            <ul class="nk-menu-sub">
                                                @foreach ($item['sub_items'] as $item)
                                                    <li class="nk-menu-item">
                                                        <a href="{{ $item['link'] }}" class="nk-menu-link">
                                                            <span class="nk-menu-text">{{ $item['text'] }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul><!-- .nk-menu-sub -->
                                        @endif
                                    </li><!-- .nk-menu-item -->
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </ul><!-- .nk-menu -->
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>
<!-- sidebar @e -->