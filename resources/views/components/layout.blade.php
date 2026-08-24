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
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>BM3 Review</h3>
                    <p>Platform review guru SMK Bina Mandiri Multimedia</p>
                </div>
                <div class="footer-links">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('teachers.index') }}">Teachers</a>
                    @auth
                        <a href="{{ route('profile.show') }}">My Profile</a>
                    @endauth
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} BM3 Review. Built for SMK Bina Mandiri Multimedia.</p>
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
    </script>
</body>
</html>
