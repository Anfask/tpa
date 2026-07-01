<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your TPA Account Details</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0f172a;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .brand-section img {
            height: 36px;
            width: auto;
            object-fit: contain;
        }

        .brand-logo-fallback {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: #ffffff;
            font-size: 20px;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #818cf8, #4f46e5);
            padding: 8px 16px;
            border-radius: 12px;
            display: inline-block;
        }

        .header-title {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 8px;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            font-size: 14px;
            font-weight: 600;
            color: #818cf8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 24px;
        }

        p {
            font-size: 15px;
            color: #cbd5e1;
            line-height: 1.7;
            margin: 0 0 20px;
        }

        .credentials-box {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 24px;
            border-radius: 16px;
            margin: 32px 0;
        }

        .credentials-row {
            margin-bottom: 16px;
        }

        .credentials-row:last-child {
            margin-bottom: 0;
        }

        .credentials-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .credentials-value {
            color: #38bdf8;
            font-weight: 700;
            font-size: 16px;
            font-family: 'Outfit', 'Courier New', Courier, monospace;
            background: rgba(56, 189, 248, 0.1);
            padding: 8px 14px;
            border-radius: 8px;
            display: inline-block;
            border: 1px solid rgba(56, 189, 248, 0.15);
        }

        .button-wrapper {
            text-align: center;
            margin: 32px 0;
        }

        .btn-primary {
            display: inline-block;
            padding: 14px 36px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            border-radius: 12px;
            letter-spacing: 0.01em;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.25);
            transition: all 0.2s ease;
        }

        .info-box {
            background-color: rgba(245, 158, 11, 0.08);
            border-left: 4px solid #f59e0b;
            padding: 16px 20px;
            border-radius: 0 12px 12px 0;
            margin-top: 32px;
        }

        .info-box p {
            margin: 0;
            font-size: 13px;
            color: #f59e0b;
            line-height: 1.6;
        }

        .signature {
            margin-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            padding-top: 24px;
            font-size: 14px;
            color: #94a3b8;
        }

        .signature strong {
            color: #ffffff;
        }

        .footer {
            padding: 32px 0;
            text-align: center;
        }

        .footer p {
            font-size: 12px;
            color: #64748b;
            margin: 0;
            line-height: 1.6;
        }

        .footer p.legal {
            margin-top: 8px;
            font-size: 11px;
            color: #475569;
        }

        /* ===== Mobile styles ===== */
        @media only screen and (max-width: 600px) {
            .wrapper {
                padding: 0 12px;
                margin: 20px auto;
            }

            .card {
                padding: 24px 20px;
                border-radius: 20px;
            }

            .header-title {
                font-size: 22px;
            }

            .btn-primary {
                display: block;
                width: 100%;
                box-sizing: border-box;
                padding: 14px 0;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="card">
            <div class="brand-section">
                @if(file_exists(public_path('1.png')))
                    <img src="{{ $message->embed(public_path('1.png')) }}" alt="TPA System">
                @else
                    <div class="brand-logo-fallback">TPA SYSTEM</div>
                @endif
            </div>

            <div class="header-title">Welcome to TPA</div>
            <div class="header-subtitle">Account Configuration</div>

            <p>Dear <strong>{{ $user->name }}</strong>,</p>
            <p>An administrator has created an account for you on the Teachers Performance Analysis (TPA) System. You can find your secure temporary credentials below:</p>

            <div class="credentials-box">
                <div class="credentials-row">
                    <span class="credentials-label">Email Address</span>
                    <span class="credentials-value" style="color:#ffffff;">{{ $user->email }}</span>
                </div>
                <div class="credentials-row">
                    <span class="credentials-label">Temporary Password</span>
                    <span class="credentials-value">{{ $tempPassword }}</span>
                </div>
            </div>

            <div class="button-wrapper">
                <a href="{{ route('login') }}" class="btn-primary" target="_blank">Access Portal Login</a>
            </div>

            <div class="info-box">
                <p><strong>Security Notice:</strong> For security purposes, we highly recommend changing your password immediately after your first login via the Settings tab in your profile dashboard.</p>
            </div>

            <div class="signature">
                Warm regards,<br>
                <strong>TPA System Administration Team</strong>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated notification regarding your account setup. Please do not reply directly to this email.</p>
            <p class="legal">&copy; {{ date('Y') }} ECYES TPA SYSTEM &middot; All rights reserved.</p>
        </div>
    </div>
</body>

</html>
