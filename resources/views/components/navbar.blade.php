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

                @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                    <a href="{{ route('invitations.index') }}" class="nav-link {{ request()->routeIs('invitations.*') ? 'active' : '' }}">
                        📋 Manage Students
                    </a>
                @endif

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
                            <div class="notification-header-actions">
                                @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.markAllRead') }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="notification-mark-read">Mark all as read</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="notification-dropdown-list">
                            @if($recentNotifications->isEmpty())
                                <div class="notification-empty">No notifications yet</div>
                            @else
                                @foreach($recentNotifications as $notification)
                                    <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}" class="notification-item-form">
                                        @csrf
                                        <button type="submit" class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                            <span class="notification-message">{{ $notification->data['message'] ?? 'New notification' }}</span>
                                            <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                        </button>
                                    </form>
                                @endforeach
                            @endif
                        </div>
                        @if($recentNotifications->isNotEmpty())
                            <div class="notification-dropdown-footer">
                                <a href="{{ route('notifications.index') }}" class="notification-view-all">View all notifications</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="nav-user">
                    <span class="nav-username">{{ '@' . auth()->user()->username }}</span>
                    <span class="nav-role-badge nav-role-{{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>
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

    // Real-time notifications polling
    @auth
    (function() {
        let lastUnreadCount = {{ $unreadCount }};
        let pollInterval = 1000; // Poll every 1 second (instant)
        let pollTimer = null;
        let isFetching = false;

        function fetchNotifications() {
            if (isFetching) return;
            isFetching = true;

            fetch('{{ route("notifications.fetch") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    // Update badge
                    let bell = document.getElementById('notificationBell');
                    if (bell) {
                        let badge = document.getElementById('notificationBadge');
                        if (data.unreadCount > 0) {
                            let displayCount = data.unreadCount > 99 ? '99+' : data.unreadCount;
                            if (!badge) {
                                badge = document.createElement('span');
                                badge.className = 'notification-badge';
                                badge.id = 'notificationBadge';
                                bell.appendChild(badge);
                            }
                            badge.textContent = displayCount;

                            // Pulse animation when new notifications arrive
                            if (data.unreadCount > lastUnreadCount) {
                                bell.classList.add('notification-bell-pulse');
                                setTimeout(() => bell.classList.remove('notification-bell-pulse'), 1000);
                            }
                        } else if (badge) {
                            badge.remove();
                        }
                    }

                    lastUnreadCount = data.unreadCount;
                    
                    // Update dropdown list
                    let list = document.querySelector('.notification-dropdown-list');
                    if (list) {
                        if (data.notifications.length === 0) {
                            list.innerHTML = '<div class="notification-empty">No notifications yet</div>';
                        } else {
                            let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                            let csrf = csrfTokenMeta ? csrfTokenMeta.content : '';
                            list.innerHTML = '';
                            data.notifications.forEach(notif => {
                                let form = document.createElement('form');
                                form.method = 'POST';
                                form.action = notif.mark_as_read_url;
                                form.className = 'notification-item-form';
                                
                                let csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = csrf;
                                
                                let button = document.createElement('button');
                                button.type = 'submit';
                                button.className = 'notification-item ' + (notif.is_read ? '' : 'unread');
                                
                                let msg = document.createElement('span');
                                msg.className = 'notification-message';
                                msg.textContent = notif.message;
                                
                                let time = document.createElement('span');
                                time.className = 'notification-time';
                                time.textContent = notif.time;
                                
                                button.appendChild(msg);
                                button.appendChild(time);
                                form.appendChild(csrfInput);
                                form.appendChild(button);
                                
                                list.appendChild(form);
                            });
                        }
                    }
                    
                    // Update header actions (Mark all as read)
                    let headerActions = document.querySelector('.notification-header-actions');
                    if (headerActions) {
                        if (data.unreadCount > 0) {
                            if (headerActions.innerHTML.trim() === '') {
                                let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                                let csrf = csrfTokenMeta ? csrfTokenMeta.content : '';
                                headerActions.innerHTML = `
                                    <form method="POST" action="{{ route('notifications.markAllRead') }}" class="inline-form">
                                        <input type="hidden" name="_token" value="${csrf}">
                                        <button type="submit" class="notification-mark-read">Mark all as read</button>
                                    </form>
                                `;
                            }
                        } else {
                            headerActions.innerHTML = '';
                        }
                    }
                    
                    // Update footer (View all)
                    let footer = document.querySelector('.notification-dropdown-footer');
                    let dropdown = document.getElementById('notificationDropdown');
                    if (data.notifications.length > 0) {
                        if (!footer && dropdown) {
                            footer = document.createElement('div');
                            footer.className = 'notification-dropdown-footer';
                            footer.innerHTML = '<a href="{{ route("notifications.index") }}" class="notification-view-all">View all notifications</a>';
                            dropdown.appendChild(footer);
                        }
                    } else if (footer) {
                        footer.remove();
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error))
                .finally(() => { isFetching = false; });
        }

        // Fetch immediately on page load
        fetchNotifications();

        // Start WebSockets listener
        if (window.Echo) {
            window.Echo.private('App.Models.User.{{ auth()->id() }}')
                .notification((notification) => {
                    fetchNotifications();
                });
        } else {
            console.warn('Laravel Echo is not defined. Falling back to polling.');
            setInterval(fetchNotifications, 5000);
        }

        // Fetch immediately when visible (covers alt-tab or returning to tab)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                fetchNotifications();
            }
        });
        
        window.addEventListener('focus', function() {
            fetchNotifications();
        });
    })();
    @endauth
</script>
