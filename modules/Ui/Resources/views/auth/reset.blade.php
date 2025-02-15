@extends('ui::layouts.auth')

@push('meta.title')
    {{ __('ui::strings.reset_password') }} | 
@endpush

@section('content')
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h5 class="nk-block-title">{{ __('ui::strings.reset_password') }}</h5>
            <div class="nk-block-des">
                <p>{{ __('ui::strings.reset_password_note') }}</p>
            </div>
        </div>
    </div><!-- .nk-block-head -->
    <form action="{{ route('AuthController@postReset') }}" method="POST" onsubmit="BS.AJAX_FORM(event)">
        @csrf
        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="default-01">{{ __('ui::inputs.your_email.label') }}</label>
                {{-- <a class="link link-primary link-sm" tabindex="-1" href="#">Need Help</a> --}}
            </div>
            <input type="text" name="email_or_phone_or_username" class="form-control form-control-lg" id="default-01" placeholder="{{ __('ui::inputs.your_email.placeholder') }}">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <span> {{ __('ui::strings.send_reset_link') }}</span>
            </button>
        </div>
    </form><!-- form -->
    <div class="form-note-s2 pt-4"> 
        {!! __('ui::strings.back_to', ['page' => '<a href="'. route('AuthController@login') .'">'. __('ui::strings.signin') .'</a>']) !!}
    </div>
@endsection