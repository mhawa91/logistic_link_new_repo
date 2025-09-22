@extends('ui::layouts.auth')

@push('meta.title')
    {{ __('ui::strings.signin') }} | 
@endpush

@push('head')
<style>
.google-login-btn {
    background-color: #4285f4;
    border: none;
    color: white;
    padding: 12px 16px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: background-color 0.3s;
    font-size: 14px;
    font-weight: 500;
    width: 100%;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.google-login-btn:hover {
    background-color: #357ae8;
    color: white;
    text-decoration: none;
}

.google-login-btn:focus {
    background-color: #357ae8;
    color: white;
    text-decoration: none;
    outline: none;
    box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.3);
}

.google-icon {
    background: white;
    border-radius: 2px;
    padding: 4px;
    margin-right: 12px;
    margin-left: 12px;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.google-icon svg {
    width: 18px;
    height: 18px;
}

.auth-separator {
    text-align: center;
    margin: 20px 0;
    position: relative;
}

.auth-separator::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e3e7fe;
}

.auth-separator span {
    background: white;
    padding: 0 15px;
    color: #9eaecf;
    font-size: 13px;
}
</style>
@endpush

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title">{{ __('ui::strings.signin') }}</h5>
            <div class="nk-block-des">
                <p>{{ __('ui::strings.signin_note') }}</p>
            </div>
        </div>
    </div>
    <form action="{{ route('AuthController@postLogin') }}" method="POST" onsubmit="BS.AJAX_FORM(event)" data-keep-loading-on-success="true">
        @csrf
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="default-01">{{ __('ui::inputs.your_email_or_phone_or_username.label') }}</label>
                {{-- <a class="link link-primary link-sm" tabindex="-1" href="#">Need Help</a> --}}
            </div>
            <input type="text" name="email_or_phone_or_username" class="form-control form-control-lg" id="default-01" placeholder="{{ __('ui::inputs.your_email_or_phone_or_username.placeholder') }}">
        </div>
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password">{{ __('ui::inputs.your_password.label') }}</label>
                <a class="link link-primary link-sm" tabindex="-1" href="{{ route('AuthController@reset') }}">{{ __('ui::strings.forgot_your_password') }}</a>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="javascript:;" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="{{ __('ui::inputs.your_password.placeholder') }}">
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-primary btn-block" type="submit">
                <span> {{ __('ui::strings.signin') }}</span>
            </button>
        </div>
    </form>

    <div class="text-center pt-4 pb-3"><h6 class="overline-title overline-title-sap"><span>{{ __('ui::strings.or') }}</span></h6></div>

    <div class="form-group">
        <a href="{{ route('WebsiteAuthController@loginByGoogle') }}" class="google-login-btn">
            <div class="google-icon">
                <svg viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
            </div>
            {{ __('ui::strings.signin_with_google') }}
        </a>
    </div>
    
    @if (config('systemcore.adminRegistration.active'))
        <div class="form-note-s2 pt-4"> 
            {{ __('ui::strings.dont_have_an_account_yet') }} <a href="{{ route('AuthController@register') }}">{{ __('ui::strings.create_an_account') }}</a>
        </div>
    @endif
@endsection