<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $heading }} - {{ $appName }}</title>
    <style>
        @media only screen and (max-width: 480px) {
            .email-content { padding: 32px 24px 30px !important; }
            .email-heading { font-size: 25px !important; line-height: 32px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f7f7; color: #263333; font-family: Arial, Helvetica, sans-serif;">
    <div style="display: none; max-height: 0; overflow: hidden; opacity: 0; color: transparent;">{{ $previewText }}</div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; background-color: #f3f7f7;">
        <tr>
            <td align="center" style="padding: 32px 16px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="width: 100%; max-width: 600px; table-layout: fixed;">
                    <tr>
                        <td align="center" style="padding: 0 0 24px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td aria-hidden="true" align="center" valign="middle" style="width: 42px; height: 42px; border-radius: 12px; background-color: #008080; color: #ffffff; font-size: 25px; font-weight: bold; line-height: 42px;">◇</td>
                                    <td style="padding-left: 12px; color: #163636; font-size: 21px; font-weight: 700; letter-spacing: -0.3px;">{{ $appName }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="overflow: hidden; border: 1px solid #dce8e8; border-radius: 16px; background-color: #ffffff; box-shadow: 0 8px 24px rgba(17, 72, 72, 0.08);">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width: 100%; table-layout: fixed;">
                                <tr><td style="height: 6px; background-color: #008080; font-size: 0; line-height: 0;">&nbsp;</td></tr>
                                <tr>
                                    <td class="email-content" style="padding: 42px 44px 38px;">
                                        <p style="margin: 0 0 18px; color: #496060; font-size: 16px; line-height: 25px;">{{ $greeting }}</p>
                                        <h1 class="email-heading" style="margin: 0 0 18px; color: #163636; font-size: 28px; line-height: 35px; letter-spacing: -0.5px;">{{ $heading }}</h1>
                                        <p style="margin: 0 0 28px; color: #496060; font-size: 16px; line-height: 25px;">{{ $intro }}</p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 0 0 26px;">
                                            <tr>
                                                <td align="center" style="border-radius: 9px; background-color: #008080;">
                                                    <a href="{{ $actionUrl }}" style="display: inline-block; padding: 14px 24px; color: #ffffff; font-size: 16px; font-weight: 700; line-height: 20px; text-decoration: none;">{{ $actionText }}</a>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="margin: 0 0 12px; color: #687c7c; font-size: 14px; line-height: 22px;">{{ $expiration }}</p>
                                        <p style="margin: 0; color: #687c7c; font-size: 14px; line-height: 22px;">{{ $outro }}</p>
                                        <div style="height: 1px; margin: 30px 0 22px; background-color: #e4ecec;"></div>
                                        <p style="margin: 0 0 8px; color: #829191; font-size: 12px; line-height: 18px;">{{ __('mail.common.fallback', ['action' => $actionText]) }}</p>
                                        <p style="margin: 0; overflow-wrap: anywhere; word-break: break-all; color: #008080; font-size: 12px; line-height: 18px;"><a href="{{ $actionUrl }}" style="color: #008080; text-decoration: underline; overflow-wrap: anywhere; word-break: break-all;">{{ $actionUrl }}</a></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 22px 16px 0; color: #829191; font-size: 12px; line-height: 18px;">
                            &copy; {{ date('Y') }} {{ $appName }}. {{ __('mail.common.rights') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
