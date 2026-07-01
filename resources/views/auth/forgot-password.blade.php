<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - TPA</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('2.png') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        :root {
            --bg-color: #080b11;
            --card-bg: rgba(13, 17, 28, 0.7);
            --card-border: rgba(255, 255, 255, 0.07);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-primary: #4f46e5;
            --accent-secondary: #0ea5e9;
            --accent-gradient: linear-gradient(135deg, #4f46e5, #0ea5e9);
            --btn-hover-gradient: linear-gradient(135deg, #4338ca, #0284c7);
            --error-color: #f43f5e;
            --success-color: #10b981;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Glowing Blobs in Background */
        .bg-blobs {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.45;
            animation: float 20s infinite alternate ease-in-out;
        }

        .blob-1 {
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, var(--accent-primary) 0%, rgba(79, 70, 229, 0) 70%);
            animation-delay: 0s;
        }

        .blob-2 {
            bottom: -10%;
            right: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, var(--accent-secondary) 0%, rgba(14, 165, 233, 0) 70%);
            animation-delay: -5s;
        }

        .blob-3 {
            top: 40%;
            left: 50%;
            width: 35vw;
            height: 35vw;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.4) 0%, rgba(168, 85, 247, 0) 70%);
            animation-delay: -10s;
        }

        @keyframes float {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.95);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        /* Glassmorphism Container */
        .auth-container {
            width: 90%;
            max-width: 1000px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            z-index: 2;
            opacity: 0;
            transform: translateY(30px);
            animation: slideUpFade 0.8s forwards cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUpFade {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Branding Panel */
        .auth-left {
            flex: 1;
            padding: 3.5rem;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.15), rgba(14, 165, 233, 0.05));
            border-right: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Left Side Glass Grid Pattern */
        .auth-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
        }

        .brand-logo-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            z-index: 2;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUpFade 0.8s forwards 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
            transition: transform 0.5s ease;
        }

        .brand-logo:hover {
            transform: rotate(10deg) scale(1.05);
        }

        .brand-logo img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff 30%, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.25;
        }

        .brand-description-wrapper {
            margin: 3rem 0;
            z-index: 2;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUpFade 0.8s forwards 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .brand-subtitle {
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }

        .brand-desc {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .brand-footer {
            font-size: 0.8rem;
            color: var(--text-secondary);
            letter-spacing: 1.5px;
            opacity: 0.7;
            text-transform: uppercase;
            z-index: 2;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUpFade 0.8s forwards 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Right Form Panel */
        .auth-right {
            flex: 1.2;
            padding: 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUpFade 0.8s forwards 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .auth-right h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-right .form-subtitle {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            line-height: 1.5;
        }

        /* Floating Input Fields */
        .form-group {
            position: relative;
            margin-bottom: 1.75rem;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .form-input {
            width: 100%;
            padding: 1.1rem 1rem 1rem 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-input::placeholder {
            color: transparent;
            /* Required for floating label */
        }

        .form-label {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 0.95rem;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Label float states */
        .form-input:focus~.form-label,
        .form-input:not(:placeholder-shown)~.form-label {
            top: 25%;
            font-size: 0.75rem;
            color: var(--accent-secondary);
            transform: translateY(-50%);
            font-weight: 600;
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--accent-secondary);
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.15);
        }

        /* Dynamic neon bar indicator on focus */
        .input-glow-bar {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--accent-gradient);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .form-input:focus~.input-glow-bar {
            width: 90%;
        }

        .check-icon-valid {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--success-color);
            font-size: 0.95rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .form-input:not(:placeholder-shown):valid~.check-icon-valid {
            opacity: 1;
        }

        /* Back to login link styling */
        .back-to-login-wrapper {
            text-align: center;
            margin-top: 2rem;
        }

        .back-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .back-link i {
            transition: transform 0.3s ease;
        }

        .back-link:hover {
            color: var(--accent-secondary);
            text-shadow: 0 0 10px rgba(14, 165, 233, 0.3);
        }

        .back-link:hover i {
            transform: translateX(-4px);
        }

        /* Submit Button Styling & Animations */
        .btn-wrapper {
            margin-top: 1.5rem;
            position: relative;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 14px;
            background: var(--accent-gradient);
            color: white;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.35);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.2),
                    transparent);
            transition: 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.5);
            background: var(--btn-hover-gradient);
        }

        .submit-btn:active {
            transform: translateY(1px);
        }

        /* Turnstile container styling */
        .turnstile-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            border-radius: 12px;
            overflow: hidden;
        }

        /* Flash Message Toasts */
        .alerts-wrapper {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 1000;
            width: 90%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
            animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            position: relative;
            overflow: hidden;
        }

        .alert-success {
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            border-left: 4px solid var(--error-color);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        .alert-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 1.2rem;
            margin-left: auto;
            line-height: 1;
            padding: 0 4px;
            transition: color 0.2s;
        }

        .alert-close:hover {
            color: var(--text-primary);
        }

        /* Responsiveness */
        @media (max-width: 850px) {
            body {
                padding: 1.5rem 0;
            }

            .auth-container {
                flex-direction: column;
                max-width: 480px;
                border-radius: 20px;
            }

            .auth-left {
                padding: 2.5rem 2rem;
                border-right: none;
                border-bottom: 1px solid var(--card-border);
                align-items: center;
                text-align: center;
            }

            .brand-logo-wrapper {
                align-items: center;
                margin-bottom: 0;
            }

            .brand-description-wrapper {
                margin: 1.5rem 0;
            }

            .auth-right {
                padding: 2.5rem 2rem;
            }
        }
    </style>
</head>

<body>

    <!-- Background Blobs -->
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- Floating Alerts -->
    <div class="alerts-wrapper">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check" style="color: var(--success-color)"></i>
                <span style="flex: 1">{{ session('success') }}</span>
                <button class="alert-close" onclick="dismissAlert(this.parentElement)">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation" style="color: var(--error-color)"></i>
                <span style="flex: 1">{{ session('error') }}</span>
                <button class="alert-close" onclick="dismissAlert(this.parentElement)">&times;</button>
            </div>
        @endif
        @if($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation" style="color: var(--error-color)"></i>
                    <span style="flex: 1">{{ $error }}</span>
                    <button class="alert-close" onclick="dismissAlert(this.parentElement)">&times;</button>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Main Container -->
    <div class="auth-container">

        <!-- Left Branding Panel -->
        <div class="auth-left">
            <div class="brand-logo-wrapper">
                <div class="brand-logo">
                    <img src="{{ asset('1.png') }}" alt="TPA Logo">
                </div>
                <h1 class="brand-name">Teachers Performance Analysis</h1>
            </div>

            <div class="brand-description-wrapper">
                <h2 class="brand-subtitle">Forgot Password?</h2>
                <p class="brand-desc">
                    No worries! Just enter your registered email address below, and we will send you a secure link to
                    reset your password.
                </p>
            </div>

            <div class="brand-footer">
                ECYES TPA SYSTEM &copy; {{ date('Y') }}
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="auth-right">
            <h2>Reset Password</h2>
            <p class="form-subtitle">Enter your email to receive recovery instructions</p>

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                <!-- Email Input -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <input class="form-input" type="email" id="email" name="email" placeholder="Email Address"
                            value="{{ old('email') }}" required autocomplete="username">
                        <label class="form-label" for="email">Email Address</label>
                        <i class="fa-solid fa-circle-check check-icon-valid"></i>
                        <div class="input-glow-bar"></div>
                    </div>
                </div>

                <!-- Turnstile Widget -->
                <div class="turnstile-container">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}"
                        data-theme="dark"></div>
                </div>

                <!-- Actions -->
                <div class="btn-wrapper">
                    <button type="submit" class="submit-btn">
                        <span>Send Reset Link</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>

                <!-- Back to Login -->
                <div class="back-to-login-wrapper">
                    <a href="{{ route('login') }}" class="back-link">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Back to Login</span>
                    </a>
                </div>
            </form>
        </div>

    </div>

    <script>
        function dismissAlert(alertEl) {
            alertEl.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            alertEl.style.opacity = '0';
            alertEl.style.transform = 'translateX(100px) scale(0.9)';
            setTimeout(() => alertEl.remove(), 400);
        }

        // Auto-dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => dismissAlert(alert));
        }, 5000);
    </script>
</body>

</html>