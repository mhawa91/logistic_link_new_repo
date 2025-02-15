@extends('ui::layouts.email', [
    'title' => 'Account Password Reset Request',
])

@section('content')
    <h1 style="color:#222222; font-weight:bold; font-size:24px; margin:10px 0 16px 0">
        Account Password Reset Request
    </h1>
    <p style="margin-bottom:5px; font-size:16px"></p>
    <p style="word-wrap:break-word; word-break:break-all">
        You have requsted password reset for your account
    </p>
    <br aria-hidden="true">
    Use the following code to confirm the request: 
    <p style="background:#eeeeee; padding:10px; border-radius:2px; color:#000000; font-size:18px; font-weight:bold">
        {{ $reset->code }}
    </p>
    or click 
    <a href="{{ $url ?? '#' }}" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" data-linkindex="1">
        here
    </a> 
    to confirm. 
    <p style="font-family:arial,sans-serif; color:#474747; font-weight:normal; font-size:14px; margin:0; padding:0 0 0 0; text-align:left; line-height:1.5">
        The confirmation code will expire in 5 minute(s). 
        <br aria-hidden="true">
        <br aria-hidden="true">
        If you didn't request a reset, we suggest you to immediately log in to your account, change your password. 
    </p>
@endsection