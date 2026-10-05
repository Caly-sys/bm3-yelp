<x-layout title="Notifications">
    <section class="section">
        <div class="container container-md">
            <div class="notifications-page-header">
                <h1 class="page-title">🔔 Notifications</h1>
                <div class="notifications-page-actions" id="pageActions">
                    <button type="button" class="btn btn-ghost btn-sm" id="markAllReadBtn" style="display:none;" onclick="markAllReadAjax()">Mark all as read</button>
                    <button type="button" class="btn btn-ghost btn-sm btn-danger-text" id="clearAllBtn" style="display:none;" onclick="clearAllAjax()">Clear all</button>
                </div>
            </div>

            <div id="notificationsContainer">
                {{-- Initial server-rendered content --}}
                @if($notifications->isEmpty())
                    <div class="empty-state card" id="emptyState">
                        <span class="empty-icon">🔔</span>
                        <h3>No notifications</h3>
                        <p>You're all caught up! New notifications will appear here.</p>
                    </div>
                @else
                    <div class="notifications-list" id="notificationsList">
                        @foreach($notifications as $notification)
                            <div class="notification-card card {{ $notification->read_at ? '' : 'notification-card-unread' }}" data-id="{{ $notification->id }}">
                                <div class="notification-card-content">
                                    <span class="notification-card-message">{{ $notification->data['message'] ?? 'Notification' }}</span>
                                    <span class="notification-card-time">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="notification-card-actions">
                                    @if(!$notification->read_at)
                                        <button type="button" class="btn btn-ghost btn-xs" onclick="markAsReadAjax('{{ $notification->id }}', this)">Mark as read</button>
                                    @else
                                        @if(isset($notification->data['link']))
                                            <a href="{{ $notification->data['link'] }}" class="btn btn-ghost btn-xs">View</a>
                                        @endif
                                    @endif
                                    <button type="button" class="btn btn-ghost btn-xs btn-danger-text" onclick="deleteNotifAjax('{{ $notification->id }}', this)">Delete</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pagination-wrapper">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const fetchUrl = '{{ route("notifications.fetch") }}';
        const markAllUrl = '{{ route("notifications.markAllRead") }}';
        const clearAllUrl = '{{ route("notifications.destroyAll") }}';
        let knownIds = new Set();
        let isFetching = false;

        // Track initially rendered notification IDs
        document.querySelectorAll('.notification-card[data-id]').forEach(el => {
            knownIds.add(el.getAttribute('data-id'));
        });

        // Show/hide header action buttons based on content
        function updatePageActions(notifications) {
            const markAllBtn = document.getElementById('markAllReadBtn');
            const clearAllBtn = document.getElementById('clearAllBtn');
            const hasUnread = notifications.some(n => !n.is_read);
            const hasAny = notifications.length > 0;

            markAllBtn.style.display = hasUnread ? '' : 'none';
            clearAllBtn.style.display = hasAny ? '' : 'none';
        }

        function buildNotificationCard(notif) {
            const card = document.createElement('div');
            card.className = 'notification-card card' + (notif.is_read ? '' : ' notification-card-unread');
            card.setAttribute('data-id', notif.id);
            card.style.animation = 'fadeSlideIn 0.3s ease-out';

            let actionsHtml = '';
            if (!notif.is_read) {
                actionsHtml += `<button type="button" class="btn btn-ghost btn-xs" onclick="markAsReadAjax('${notif.id}', this)">Mark as read</button>`;
            } else if (notif.link) {
                actionsHtml += `<a href="${notif.link}" class="btn btn-ghost btn-xs">View</a>`;
            }
            actionsHtml += `<button type="button" class="btn btn-ghost btn-xs btn-danger-text" onclick="deleteNotifAjax('${notif.id}', this)">Delete</button>`;

            card.innerHTML = `
                <div class="notification-card-content">
                    <span class="notification-card-message">${escapeHtml(notif.message)}</span>
                    <span class="notification-card-time">${escapeHtml(notif.time)}</span>
                </div>
                <div class="notification-card-actions">${actionsHtml}</div>
            `;
            return card;
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function fetchAndUpdate() {
            if (isFetching) return;
            isFetching = true;

            fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    const container = document.getElementById('notificationsContainer');
                    let list = document.getElementById('notificationsList');

                    updatePageActions(data.notifications);

                    if (data.notifications.length === 0) {
                        container.innerHTML = `
                            <div class="empty-state card" id="emptyState">
                                <span class="empty-icon">🔔</span>
                                <h3>No notifications</h3>
                                <p>You're all caught up! New notifications will appear here.</p>
                            </div>
                        `;
                        knownIds.clear();
                        return;
                    }

                    // Ensure list container exists
                    if (!list) {
                        container.innerHTML = '<div class="notifications-list" id="notificationsList"></div>';
                        list = document.getElementById('notificationsList');
                    }

                    const newIds = new Set(data.notifications.map(n => n.id));

                    // Remove cards that no longer exist on server
                    list.querySelectorAll('.notification-card[data-id]').forEach(card => {
                        if (!newIds.has(card.getAttribute('data-id'))) {
                            card.style.animation = 'fadeSlideOut 0.25s ease-in forwards';
                            setTimeout(() => card.remove(), 250);
                        }
                    });

                    // Update existing cards and prepend new ones
                    data.notifications.forEach((notif, index) => {
                        const existing = list.querySelector(`.notification-card[data-id="${notif.id}"]`);
                        if (existing) {
                            // Update read state and time
                            existing.className = 'notification-card card' + (notif.is_read ? '' : ' notification-card-unread');
                            const timeEl = existing.querySelector('.notification-card-time');
                            if (timeEl) timeEl.textContent = notif.time;
                        } else {
                            // New notification — insert at correct position
                            const card = buildNotificationCard(notif);
                            const children = list.children;
                            if (index < children.length) {
                                list.insertBefore(card, children[index]);
                            } else {
                                list.appendChild(card);
                            }
                            knownIds.add(notif.id);
                        }
                    });
                })
                .catch(err => console.error('Error fetching notifications:', err))
                .finally(() => { isFetching = false; });
        }

        // Mark single as read via AJAX
        function markAsReadAjax(id, btn) {
            btn.disabled = true;
            btn.textContent = '...';
            fetch('{{ url("notifications") }}/' + id + '/read', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(() => {
                const card = btn.closest('.notification-card');
                if (card) {
                    card.classList.remove('notification-card-unread');
                    const actions = card.querySelector('.notification-card-actions');
                    // Replace "Mark as read" with nothing (or View link on next poll)
                    btn.remove();
                }
                fetchAndUpdate(); // Refresh data
            }).catch(() => { btn.disabled = false; btn.textContent = 'Mark as read'; });
        }

        // Delete single via AJAX
        function deleteNotifAjax(id, btn) {
            btn.disabled = true;
            btn.textContent = '...';
            fetch('{{ url("notifications") }}/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(() => {
                const card = btn.closest('.notification-card');
                if (card) {
                    card.style.animation = 'fadeSlideOut 0.25s ease-in forwards';
                    setTimeout(() => { card.remove(); fetchAndUpdate(); }, 250);
                }
            }).catch(() => { btn.disabled = false; btn.textContent = 'Delete'; });
        }

        // Mark all as read via AJAX
        function markAllReadAjax() {
            const btn = document.getElementById('markAllReadBtn');
            btn.disabled = true;
            btn.textContent = '...';
            fetch(markAllUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(() => {
                document.querySelectorAll('.notification-card-unread').forEach(c => c.classList.remove('notification-card-unread'));
                btn.style.display = 'none';
                btn.disabled = false;
                btn.textContent = 'Mark all as read';
                fetchAndUpdate();
            }).catch(() => { btn.disabled = false; btn.textContent = 'Mark all as read'; });
        }

        // Clear all via AJAX
        function clearAllAjax() {
            if (!confirm('Are you sure you want to delete all notifications?')) return;
            const btn = document.getElementById('clearAllBtn');
            btn.disabled = true;
            btn.textContent = '...';
            fetch(clearAllUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(() => {
                fetchAndUpdate();
                btn.disabled = false;
                btn.textContent = 'Clear all';
            }).catch(() => { btn.disabled = false; btn.textContent = 'Clear all'; });
        }

        // Initialize header action buttons
        (function() {
            const hasUnread = document.querySelector('.notification-card-unread');
            const hasAny = document.querySelector('.notification-card');
            document.getElementById('markAllReadBtn').style.display = hasUnread ? '' : 'none';
            document.getElementById('clearAllBtn').style.display = hasAny ? '' : 'none';
        })();

        // Listen for new notifications via Echo
        if (window.Echo) {
            window.Echo.private('App.Models.User.{{ auth()->id() }}')
                .notification((notification) => {
                    fetchAndUpdate();
                });
        } else {
            console.warn('Laravel Echo is not defined. Falling back to polling.');
            setInterval(fetchAndUpdate, 5000);
        }

        // Fetch on tab focus
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) fetchAndUpdate();
        });
    </script>
</x-layout>
