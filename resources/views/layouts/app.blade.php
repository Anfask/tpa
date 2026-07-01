<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TPA - Teachers Performance Analysis')</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Chart.js CDN for Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('2.png') }}">
</head>

<body class="{{ session('theme', 'dark-theme') }}">

    <!-- Background Blobs -->
    <div class="bg-blobs"
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; overflow: hidden; z-index: 0; pointer-events: none;">
        <div class="blob blob-1"
            style="position: absolute; border-radius: 50%; filter: blur(120px); opacity: 0.22; animation: float 20s infinite alternate ease-in-out; top: -10%; left: -10%; width: 40vw; height: 40vw; background: radial-gradient(circle, var(--primary) 0%, rgba(99, 102, 241, 0) 70%);">
        </div>
        <div class="blob blob-2"
            style="position: absolute; border-radius: 50%; filter: blur(120px); opacity: 0.22; animation: float 20s infinite alternate ease-in-out; animation-delay: -5s; bottom: -10%; right: -10%; width: 40vw; height: 40vw; background: radial-gradient(circle, var(--secondary) 0%, rgba(14, 165, 233, 0) 70%);">
        </div>
    </div>

    <div class="app-container" style="position: relative; z-index: 1;">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('1.png') }}" alt="Icon" style="width: 40px; height: 40px; object-fit: contain;">
                <div class="brand-text">Teachers Performance Analysis</div>
            </div>

            <ul class="sidebar-menu">
                @if(auth()->user()->isSuperAdmin())
                    <!-- Super Admin Menu -->
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.dashboard') }}"
                            class="sidebar-link {{ Route::is('super-admin.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-line"></i> Dashboard
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.campuses') }}"
                            class="sidebar-link {{ Route::is('super-admin.campuses*') ? 'active' : '' }}">
                            <i class="fa-solid fa-school"></i> Campuses
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.admins') }}"
                            class="sidebar-link {{ Route::is('super-admin.admins*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-shield"></i> Admins
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.teachers') }}"
                            class="sidebar-link {{ Route::is('super-admin.teachers*') ? 'active' : '' }}">
                            <i class="fa-solid fa-chalkboard-user"></i> Teachers
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.classes') }}"
                            class="sidebar-link {{ Route::is('super-admin.classes*') ? 'active' : '' }}">
                            <i class="fa-solid fa-graduation-cap"></i> Classes
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.inspection-config') }}"
                            class="sidebar-link {{ Route::is('super-admin.inspection-config*') ? 'active' : '' }}">
                            <i class="fa-solid fa-sliders"></i> Inspection Config
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.admin-inspection') }}"
                            class="sidebar-link {{ Route::is('super-admin.admin-inspection*') ? 'active' : '' }}">
                            <i class="fa-solid fa-clipboard-check"></i> Inspect Admin
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.monitoring') }}"
                            class="sidebar-link {{ Route::is('super-admin.monitoring*') ? 'active' : '' }}">
                            <i class="fa-solid fa-eye"></i> Monitoring
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.reports') }}"
                            class="sidebar-link {{ Route::is('super-admin.reports*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Reports & Export
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('super-admin.settings') }}"
                            class="sidebar-link {{ Route::is('super-admin.settings*') ? 'active' : '' }}">
                            <i class="fa-solid fa-gear"></i> Settings
                        </a>
                    </li>
                @elseif(auth()->user()->isAdmin())
                    <!-- Admin Menu -->
                    <li class="sidebar-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="sidebar-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-line"></i> Dashboard
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('admin.teachers') }}"
                            class="sidebar-link {{ Route::is('admin.teachers*') ? 'active' : '' }}">
                            <i class="fa-solid fa-chalkboard-user"></i> Teachers
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('admin.teacher-inspection') }}"
                            class="sidebar-link {{ Route::is('admin.teacher-inspection*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-signature"></i> Teacher Inspection
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('admin.campus-inspection') }}"
                            class="sidebar-link {{ Route::is('admin.campus-inspection*') ? 'active' : '' }}">
                            <i class="fa-solid fa-building-circle-check"></i> Campus Inspection
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('admin.performance') }}"
                            class="sidebar-link {{ Route::is('admin.performance*') ? 'active' : '' }}">
                            <i class="fa-solid fa-medal"></i> My Performance
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('admin.reports') }}"
                            class="sidebar-link {{ Route::is('admin.reports*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Reports & Export
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('admin.settings') }}"
                            class="sidebar-link {{ Route::is('admin.settings*') ? 'active' : '' }}">
                            <i class="fa-solid fa-gear"></i> Settings
                        </a>
                    </li>
                @elseif(auth()->user()->isTeacher())
                    <!-- Teacher Menu -->
                    <li class="sidebar-item">
                        <a href="{{ route('teacher.dashboard') }}"
                            class="sidebar-link {{ Route::is('teacher.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-line"></i> Dashboard
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('teacher.profile') }}"
                            class="sidebar-link {{ Route::is('teacher.profile*') ? 'active' : '' }}">
                            <i class="fa-solid fa-address-card"></i> My Profile
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('teacher.scores') }}"
                            class="sidebar-link {{ Route::is('teacher.scores*') ? 'active' : '' }}">
                            <i class="fa-solid fa-star"></i> My Scores
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('teacher.settings') }}"
                            class="sidebar-link {{ Route::is('teacher.settings*') ? 'active' : '' }}">
                            <i class="fa-solid fa-gear"></i> Settings
                        </a>
                    </li>
                @endif
            </ul>

            <div class="sidebar-footer">
                <div class="profile-card">
                    <div class="profile-info">
                        <div class="profile-name">{{ auth()->user()->name }}</div>
                        <div class="profile-role">
                            @if(auth()->user()->isSuperAdmin())
                                Super Admin
                            @elseif(auth()->user()->isAdmin())
                                Admin ({{ auth()->user()->campus->name ?? 'None' }})
                            @else
                                Teacher
                            @endif
                        </div>
                    </div>
                    <!-- Logout action link -->
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="btn-icon" style="border: none; background: transparent; width: auto; height: auto;"
                        title="Logout">
                        <i class="fa-solid fa-right-from-bracket" style="color: var(--danger)"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="btn-icon menu-toggle" id="sidebar-toggle" title="Toggle Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="header-title-container">
                        <h1>@yield('header_title', 'Dashboard')</h1>
                        <p class="header-subtitle">@yield('header_subtitle', 'TPA System Hub')</p>
                    </div>
                </div>

                <div class="header-actions">
                    <!-- Theme Toggle Button -->
                    <button class="btn-icon" id="theme-toggle" title="Toggle Light/Dark Mode">
                        <i class="fa-solid fa-moon"></i>
                    </button>

                    <!-- Notification Bell -->
                    <div class="notif-wrapper">
                        @php
                            $notifs = \App\Models\Notification::where('user_id', auth()->id())
                                ->where('is_read', false)
                                ->orderBy('created_at', 'desc')
                                ->get();
                        @endphp
                        <button class="btn-icon" id="notification-bell" title="Notifications">
                            <i class="fa-solid fa-bell"></i>
                            @if($notifs->count() > 0)
                                <span class="notif-badge">{{ $notifs->count() }}</span>
                            @endif
                        </button>

                        <!-- Notifications Dropdown -->
                        <div class="notifications-dropdown" id="notifications-panel">
                            <div class="notifications-header">Notifications</div>
                            <div class="notifications-list">
                                @forelse(\App\Models\Notification::where('user_id', auth()->id())->orderBy('created_at', 'desc')->take(10)->get() as $n)
                                    <div class="notification-item {{ $n->is_read ? '' : 'unread' }}"
                                        onclick="markAsRead(this, {{ $n->id }})">
                                        <h4>{{ $n->title }}</h4>
                                        <p>{{ $n->message }}</p>
                                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">
                                            {{ $n->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @empty
                                    <div style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                                        No notifications found.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Alerts for Flash Messages (Toasts) -->
            <div class="toast-container">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <div class="alert-content">{{ session('success') }}</div>
                        <button class="alert-close"
                            onclick="this.parentElement.style.animation = 'toast-slide-out 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards'; setTimeout(() => this.parentElement.remove(), 350)">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div class="alert-content">{{ session('error') }}</div>
                        <button class="alert-close"
                            onclick="this.parentElement.style.animation = 'toast-slide-out 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards'; setTimeout(() => this.parentElement.remove(), 350)">&times;</button>
                    </div>
                @endif
                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <div class="alert-content">{{ $error }}</div>
                            <button class="alert-close"
                                onclick="this.parentElement.style.animation = 'toast-slide-out 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards'; setTimeout(() => this.parentElement.remove(), 350)">&times;</button>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Page Content -->
            @yield('content')

            <!-- Dashboard Footer -->
            <footer class="dashboard-footer">
                <div>&copy; {{ date('Y') }} ECYES TPA SYSTEM. All rights reserved.</div>
                <div class="developer-credit">
                    <span>Designed & Developed by</span>
                    <a href="https://cyberduce.com" target="_blank" style="display: flex; align-items: center;">
                        <img src="/images/cyberduce-white.png" class="logo-dark" alt="Cyberduce">
                        <img src="/images/cyberduce.png" class="logo-light" alt="Cyberduce">
                    </a>
                </div>
            </footer>

        </main>
    </div>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Theme and Notification Drawer Interactions JS -->
    <script>
        // Theme toggle mechanism
        const themeBtn = document.getElementById('theme-toggle');
        const themeIcon = themeBtn.querySelector('i');

        // Update icon based on initial theme state
        function updateThemeIcon() {
            if (document.body.classList.contains('dark-theme')) {
                themeIcon.className = 'fa-solid fa-sun';
            } else {
                themeIcon.className = 'fa-solid fa-moon';
            }
        }
        updateThemeIcon();

        themeBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme');
            updateThemeIcon();

            // Send request to persist in session
            fetch('/persist-theme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ theme: document.body.classList.contains('dark-theme') ? 'dark-theme' : '' })
            }).catch(e => console.log('Theme persistence error: ', e));
        });

        // Notifications panel toggle
        const bellBtn = document.getElementById('notification-bell');
        const panel = document.getElementById('notifications-panel');

        bellBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', () => {
            panel.style.display = 'none';
        });

        panel.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // AJAX: mark notification as read
        function markAsRead(element, id) {
            if (element.classList.contains('unread')) {
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            element.classList.remove('unread');
                            // Decrement bell count if exists
                            const countSpan = bellBtn.querySelector('span');
                            if (countSpan) {
                                let count = parseInt(countSpan.innerText) - 1;
                                if (count <= 0) {
                                    countSpan.remove();
                                } else {
                                    countSpan.innerText = count;
                                }
                            }
                        }
                    })
                    .catch(e => console.log('Error marking read: ', e));
            }
        }

        // Custom Dialog Modal Logic
        function showCustomDialog({ title, message, type = 'info', showCancel = false, confirmText = 'OK', cancelText = 'Cancel', callback }) {
            // Remove any existing dialog first
            const existing = document.querySelector('.custom-dialog-overlay');
            if (existing) existing.remove();

            // Create container elements
            const overlay = document.createElement('div');
            overlay.className = 'custom-dialog-overlay';

            // Icon mapping
            let iconHtml = '';
            if (type === 'success') iconHtml = '<i class="fa-solid fa-circle-check"></i>';
            else if (type === 'danger') iconHtml = '<i class="fa-solid fa-triangle-exclamation"></i>';
            else if (type === 'warning' || type === 'confirm') iconHtml = '<i class="fa-solid fa-circle-exclamation"></i>';
            else iconHtml = '<i class="fa-solid fa-circle-info"></i>';

            overlay.innerHTML = `
                <div class="custom-dialog-modal">
                    <div class="custom-dialog-icon ${type}">
                        ${iconHtml}
                    </div>
                    <h3 class="custom-dialog-title">${title || 'Notice'}</h3>
                    <div class="custom-dialog-message">${message}</div>
                    <div class="custom-dialog-buttons">
                        ${showCancel ? `<button class="btn btn-secondary custom-dialog-btn-cancel">${cancelText}</button>` : ''}
                        <button class="btn ${type === 'danger' ? 'btn-danger' : (type === 'success' ? 'btn-accent' : 'btn-primary')} custom-dialog-btn-confirm">${confirmText}</button>
                    </div>
                </div>
            `;

            document.body.appendChild(overlay);

            // Trigger animation
            setTimeout(() => overlay.classList.add('active'), 10);

            const confirmBtn = overlay.querySelector('.custom-dialog-btn-confirm');
            const cancelBtn = overlay.querySelector('.custom-dialog-btn-cancel');

            // Set focus to primary action
            confirmBtn.focus();

            function closeDialog(result) {
                overlay.classList.remove('active');
                // Clean up event listeners
                document.removeEventListener('keydown', handleKeyDown);
                setTimeout(() => {
                    overlay.remove();
                    if (callback) callback(result);
                }, 300);
            }

            confirmBtn.addEventListener('click', () => closeDialog(true));
            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => closeDialog(false));
            }

            // Close on backdrop click (cancel behavior)
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    closeDialog(false);
                }
            });

            // Keyboard accessibility
            function handleKeyDown(e) {
                if (e.key === 'Escape') {
                    e.preventDefault();
                    closeDialog(false);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    confirmBtn.click();
                }
            }
            document.addEventListener('keydown', handleKeyDown);
        }

        // Global Overrides
        window.alert = function (message) {
            showCustomDialog({
                title: 'Alert',
                message: message,
                type: 'info',
                showCancel: false,
                confirmText: 'Close'
            });
        };

        // Intercept inline confirm() in forms and clicks
        document.addEventListener('submit', function (e) {
            if (e.target.dataset.confirmed === 'true') {
                delete e.target.dataset.confirmed; // clear flag for next time
                return;
            }

            const onSubmitAttr = e.target.getAttribute('onsubmit');
            if (onSubmitAttr && onSubmitAttr.includes('confirm(')) {
                e.preventDefault();
                e.stopImmediatePropagation();

                // Extract message from confirm(...)
                let message = 'Are you sure you want to proceed?';
                const match = onSubmitAttr.match(/confirm\(['"]([^'"]+)['"]\)/);
                if (match && match[1]) {
                    message = match[1];
                }

                // Determine type of confirm (if delete is in message, use danger style)
                const isDelete = message.toLowerCase().includes('delete') || message.toLowerCase().includes('remove');

                showCustomDialog({
                    title: isDelete ? 'Confirm Action' : 'Are you sure?',
                    message: message,
                    type: isDelete ? 'danger' : 'confirm',
                    showCancel: true,
                    confirmText: isDelete ? 'Delete' : 'Confirm',
                    cancelText: 'Cancel',
                    callback: function (confirmed) {
                        if (confirmed) {
                            e.target.dataset.confirmed = 'true';
                            e.target.submit();
                        }
                    }
                });
            }
        }, true);

        document.addEventListener('click', function (e) {
            const target = e.target.closest('a, button');
            if (!target) return;

            if (target.dataset.confirmed === 'true') {
                delete target.dataset.confirmed; // clear flag
                return;
            }

            const onclickAttr = target.getAttribute('onclick');
            if (onclickAttr && onclickAttr.includes('confirm(')) {
                e.preventDefault();
                e.stopImmediatePropagation();

                let message = 'Are you sure you want to proceed?';
                const match = onclickAttr.match(/confirm\(['"]([^'"]+)['"]\)/);
                if (match && match[1]) {
                    message = match[1];
                }

                const isDelete = message.toLowerCase().includes('delete') || message.toLowerCase().includes('remove');

                showCustomDialog({
                    title: isDelete ? 'Confirm Action' : 'Are you sure?',
                    message: message,
                    type: isDelete ? 'danger' : 'confirm',
                    showCancel: true,
                    confirmText: isDelete ? 'Delete' : 'Confirm',
                    cancelText: 'Cancel',
                    callback: function (confirmed) {
                        if (confirmed) {
                            // Safe re-triggering of click event:
                            const originalOnclick = target.onclick;
                            const originalOnclickAttr = target.getAttribute('onclick');
                            target.onclick = null;
                            target.removeAttribute('onclick');

                            target.click();

                            // Restore after event finishes
                            setTimeout(() => {
                                if (originalOnclick) target.onclick = originalOnclick;
                                if (originalOnclickAttr) target.setAttribute('onclick', originalOnclickAttr);
                            }, 0);
                        }
                    }
                });
            }
        }, true);

        // Auto-dismiss Toast Notifications
        document.querySelectorAll('.toast-container .alert').forEach(alert => {
            setTimeout(() => {
                alert.style.animation = 'toast-slide-out 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards';
                setTimeout(() => alert.remove(), 350);
            }, 5000);
        });

        // Sidebar Toggle Handling (Mobile & Desktop)
        const sidebarToggleBtn = document.getElementById('sidebar-toggle');
        const sidebarEl = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        if (sidebarToggleBtn && sidebarEl) {
            sidebarToggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (window.innerWidth <= 640) {
                    sidebarEl.classList.toggle('open');
                } else {
                    sidebarEl.classList.toggle('collapsed');
                    if (mainContent) mainContent.classList.toggle('expanded');
                }
            });
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 640 && sidebarEl.classList.contains('open') && !sidebarEl.contains(e.target) && e.target !== sidebarToggleBtn) {
                    sidebarEl.classList.remove('open');
                }
            });
        }
    </script>
    @yield('scripts')
</body>

</html>