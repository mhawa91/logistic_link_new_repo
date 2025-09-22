@extends('admin::emails_v2.layout')

@section('content')
    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
        <tbody>
            <tr>
                <td style="text-align:center;padding: 30px 30px 15px 30px;">
                    <h2 style="font-size: 18px; color: {{ config('mail_theme.colors.primary') }}; font-weight: 600; margin: 0;">New Contract Added</h2>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;padding: 0 30px 20px">
                    <p style="margin-bottom: 10px;">Hi {{ $user->name }},</p>
                    <p>A new contract has been added by <strong>{{ $company }}</strong> on {{ config('mail_theme.appName') }}!</p>
                                        
                    <p>You can view and manage this contract by accessing your dashboard.</p>

                    <a href="{{ route('AuthController@login', ['hash' => $hash, 'on_success'=> route('TransportersController@index')]) }}" style="background-color:{{ config('mail_theme.colors.primary') }};border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0 25px; margin-top: 25px">View Contract</a>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;padding: 20px 30px 40px">
                    <p>If you think you got this email by mistake, please ignore this email.</p>
                    <p style="margin-bottom: 15px;">Thank you for using our platform!</p>
                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">
                        This is an automatically generated email please do not reply to this email. If you face any issues, please contact us at {{ config('mail_theme.appContactEmail') }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
