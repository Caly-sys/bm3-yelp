<x-layout title="Review Reports">
    <x-admin-nav title="Flagged Reports Queue" subtitle="Review reported content, take moderation actions, or dismiss false reports">
    </x-admin-nav>

    <section class="section admin-section">
        <div class="container">
            {{-- Filter Bar --}}
            <form action="{{ route('admin.reports.index') }}" method="GET" class="admin-filter-bar card mb-4">
                <div class="admin-filter-controls">
                    <select name="status" class="form-select" aria-label="Filter by status">
                        <option value="">Status: All Reports</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Only ({{ $pendingCount }})</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved Only ({{ $resolvedCount }})</option>
                        <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Dismissed Only ({{ $dismissedCount }})</option>
                    </select>

                    <select name="reason" class="form-select" aria-label="Filter by reason">
                        <option value="">All Reasons</option>
                        <option value="spam" {{ request('reason') === 'spam' ? 'selected' : '' }}>Spam</option>
                        <option value="harassment" {{ request('reason') === 'harassment' ? 'selected' : '' }}>Harassment</option>
                        <option value="offensive" {{ request('reason') === 'offensive' ? 'selected' : '' }}>Offensive Content</option>
                        <option value="personal_info" {{ request('reason') === 'personal_info' ? 'selected' : '' }}>Personal Info</option>
                        <option value="fake" {{ request('reason') === 'fake' ? 'selected' : '' }}>Fake Review</option>
                        <option value="other" {{ request('reason') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request()->hasAny(['status', 'reason']))
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if($reports->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">🚩</span>
                    <h3>No reports found</h3>
                    <p>There are no reports matching the selected filters.</p>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-primary btn-sm">Reset Filters</a>
                </div>
            @else
                <div class="admin-table-wrapper card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Reason</th>
                                <th>Reporter</th>
                                <th>Reported Review</th>
                                <th>Teacher</th>
                                <th>Status</th>
                                <th>Reported On</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr class="{{ $report->status === 'pending' ? 'row-pending' : '' }}">
                                    <td>
                                        <span class="badge {{ $report->status === 'pending' ? 'badge-warning' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $report->reason)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-reviewer-badge">{{ '@' . ($report->user->username ?? 'deleted') }}</span>
                                    </td>
                                    <td>
                                        <div class="admin-cell-details">
                                            <span class="text-xs text-muted">Author: <strong>{{ '@' . ($report->review->user->username ?? 'deleted') }}</strong></span>
                                            <p class="admin-item-subtitle" title="{{ $report->review->comment ?? 'Review deleted' }}">
                                                "{{ Str::limit($report->review->comment ?? 'Review deleted', 55) }}"
                                            </p>
                                            @if($report->details)
                                                <div class="admin-report-note">
                                                    <strong>Note:</strong> {{ $report->details }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($report->review && $report->review->teacher)
                                            <a href="{{ route('teachers.show', $report->review->teacher) }}" class="admin-target-link" target="_blank">
                                                {{ $report->review->teacher->name }} ↗
                                            </a>
                                        @else
                                            <span class="text-muted">Deleted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->status === 'pending')
                                            <span class="badge badge-warning">⏳ Pending</span>
                                        @elseif($report->status === 'resolved')
                                            <span class="badge badge-success">✓ Resolved</span>
                                        @else
                                            <span class="badge">✕ Dismissed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted text-xs">{{ $report->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="admin-actions text-right">
                                        @if($report->status === 'pending')
                                            <form method="POST" action="{{ route('admin.reports.resolve', $report) }}" class="inline-form"
                                                onsubmit="return confirm('Resolve this report? This will HIDE the review from public view.')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-danger btn-xs">
                                                    Hide Review
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}" class="inline-form">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-ghost btn-xs">
                                                    Dismiss
                                                </button>
                                            </form>
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
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layout>
