<x-layout title="Manage Users">
    <x-admin-nav title="User Management" subtitle="View all registered student and admin accounts, manage roles and access">
    </x-admin-nav>

    <section class="section admin-section">
        <div class="container">
            {{-- Filter Bar --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="admin-filter-bar card mb-4">
                <div class="admin-filter-search">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by username, name, or email..." class="form-input" aria-label="Search users">
                </div>

                <div class="admin-filter-controls">
                    <select name="role" class="form-select" aria-label="Filter by role">
                        <option value="">All Roles</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Students</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admins</option>
                    </select>

                    <select name="status" class="form-select" aria-label="Filter by status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended Only</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if($users->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">👥</span>
                    <h3>No users found</h3>
                    <p>Try adjusting your search criteria or filters.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">Clear Filters</a>
                </div>
            @else
                <div class="admin-table-wrapper card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Reviews</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="{{ $user->is_suspended ? 'row-suspended' : '' }}">
                                    <td>
                                        <div class="admin-cell-user">
                                            <div class="admin-avatar-sm" style="background-color: {{ $user->isAdmin() ? '#8B5CF6' : '#0096FA' }}">
                                                @if($user->avatar)
                                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->username }}">
                                                @else
                                                    <span>{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div class="admin-cell-details">
                                                <strong class="admin-item-title">{{ $user->name }}</strong>
                                                <span class="admin-item-subtitle">{{ '@' . $user->username }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-sm font-medium">{{ $user->email }}</span>
                                    </td>
                                    <td>
                                        @if($user->isAdmin())
                                            <span class="badge badge-admin">⚡ Admin</span>
                                        @else
                                            <span class="badge">Student</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge">{{ $user->reviews_count }}</span>
                                    </td>
                                    <td>
                                        @if($user->is_suspended)
                                            <span class="badge badge-danger">🚫 Suspended</span>
                                        @else
                                            <span class="badge badge-success">✓ Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted text-xs">{{ $user->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="admin-actions text-right">
                                        @if($user->isAdmin())
                                            <span class="text-muted text-xs font-semibold">Protected</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.toggle-suspend', $user) }}" class="inline-form"
                                                onsubmit="return confirm('Are you sure you want to {{ $user->is_suspended ? 'UNSUSPEND' : 'SUSPEND' }} @' + '{{ $user->username }}?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-ghost btn-xs {{ $user->is_suspended ? 'btn-success-text' : 'btn-danger-text' }}">
                                                    {{ $user->is_suspended ? '🔓 Unsuspend' : '🚫 Suspend' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layout>
