<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
        }

        .banner {
            margin: 0 20px;
            padding: 24px 36px;
            background-color: #0f172a;
            border-radius: 16px 16px 0 0;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .banner img {
            height: 32px;
            width: auto;
            object-fit: contain;
        }

        .banner .divider {
            width: 1px;
            height: 28px;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .banner .icon {
            width: 36px;
            height: 36px;
            background-color: rgba(79, 70, 229, 0.18);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .banner .icon-inner {
            width: 10px;
            height: 10px;
            background-color: #818cf8;
            border-radius: 50%;
        }

        .banner-text h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.2px;
        }

        .banner-text p {
            margin: 2px 0 0;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .card {
            background: #ffffff;
            border-radius: 0 0 16px 16px;
            margin: 0 20px;
            padding: 36px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            border-top: none;
        }

        .card p {
            font-size: 15px;
            color: #475569;
            line-height: 1.65;
            margin: 0 0 20px;
        }

        .button-wrapper {
            text-align: center;
            margin: 28px 0;
        }

        .btn-primary {
            display: inline-block;
            padding: 13px 32px;
            background: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border-radius: 10px;
            letter-spacing: 0.01em;
        }

        .link-fallback {
            font-size: 12px;
            color: #94a3b8;
            word-break: break-all;
            margin-top: 8px;
            text-align: center;
        }

        .link-fallback a {
            color: #4f46e5;
            text-decoration: none;
        }

        .info-box {
            background-color: #f8fafc;
            border-left: 3px solid #94a3b8;
            padding: 14px 16px;
            border-radius: 0 10px 10px 0;
            margin-top: 24px;
        }

        .info-box p {
            margin: 0;
            font-size: 13px;
            color: #64748b;
            line-height: 1.55;
        }

        .signature {
            margin-top: 36px;
            margin-bottom: 0;
        }

        .footer {
            padding: 28px 36px;
            text-align: center;
        }

        .footer p {
            font-size: 12px;
            color: #94a3b8;
            margin: 0;
            line-height: 1.6;
        }

        .footer p.legal {
            margin-top: 6px;
            font-size: 11px;
            color: #cbd5e1;
        }

        /* ===== Mobile styles ===== */
        @media only screen and (max-width: 600px) {
            .wrapper {
                width: 100% !important;
                margin: 0 auto !important;
            }

            .banner {
                margin: 0 12px;
                padding: 20px;
                border-radius: 14px 14px 0 0;
                flex-wrap: wrap;
                gap: 10px;
            }

            .banner img {
                height: 26px;
            }

            .banner .divider {
                display: none;
            }

            .banner .icon {
                width: 32px;
                height: 32px;
            }

            .banner-text h1 {
                font-size: 16px;
            }

            .banner-text p {
                font-size: 11px;
            }

            .card {
                margin: 0 12px;
                padding: 22px 18px;
                border-radius: 0 0 14px 14px;
            }

            .card p {
                font-size: 14px;
                line-height: 1.6;
            }

            .button-wrapper {
                margin: 22px 0;
            }

            .btn-primary {
                display: block;
                width: 100%;
                box-sizing: border-box;
                padding: 14px 0;
                font-size: 15px;
            }

            .info-box p {
                font-size: 12.5px;
            }

            .footer {
                padding: 22px 16px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="banner">
            <img src="{{ $message->embed(public_path('1.png')) }}" alt="TPA System">
            <div class="divider"></div>
            <div class="icon">
                <div class="icon-inner"></div>
            </div>
            <div class="banner-text">
                <h1>Password Reset Request</h1>
                <p>Account Security</p>
            </div>
        </div>

        <div class="card">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>
            <p>We received a request to reset the password for your TPA System account. To proceed, please
                click the button below to choose a new password.</p>

            <div class="button-wrapper">
                <a href="{{ $resetUrl }}" class="btn-primary" target="_blank">Reset Password</a>
            </div>

            <p class="link-fallback">If the button above does not work, copy and paste this link into your
                browser:<br><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>

            <div class="info-box">
                <p>This link will expire in 60 minutes for your security. If you did not request a password
                    reset, no further action is required and your password will remain unchanged.</p>
            </div>

            <p class="signature">Warm regards,<br><strong>TPA System Security Team</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated security notification. Please do not reply to this email.</p>
            <p class="legal">&copy; {{ date('Y') }} ECYES TPA SYSTEM &middot; All rights reserved.</p>
        </div>
    </div>
</body>

</html>