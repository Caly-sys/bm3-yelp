@props(['class' => ''])

<nav class="navbar {{ $class }}" id="mainNav">
    <div class="container navbar-inner">
        <a href="{{ route('home') }}" class="navbar-brand">
            <span class="brand-badge">bm3</span>
            <span class="brand-text">Teacher<span>Review</span></span>
        </a>

        <div class="navbar-links" id="navLinks">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">Teachers</a>

            @auth
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">My Profile</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="nav-link nav-admin {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a>
                @endif

                {{-- Notification Bell --}}
                @php
                    $unreadNotifications = auth()->user()->unreadNotifications;
                    $unreadCount = $unreadNotifications->count();
                    $recentNotifications = auth()->user()->notifications()->latest()->take(10)->get();
                @endphp
                <div class="notification-wrapper" id="notificationWrapper">
                    <button type="button" class="notification-bell" id="notificationBell" aria-label="Notifications" title="Notifications">
                        🔔
                        @if($unreadCount > 0)
                            <span class="notification-badge" id="notificationBadge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-dropdown-header">
                            <span class="notification-dropdown-title">Notifications</span>
                            @if($unreadCount > 0)
                                <form method="POST" action="{{ route('notifications.markAllRead') }}" class="inline-form">
                                    @csrf
                                    <button type="submit" class="notification-mark-read">Mark all as read</button>
                                </form>
                            @endif
                        </div>
                        <div class="notification-dropdown-list">
                            @if($recentNotifications->isEmpty())
                                <div class="notification-empty">No notifications yet</div>
                            @else
                                @foreach($recentNotifications as $notification)
                                    <a href="{{ $notification->data['link'] ?? '#' }}" class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                        <span class="notification-message">{{ $notification->data['message'] ?? 'New notification' }}</span>
                                        <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="nav-user">
                    <span class="nav-username">{{ '@' . auth()->user()->username }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">Logout</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up</a>
            @endauth

            <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode" title="Toggle theme">
                <span class="theme-icon-light" aria-hidden="true">☀️</span>
                <span class="theme-icon-dark" aria-hidden="true">🌙</span>
            </button>
        </div>

        <button class="navbar-toggle" id="navToggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>

<script>
    document.getElementById('navToggle')?.addEventListener('click', function() {
        document.getElementById('navLinks').classList.toggle('open');
        this.classList.toggle('active');
    });

    // Notification dropdown toggle
    document.getElementById('notificationBell')?.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('notificationDropdown').classList.toggle('open');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('notificationWrapper');
        const dropdown = document.getElementById('notificationDropdown');
        if (wrapper && dropdown && !wrapper.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });
</script>
