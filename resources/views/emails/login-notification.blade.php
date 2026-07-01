<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Alert: New Login Detected</title>
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
            background-color: rgba(239, 68, 68, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .banner .icon-inner {
            width: 10px;
            height: 10px;
            background-color: #ef4444;
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

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .details-table td {
            padding: 13px 18px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .details-table tr:last-child td {
            border-bottom: none;
        }

        .details-label {
            font-weight: 700;
            color: #64748b;
            width: 120px;
            font-size: 13px;
            white-space: nowrap;
        }

        .warning-box {
            background-color: #fef2f2;
            border-left: 3px solid #ef4444;
            padding: 14px 16px;
            border-radius: 0 10px 10px 0;
            margin-top: 24px;
        }

        .warning-box p {
            margin: 0;
            font-size: 13px;
            color: #991b1b;
            line-height: 1.55;
            font-weight: 500;
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

            .details-table,
            .details-table tr,
            .details-table td {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            .details-table tr {
                padding: 10px 14px;
            }

            .details-table td {
                padding: 2px 0;
                border-bottom: none;
            }

            .details-table tr:not(:last-child) {
                border-bottom: 1px solid #e2e8f0;
            }

            .details-label {
                width: auto;
                font-size: 12px;
                padding-top: 10px;
            }

            .details-table td:last-child {
                padding-bottom: 10px;
                word-break: break-all;
            }

            .warning-box p {
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
                <h1>New Login Detected</h1>
                <p>Security Alert</p>
            </div>
        </div>

        <div class="card">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>
            <p>We are writing to inform you that a new login was recorded for your Super Admin account on the
                TPA System. The details of this sign-in are provided below for your reference.</p>

            <table class="details-table">
                <tr>
                    <td class="details-label">Date &amp; Time</td>
                    <td>{{ $time }}</td>
                </tr>
                <tr>
                    <td class="details-label">IP Address</td>
                    <td><code
                            style="background:#e2e8f0;padding:2px 6px;border-radius:4px;font-size:13px;">{{ $ipAddress }}</code>
                    </td>
                </tr>
                <tr>
                    <td class="details-label">User Agent</td>
                    <td style="font-size:13px;">{{ $userAgent }}</td>
                </tr>
            </table>

            <div class="warning-box">
                <p>If you do not recognize this activity, please reset your password and secure your account
                    immediately. If you require further assistance, contact our support team without delay.</p>
            </div>

            <p class="signature">Sincerely,<br><strong>TPA Security Alert Engine</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated security notification. Please do not reply to this email.</p>
            <p class="legal">&copy; {{ date('Y') }} ECYES TPA SYSTEM &middot; All rights reserved.</p>
        </div>
    </div>
</body>

</html>