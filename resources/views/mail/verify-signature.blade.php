<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ __('Verify your signature') }}</title>
    <style>
        /* Minimalist Reset */
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; font-size: 16px; line-height: 1.5; margin: 0; padding: 0; background-color: #f3f4f6; color: #374151; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background-color: #ffffff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); }
        .button { display: inline-block; padding: 12px 24px; background-color: #111827; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 16px; margin-bottom: 16px; }
        .footer { margin-top: 32px; font-size: 14px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 style="font-size: 24px; font-weight: 700; margin-top: 0; margin-bottom: 16px; color: #111827;">
                {{ __('Verify your signature') }}
            </h1>

            @if(isset($campaignName))
                <p style="margin-bottom: 16px;">
                    {{ __('Thank you for supporting') }} <strong>{{ $campaignName }}</strong>.
                </p>
            @endif

            <p style="margin-bottom: 24px;">
                {{ __('Please click the button below to verify your email address and confirm your signature.') }}
            </p>

            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                <tr>
                    <td align="left">
                        <a href="{{ $verificationUrl }}" class="button">{{ __('Verify Email') }}</a>
                    </td>
                </tr>
            </table>

            <p style="font-size: 14px; color: #6b7280; margin-bottom: 0; margin-top: 24px;">
                {{ __('If you did not sign this campaign, you can safely ignore this email.') }}
            </p>
        </div>

        <div class="footer">
            <p>{{ __('Powered by Voces') }}</p>
        </div>
    </div>
</body>
</html>
