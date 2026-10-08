<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="BM3 Review - Rate and review teachers at SMK Bina Mandiri Multimedia">
    <title>{{ $title ?? 'BM3 Review' }} - Rate Your Teachers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('bm3_theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Navigation --}}
    <x-navbar />

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flash-message flash-success" id="flash-message">
            <div class="container">
                <span>✓ {{ session('success') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="flash-close" aria-label="Close">&times;</button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="flash-message flash-error" id="flash-message">
            <div class="container">
                <span>✕ {{ session('error') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="flash-close" aria-label="Close">&times;</button>
            </div>
        </div>
    @endif
    @if(session('info'))
        <div class="flash-message flash-info" id="flash-message">
            <div class="container">
                <span>ℹ {{ session('info') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="flash-close" aria-label="Close">&times;</button>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="site-footer">
        <div class="footer-top-border"></div>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand-col">
                    <div class="footer-logo">
                        <span class="brand-badge">bm3</span>
                        <span class="brand-text">Teacher<span>Review</span></span>
                    </div>
                    <p class="footer-description">
                        Meningkatkan kualitas pendidikan di SMK Bina Mandiri Multimedia melalui feedback yang transparan dan membangun.
                    </p>
                    <div class="footer-socials">
                        <a href="#" class="social-icon" aria-label="Instagram">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="social-icon" aria-label="Twitter">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="#" class="social-icon" aria-label="YouTube">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.377.55a3.016 3.016 0 0 0-2.122 2.136C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.55 9.376.55 9.376.55s7.505 0 9.377-.55a3.016 3.016 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    </div>
                </div>

                <div class="footer-links-col">
                    <h4 class="footer-heading">Platform</h4>
                    <ul class="footer-list">
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                        <li><a href="{{ route('teachers.index') }}">Cari Guru</a></li>
                        <li><a href="#">Cara Kerja</a></li>
                        <li><a href="#">Panduan Siswa</a></li>
                    </ul>
                </div>

                <div class="footer-links-col">
                    <h4 class="footer-heading">Akun</h4>
                    <ul class="footer-list">
                        @auth
                            <li><a href="{{ route('profile.show') }}">Profil Saya</a></li>
                            @if(auth()->user()->isAdmin())
                                <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                                    @csrf
                                    <button type="submit" class="footer-btn-link">Logout</button>
                                </form>
                            </li>
                        @else
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Daftar</a></li>
                        @endauth
                    </ul>
                </div>

                <div class="footer-links-col">
                    <h4 class="footer-heading">Bantuan</h4>
                    <ul class="footer-list">
                        <li><a href="#">Syarat & Ketentuan</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Laporkan Bug</a></li>
                        <li><a href="#">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-copyright">
                    &copy; {{ date('Y') }} BM3 Teacher Review. Built for SMK Bina Mandiri Multimedia.
                </div>
                <div class="footer-status">
                    <span class="status-dot"></span> All systems operational
                </div>
            </div>
        </div>
    </footer>

    {{-- Report Modal --}}
    @auth
    <div class="modal-overlay" id="reportModal" style="display:none;">
        <div class="modal">
            <div class="modal-header">
                <h3>Report Review</h3>
                <button class="modal-close" onclick="closeReportModal()" aria-label="Close">&times;</button>
            </div>
            <form id="reportForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Why are you reporting this review?</p>
                    <div class="report-options">
                        <label class="report-option">
                            <input type="radio" name="reason" value="spam" required>
                            <span>Spam</span>
                        </label>
                        <label class="report-option">
                            <input type="radio" name="reason" value="harassment">
                            <span>Harassment</span>
                        </label>
                        <label class="report-option">
                            <input type="radio" name="reason" value="offensive">
                            <span>Offensive Content</span>
                        </label>
                        <label class="report-option">
                            <input type="radio" name="reason" value="personal_info">
                            <span>Personal Information</span>
                        </label>
                        <label class="report-option">
                            <input type="radio" name="reason" value="fake">
                            <span>Fake Review</span>
                        </label>
                        <label class="report-option">
                            <input type="radio" name="reason" value="other">
                            <span>Other</span>
                        </label>
                    </div>
                    <textarea name="details" placeholder="Additional details (optional)..." class="form-textarea" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeReportModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
    @endauth

    <script>
        // Auto-hide flash messages
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 300);
            }
        }, 4000);

        // Report modal
        function openReportModal(reviewId) {
            const modal = document.getElementById('reportModal');
            const form = document.getElementById('reportForm');
            form.action = '/reviews/' + reviewId + '/report';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeReportModal() {
            const modal = document.getElementById('reportModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
        // Close on backdrop click
        document.getElementById('reportModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeReportModal();
        });

        // CSRF Token Auto-Refresh — prevents 419 errors when page is left open for hours
        (function() {
            const REFRESH_INTERVAL = 30 * 60 * 1000; // 30 minutes

            function refreshCsrfToken() {
                fetch('/', { method: 'GET', credentials: 'same-origin' })
                    .then(response => response.text())
                    .then(html => {
                        // Extract the new CSRF token from the response
                        const match = html.match(/<meta name="csrf-token" content="([^"]+)"/);
                        if (match && match[1]) {
                            const newToken = match[1];

                            // Update the meta tag
                            const metaTag = document.querySelector('meta[name="csrf-token"]');
                            if (metaTag) {
                                metaTag.setAttribute('content', newToken);
                            }

                            // Update all hidden CSRF inputs in forms
                            document.querySelectorAll('input[name="_token"]').forEach(input => {
                                input.value = newToken;
                            });
                        }
                    })
                    .catch(err => console.error('CSRF refresh failed:', err));
            }

            setInterval(refreshCsrfToken, REFRESH_INTERVAL);

            // Also refresh when the user comes back to the tab after being away
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    refreshCsrfToken();
                }
            });
        })();
    </script>
</body>
</html>

