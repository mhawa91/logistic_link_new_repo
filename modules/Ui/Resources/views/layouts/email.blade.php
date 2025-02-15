<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title : 'Email' }}</title>
</head>

<body>
    <div class="rps_25a5">
        <div
            style="font-family: arial, sans-serif, serif, EmojiFont; color: rgb(71, 71, 71); padding: 2%; background-color: rgb(246, 246, 246);">
            <table border="0" cellspacing="0" cellpadding="0"
                style="width:100%; max-width:680px; margin:0px auto; background:white; font-size:14px; line-height:1.5">
                <tbody>
                    <tr>
                        <td align="center" style="padding:45px 45px 0 45px;">
                            <a
                                href="{{ url('/') }}" target="_blank" rel="noopener noreferrer"
                                data-auth="NotApplicable" title="{{ __( config('app.name') ) }}" style="text-decoration:none"
                                data-linkindex="0">
                                <img data-imagetype="External"
                                    src="{{ asset( config('ui.logos.' . $_LOCALE_ . '.email.publicPath') ) }}" alt="{{ __( config('app.name') ) }}"
                                    style="max-width:60%; min-width:130px; max-width:150px">
                            </a> 
                        </td>
                        <td align="right"></td>
                    </tr>
                    <tr>
                        <td align="left" colspan="2" style="padding:0 45px 20px 45px;">

                            <hr style="background-color:#f02125; height:2px; border:0; margin:24px 0">

                            @yield('content')

                            <hr style="background-color:#f02125; height:2px; border:0; margin:24px 0">
                            {{-- <pre align="left" style="font-size:10px; color:#000000">Anti phishing code: </pre> --}}
                            @if (
                                $SystemConfig->of('facebook', null)
                                || $SystemConfig->of('twitter', null)
                                // || $SystemConfig->of('linkedin', null)
                                || $SystemConfig->of('youtube', null)
                                || $SystemConfig->of('facebook', null)
                                || $SystemConfig->of('facebook', null)
                            )
                                <p align="center" style="margin-bottom:24px">
                                    @if ($x = $SystemConfig->of('facebook', null))
                                        <a href="//{!! str_replace(['http://', 'https://'], '', $x) !!}"
                                            target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" title="facebook"
                                            style="margin-right:24px; text-decoration:none;" data-linkindex="2">
                                            <img data-imagetype="External"
                                            src="{{ asset('modules/ui/global/email/icons/facebook.png') }}" height="20" width="20"
                                            alt="facebook">
                                        </a> 
                                    @endif
                                    @if ($x = $SystemConfig->of('twitter', null))
                                        <a href="//{!! str_replace(['http://', 'https://'], '', $x) !!}"
                                            target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" title="twitter"
                                            style="margin-right:24px; text-decoration:none;" data-linkindex="4">
                                            <img data-imagetype="External"
                                            src="{{ asset('modules/ui/global/email/icons/twitter.png') }}" height="20" width="20"
                                            alt="twitter">
                                        </a> 
                                    @endif
                                    {{-- @if ($x = $SystemConfig->of('linkedin', null))
                                        <a href="//{!! str_replace(['http://', 'https://'], '', $x) !!}" target="_blank"><i class="fa fa-linkedin"></i></a>
                                    @endif --}}
                                    @if ($x = $SystemConfig->of('instagram', null))
                                        <a href="//{!! str_replace(['http://', 'https://'], '', $x) !!}"
                                            target="_blank" rel="noopener noreferrer" data-auth="NotApplicable"
                                            title="instagram" style="margin-right:24px; text-decoration:none;" data-linkindex="3">
                                            <img
                                            data-imagetype="External"
                                            src="{{ asset('modules/ui/global/email/icons/instagram.png') }}" height="20" width="20"
                                            alt="instagram">
                                        </a> 
                                    @endif
                                    @if ($x = $SystemConfig->of('youtube', null))
                                        <a href="//{!! str_replace(['http://', 'https://'], '', $x) !!}"
                                            target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" title="youtube"
                                            style="margin-right:24px; text-decoration:none;" data-linkindex="5">
                                            <img data-imagetype="External"
                                            src="{{ asset('modules/ui/global/email/icons/youtube.png') }}" height="20" width="20"
                                            alt="youtube">
                                        </a> 
                                    @endif
                                    
                                    {{-- <a href="#" target="_blank"
                                    rel="noopener noreferrer" data-auth="NotApplicable" title="discord"
                                    style="margin-right:24px; text-decoration:none;" data-linkindex="6">
                                    <img data-imagetype="External"
                                        src="{{ asset('modules/ui/global/email/icons/discord.png') }}" height="20" width="20"
                                        alt="discord">
                                    </a> 
                                    <a href="#" target="_blank"
                                        rel="noopener noreferrer" data-auth="NotApplicable" title="reddit"
                                        style="margin-right:24px; text-decoration:none;" data-linkindex="7">
                                        <img data-imagetype="External"
                                        src="{{ asset('modules/ui/global/email/icons/reddit.png') }}" height="20" width="20"
                                        alt="reddit">
                                    </a> 
                                    <a href="#" target="_blank" style="text-decoration:none;"
                                        rel="noopener noreferrer" data-auth="NotApplicable" title="vk"
                                        data-linkindex="8">
                                        <img data-imagetype="External"
                                        src="{{ asset('modules/ui/global/email/icons/vk.png') }}" height="20" width="20"
                                        alt="vk">
                                    </a>  --}}
                                </p>
                                {{-- <p align="left" style="margin-bottom:24px">
                                    <a
                                        href="#"
                                        target="_blank" rel="noopener noreferrer" data-auth="NotApplicable"
                                        title="playstore" style="margin-right:24px; text-decoration:none;" data-linkindex="9">
                                        <img
                                            data-imagetype="External"
                                            src="{{ asset('modules/ui/global/email/icons/footer_gplay.png') }}" height="40"
                                            width="121" alt="playstore">
                                    </a> 
                                    <a
                                        href="#" target="_blank"
                                        rel="noopener noreferrer" data-auth="NotApplicable" title="appstore"
                                        style="margin-right:24px; text-decoration:none;" data-linkindex="10">
                                        <img data-imagetype="External"
                                            src="{{ asset('modules/ui/global/email/icons/footer_appstore.png') }}" height="40"
                                            width="121" alt="appstore">
                                    </a> 
                                </p> --}}
                            @endif
                            
                            <p align="center" style="font-size:11px; color:#959595">
                                Do not reply to this e-mail because it is unmonitored.
                            </p>
                            <p align="center" style="font-size:11px; color:#959595">
                                {{ __('ui::strings.all_rights_reserved') }} © {{ __( config('app.name') ) }} {{ date('Y') }}
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>