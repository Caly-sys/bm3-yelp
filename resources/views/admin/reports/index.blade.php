<x-layout title="Manage Reports">
    <section class="section page-header-section">
        <div class="container">
            <a href="{{ route('admin.dashboard') }}" class="back-link">← Admin Dashboard</a>
            <h1 class="page-title">Review Reports</h1>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Reporter</th>
                            <th>Reason</th>
                            <th>Review By</th>
                            <th>Teacher</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>{{ '@' . ($report->user->username ?? 'deleted') }}</td>
                                <td><span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $report->reason)) }}</span></td>
                                <td>{{ '@' . ($report->review->user->username ?? 'deleted') }}</td>
                                <td>{{ $report->review->teacher->name ?? 'deleted' }}</td>
                                <td>
                                    @if($report->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($report->status === 'resolved')
                                        <span class="badge badge-success">Resolved</span>
                                    @else
                                        <span class="badge">Dismissed</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('M d, Y') }}</td>
                                <td class="admin-actions">
                                    @if($report->status === 'pending')
                                        <form method="POST" action="{{ route('admin.reports.resolve', $report) }}"
                                            onsubmit="return confirm('Resolve report? This will DELETE the reviewed content.')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-ghost btn-sm btn-danger-text">Resolve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-ghost btn-sm">Dismiss</button>
                                        </form>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrapper">{{ $reports->links() }}</div>
        </div>
    </section>
</x-layout>
