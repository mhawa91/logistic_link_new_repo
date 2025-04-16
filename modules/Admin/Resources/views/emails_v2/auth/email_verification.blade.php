@extends('admin::emails_v2.layout')

@section('content')
    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#ffffff;">
        <tbody>
            <tr>
                <td style="text-align:center;padding: 30px 30px 15px 30px;">
                    <h2 style="font-size: 18px; color: {{ config('mail_theme.colors.primary') }}; font-weight: 600; margin: 0;">Confirm Your Email</h2>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;padding: 0 30px 20px">
                    <p style="margin-bottom: 10px;">Hi {{ $data['full_name'] }},</p>
                    <p>Thank you for registering with {{ config('mail_theme.appName') }}! To complete your account setup, please confirm your email address using the following One-Time Password (OTP).</p>
                    {{--  <a href="#" style="background-color:{{ theme.colors.primary }};border-radius:4px;color:#ffffff;display:inline-block;font-size:13px;font-weight:600;line-height:44px;text-align:center;text-decoration:none;text-transform: uppercase; padding: 0 25px">Reset Password</a>  --}}
                </td>
            </tr>
            <tr>
                <td style="text-align:center;padding: 15px 30px 15px 30px;">
                    <table style="width:100%;max-width:420px;margin:0 auto;">
                        <tbody>
                            @php
                                $codeParts = str_split($data['code']);
                            @endphp
                            <tr>
                                <td style="text-align: center; vertical-align: middle; font-size: 18px; font-weight: 700">
                                    <p style="border: 1px dashed {{ config('mail_theme.colors.primary') }}; color: {{ config('mail_theme.colors.primary') }}; margin-left: 10px; margin-right: 10px; padding-top: 30px; padding-bottom: 30px; border-radius: 10px;">
                                        {{ $codeParts[0] }}
                                    </p>
                                </td>
                                <td style="text-align: center; vertical-align: middle; font-size: 18px; font-weight: 700">
                                    <p style="border: 1px dashed {{ config('mail_theme.colors.primary') }}; color: {{ config('mail_theme.colors.primary') }}; margin-left: 10px; margin-right: 10px; padding-top: 30px; padding-bottom: 30px; border-radius: 10px;">
                                        {{ $codeParts[1] }}
                                    </p>
                                </td>
                                <td style="text-align: center; vertical-align: middle; font-size: 18px; font-weight: 700">
                                    <p style="border: 1px dashed {{ config('mail_theme.colors.primary') }}; color: {{ config('mail_theme.colors.primary') }}; margin-left: 10px; margin-right: 10px; padding-top: 30px; padding-bottom: 30px; border-radius: 10px;">
                                        {{ $codeParts[2] }}
                                    </p>
                                </td>
                                <td style="text-align: center; vertical-align: middle; font-size: 18px; font-weight: 700">
                                    <p style="border: 1px dashed {{ config('mail_theme.colors.primary') }}; color: {{ config('mail_theme.colors.primary') }}; margin-left: 10px; margin-right: 10px; padding-top: 30px; padding-bottom: 30px; border-radius: 10px;">
                                        {{ $codeParts[3] }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align:center;padding: 20px 30px 40px">
                    <p>If you didn't initiate this registration, please ignore this email.</p>
                    <p style="margin-bottom: 15px;">We’re excited to have you join us!</p>
                    <p style="margin: 0; font-size: 13px; line-height: 22px; color:#9ea8bb;">
                        This is an automatically generated email please do not reply to this email. If you face any issues, please contact us at {{ config('mail_theme.appContactEmail') }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
@endsection