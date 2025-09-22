@extends('ui::layouts.auth')

@push('meta.title')
    {{ __('ui::strings.signup') }} | 
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

.google-login-btn.disabled {
    background-color: #9eaecf;
    cursor: not-allowed;
    opacity: 0.6;
}

.google-login-btn.disabled:hover {
    background-color: #9eaecf;
}
</style>

<script>
function getSelectedAccountType() {
    const selectedType = document.querySelector('input[name="type"]:checked');
    return selectedType ? selectedType.value : null;
}

function updateGoogleLoginUrl() {
    const googleBtn = document.querySelector('.google-login-btn');
    const selectedType = getSelectedAccountType();
    
    if (selectedType) {
        const baseUrl = "{{ route('WebsiteAuthController@loginByGoogle') }}";
        googleBtn.href = baseUrl + "?type=" + selectedType + "&new_account=Y";
        googleBtn.classList.remove('disabled');
    } else {
        googleBtn.href = "javascript:void(0)";
        googleBtn.classList.add('disabled');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Update URL when account type changes
    document.querySelectorAll('input[name="type"]').forEach(function(radio) {
        radio.addEventListener('change', updateGoogleLoginUrl);
    });
    
    // Initial check
    updateGoogleLoginUrl();
});
</script>
@endpush

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title">{{ __('ui::strings.signup') }}</h5>
            <div class="nk-block-des">
                <p>{{ __('ui::strings.signup_note') }}</p>
            </div>
        </div>
    </div><!-- .nk-block-head -->
    <form action="{{ route('AuthController@postRegister') }}" onsubmit="BS.AJAX_FORM(event)" data-keep-loading-on-success="true" method="POST">
        @csrf

        
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="full-name">{{ __('ui::inputs.full_name.label') }}</label>
            </div>
            <input type="text" name="full_name" class="form-control form-control-lg" id="full-name" placeholder="{{ __('ui::inputs.full_name.placeholder') }}">
        </div>

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="default-01">{{ __('ui::inputs.your_email.label') }}</label>
            </div>
            <input type="text" name="email" class="form-control form-control-lg" id="default-01" placeholder="{{ __('ui::inputs.your_email.placeholder') }}">
        </div>
        
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="your_password">{{ __('ui::inputs.your_password.label') }}</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="javascript:;" class="form-icon form-icon-right passcode-switch lg" data-target="your_password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" name="password" class="form-control form-control-lg" id="your_password" placeholder="{{ __('ui::inputs.your_password.placeholder') }}">
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="your_password_confirmation">{{ __('ui::inputs.your_password_confirmation.label') }}</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="javascript:;" class="form-icon form-icon-right passcode-switch lg" data-target="your_password_confirmation">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" name="password_confirmation" class="form-control form-control-lg" id="your_password_confirmation" placeholder="{{ __('ui::inputs.your_password_confirmation.placeholder') }}">
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="full-name">{{ __('contenttypes::inputs.account-type.label') }}</label>
            </div>
            <ul class="custom-control-group">
                <li>
                    <div class="custom-control custom-checkbox custom-control-pro no-control">
                        <input type="radio" class="custom-control-input" name="type" id="GROUP-DEFAULT" value="DEFAULT">
                        <label class="custom-control-label" for="GROUP-DEFAULT">
                            <em class="icon ni ni-building"></em>
                            <span>{{ __('contenttypes::inputs.account-type.options.DEFAULT') }}</span>
                        </label>
                    </div>
                </li>
                <li>
                    <div class="custom-control custom-checkbox custom-control-pro no-control">
                        <input type="radio" class="custom-control-input" name="type" id="GROUP-TRANSPORTER" value="TRANSPORTER">
                        <label class="custom-control-label" for="GROUP-TRANSPORTER">
                            <em class="icon ni ni-truck"></em>
                            <span>{{ __('contenttypes::inputs.account-type.options.TRANSPORTER') }}</span>
                        </label>
                    </div>
                </li>
            </ul>
        </div>

        <div class="form-group">
            {{-- <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('ui::strings.signup') }}</button> --}}

            <button class="btn btn-lg btn-primary btn-block" type="submit">
                <span> {{ __('ui::strings.signup') }}</span>
            </button>
        </div>
    </form><!-- form -->
    
    <div class="text-center pt-4 pb-3"><h6 class="overline-title overline-title-sap"><span>{{ __('ui::strings.or') }}</span></h6></div>

    <div class="form-group">
        <a href="javascript:void(0)" class="google-login-btn disabled">
            <div class="google-icon">
                <svg viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
            </div>
            {{ __('ui::strings.signup_with_google') }}
        </a>
    </div>
    
    <div class="form-note-s2 pt-4"> 
        {!! __('ui::strings.back_to', ['page' => '<a href="'. route('AuthController@login') .'">'. __('ui::strings.signin') .'</a>']) !!}
    </div>
@endsection