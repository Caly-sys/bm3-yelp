<x-layout title="Notifications">
    <section class="section">
        <div class="container container-md">
            <div class="notifications-page-header">
                <h1 class="page-title">🔔 Notifications</h1>
                <div class="notifications-page-actions">
                    @if($notifications->where('read_at', null)->count() > 0)
                        <form method="POST" action="{{ route('notifications.markAllRead') }}" class="inline-form">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-sm">Mark all as read</button>
                        </form>
                    @endif
                    @if($notifications->isNotEmpty())
                        <form method="POST" action="{{ route('notifications.destroyAll') }}" class="inline-form"
                            onsubmit="return confirm('Are you sure you want to delete all notifications?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm btn-danger-text">Clear all</button>
                        </form>
                    @endif
                </div>
            </div>

            @if($notifications->isEmpty())
                <div class="empty-state card">
                    <span class="empty-icon">🔔</span>
                    <h3>No notifications</h3>
                    <p>You're all caught up! New notifications will appear here.</p>
                </div>
            @else
                <div class="notifications-list">
                    @foreach($notifications as $notification)
                        <div class="notification-card card {{ $notification->read_at ? '' : 'notification-card-unread' }}">
                            <div class="notification-card-content">
                                <span class="notification-card-message">{{ $notification->data['message'] ?? 'Notification' }}</span>
                                <span class="notification-card-time">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="notification-card-actions">
                                @if(!$notification->read_at)
                                    <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-xs">Mark as read</button>
                                    </form>
                                @else
                                    @if(isset($notification->data['link']))
                                        <a href="{{ $notification->data['link'] }}" class="btn btn-ghost btn-xs">View</a>
                                    @endif
                                @endif
                                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs btn-danger-text">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination-wrapper">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layout>
