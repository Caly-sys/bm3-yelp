<x-layout title="Manage Users">
    <section class="section page-header-section">
        <div class="container">
            <a href="{{ route('admin.dashboard') }}" class="back-link">← Admin Dashboard</a>
            <h1 class="page-title">Manage Users</h1>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Reviews</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td><strong>{{ '@' . $user->username }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge {{ $user->isAdmin() ? 'badge-admin' : '' }}">{{ ucfirst($user->role) }}</span></td>
                                <td>{{ $user->reviews_count }}</td>
                                <td>
                                    @if($user->is_suspended)
                                        <span class="badge badge-danger">Suspended</span>
                                    @else
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    @unless($user->isAdmin())
                                        <form method="POST" action="{{ route('admin.users.toggle-suspend', $user) }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-ghost btn-sm {{ $user->is_suspended ? '' : 'btn-danger-text' }}">
                                                {{ $user->is_suspended ? 'Unsuspend' : 'Suspend' }}
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrapper">{{ $users->links() }}</div>
        </div>
    </section>
</x-layout>
