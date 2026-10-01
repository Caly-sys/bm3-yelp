<x-layout title="Manage Invitations">
    <section class="section">
        <div class="container">
            <div class="notifications-page-header">
                <div>
                    <h1 class="page-title">📋 Manage Invitations</h1>
                    <p class="page-subtitle">
                        @if(auth()->user()->isAdmin())
                            Invite new teachers and students to BM3 Review
                        @else
                            Invite new students to BM3 Review
                        @endif
                    </p>
                </div>
                <div class="notifications-page-actions">
                    <a href="{{ route('invitations.create') }}" class="btn btn-primary btn-sm">➕ New Invitation</a>
                </div>
            </div>

            @if($invitations->isEmpty())
                <div class="empty-state card">
                    <span class="empty-icon">📩</span>
                    <h3>No invitations yet</h3>
                    <p>Start inviting {{ auth()->user()->isAdmin() ? 'teachers and students' : 'students' }} to join BM3 Review.</p>
                    <a href="{{ route('invitations.create') }}" class="btn btn-primary btn-sm mt-2">Create Invitation</a>
                </div>
            @else
                <div class="admin-table-wrapper card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Invited User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Sent</th>
                                <th>Expires</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invitations as $invitation)
                                <tr>
                                    <td>
                                        <strong>{{ $invitation->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-sm">{{ $invitation->email }}</span>
                                    </td>
                                    <td>
                                        @if($invitation->role === 'teacher')
                                            <span class="badge badge-admin">🎓 Teacher</span>
                                        @else
                                            <span class="badge">📚 Student</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invitation->status === 'pending' && $invitation->expires_at->isFuture())
                                            <span class="badge badge-warning">⏳ Pending</span>
                                        @elseif($invitation->status === 'accepted')
                                            <span class="badge badge-success">✅ Accepted</span>
                                        @elseif($invitation->status === 'expired' || ($invitation->status === 'pending' && $invitation->expires_at->isPast()))
                                            <span class="badge badge-danger">⏰ Expired</span>
                                        @elseif($invitation->status === 'cancelled')
                                            <span class="badge badge-danger">❌ Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted text-xs">{{ $invitation->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted text-xs">{{ $invitation->expires_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="admin-actions text-right">
                                        @if($invitation->status === 'pending' && $invitation->expires_at->isFuture())
                                            <button type="button" class="btn btn-ghost btn-xs" onclick="copyInvitationLink('{{ route('invitations.accept', $invitation->token) }}')">
                                                📋 Copy Link
                                            </button>
                                            <form method="POST" action="{{ route('invitations.cancel', $invitation) }}" class="inline-form"
                                                onsubmit="return confirm('Cancel this invitation?')">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost btn-xs btn-danger-text">Cancel</button>
                                            </form>
                                        @elseif($invitation->status === 'accepted')
                                            <span class="text-muted text-xs">By @{{ $invitation->acceptedUser->username ?? 'unknown' }}</span>
                                        @else
                                            <span class="text-muted text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
                    {{ $invitations->links() }}
                </div>
            @endif
        </div>
    </section>

    <script>
    function copyInvitationLink(link) {
        navigator.clipboard.writeText(link).then(function() {
            alert('Invitation link copied to clipboard!');
        }).catch(function() {
            prompt('Copy this invitation link:', link);
        });
    }
    </script>
</x-layout>
