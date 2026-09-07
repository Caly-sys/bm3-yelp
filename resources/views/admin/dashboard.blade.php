<x-layout title="Admin Dashboard">
    <x-admin-nav title="Admin Dashboard" subtitle="Platform metrics, moderation queue, and system management">
        <x-slot:actions>
            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm">
                <span>➕</span> Add Teacher
            </a>
        </x-slot:actions>
    </x-admin-nav>

    <section class="section admin-section">
        <div class="container">
            {{-- Primary Stats Grid --}}
            <div class="admin-stats-grid">
                <div class="admin-stat-card">
                    <div class="admin-stat-header">
                        <span class="admin-stat-icon">👨‍🏫</span>
                        <a href="{{ route('admin.teachers.index') }}" class="admin-stat-link">Manage →</a>
                    </div>
                    <span class="admin-stat-number">{{ $stats['teachers'] }}</span>
                    <span class="admin-stat-label">Total Teachers</span>
                </div>

                <div class="admin-stat-card">
                    <div class="admin-stat-header">
                        <span class="admin-stat-icon">👩‍🎓</span>
                        <a href="{{ route('admin.users.index') }}" class="admin-stat-link">Manage →</a>
                    </div>
                    <span class="admin-stat-number">{{ $stats['students'] }}</span>
                    <span class="admin-stat-label">Registered Students</span>
                </div>

                <div class="admin-stat-card">
                    <div class="admin-stat-header">
                        <span class="admin-stat-icon">📝</span>
                        <a href="{{ route('admin.reviews.index') }}" class="admin-stat-link">View all →</a>
                    </div>
                    <span class="admin-stat-number">{{ $stats['published_reviews'] }}</span>
                    <span class="admin-stat-label">Published Reviews</span>
                </div>

                <div class="admin-stat-card">
                    <div class="admin-stat-header">
                        <span class="admin-stat-icon">⭐</span>
                        <span class="badge badge-primary">Platform Avg</span>
                    </div>
                    <span class="admin-stat-number">{{ number_format($stats['avg_rating'], 1) }}<span class="admin-stat-unit">/5.0</span></span>
                    <span class="admin-stat-label">Overall Rating Score</span>
                </div>

                <div class="admin-stat-card {{ $stats['pending_reports'] > 0 ? 'admin-stat-alert' : '' }}">
                    <div class="admin-stat-header">
                        <span class="admin-stat-icon">🚩</span>
                        <a href="{{ route('admin.reports.index') }}" class="admin-stat-link">Review →</a>
                    </div>
                    <span class="admin-stat-number">{{ $stats['pending_reports'] }}</span>
                    <span class="admin-stat-label">Pending Reports</span>
                </div>

                <div class="admin-stat-card">
                    <div class="admin-stat-header">
                        <span class="admin-stat-icon">🚫</span>
                        <a href="{{ route('admin.users.index', ['status' => 'suspended']) }}" class="admin-stat-link">Filter →</a>
                    </div>
                    <span class="admin-stat-number">{{ $stats['suspended_users'] }}</span>
                    <span class="admin-stat-label">Suspended Users</span>
                </div>
            </div>

            {{-- Rating Dimensions Breakdown Bar --}}
            <div class="card admin-breakdown-card">
                <div class="admin-card-header">
                    <div>
                        <h3 class="card-title mb-0">Platform Teaching Metrics</h3>
                        <p class="text-muted text-sm">Aggregated scores across all published student reviews</p>
                    </div>
                    <span class="badge badge-info">{{ $stats['published_reviews'] }} Total Reviews</span>
                </div>

                <div class="admin-metrics-row">
                    <div class="admin-metric-box">
                        <span class="admin-metric-label">Teaching Quality</span>
                        <div class="admin-metric-bar-wrap">
                            <div class="admin-metric-bar" style="width: {{ ($stats['avg_teaching'] / 5) * 100 }}%"></div>
                        </div>
                        <span class="admin-metric-val">{{ number_format($stats['avg_teaching'], 1) }} / 5</span>
                    </div>

                    <div class="admin-metric-box">
                        <span class="admin-metric-label">Explanation Clarity</span>
                        <div class="admin-metric-bar-wrap">
                            <div class="admin-metric-bar" style="width: {{ ($stats['avg_explanation'] / 5) * 100 }}%"></div>
                        </div>
                        <span class="admin-metric-val">{{ number_format($stats['avg_explanation'], 1) }} / 5</span>
                    </div>

                    <div class="admin-metric-box">
                        <span class="admin-metric-label">Fairness & Grading</span>
                        <div class="admin-metric-bar-wrap">
                            <div class="admin-metric-bar" style="width: {{ ($stats['avg_fairness'] / 5) * 100 }}%"></div>
                        </div>
                        <span class="admin-metric-val">{{ number_format($stats['avg_fairness'], 1) }} / 5</span>
                    </div>

                    <div class="admin-metric-box">
                        <span class="admin-metric-label">Workload Balance</span>
                        <div class="admin-metric-bar-wrap">
                            <div class="admin-metric-bar" style="width: {{ ($stats['avg_workload'] / 5) * 100 }}%"></div>
                        </div>
                        <span class="admin-metric-val">{{ number_format($stats['avg_workload'], 1) }} / 5</span>
                    </div>
                </div>
            </div>

            {{-- Main Action & Moderation Grid --}}
            <div class="admin-panels-grid">
                {{-- Pending Reports Queue --}}
                <div class="card admin-panel">
                    <div class="admin-card-header">
                        <div class="admin-panel-title">
                            <span class="admin-panel-icon">🚩</span>
                            <div>
                                <h3 class="card-title mb-0">Moderation Queue</h3>
                                <p class="text-muted text-sm">Flagged reviews awaiting admin decision</p>
                            </div>
                        </div>
                        @if($stats['pending_reports'] > 0)
                            <span class="badge badge-danger">{{ $stats['pending_reports'] }} Pending</span>
                        @endif
                    </div>

                    <div class="admin-list-container">
                        @forelse($recentReports as $report)
                            <div class="admin-activity-item">
                                <div class="admin-activity-main">
                                    <div class="admin-activity-meta">
                                        <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $report->reason)) }}</span>
                                        <span class="text-muted text-xs">• Reported by <strong>{{ '@' . ($report->user->username ?? 'deleted') }}</strong></span>
                                        <span class="text-muted text-xs">• {{ $report->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="admin-activity-content">
                                        Teacher: <strong>{{ $report->review->teacher->name ?? 'Deleted Teacher' }}</strong> — 
                                        <em>"{{ Str::limit($report->review->comment ?? 'Review deleted', 70) }}"</em>
                                    </p>
                                    @if($report->details)
                                        <div class="admin-report-note">
                                            <strong>Reporter note:</strong> {{ $report->details }}
                                        </div>
                                    @endif
                                </div>
                                <div class="admin-item-actions">
                                    <form method="POST" action="{{ route('admin.reports.resolve', $report) }}"
                                        onsubmit="return confirm('Resolve report and hide this review?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-danger btn-xs" title="Hide reported review">Hide Review</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-ghost btn-xs" title="Dismiss report">Dismiss</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty-state-sm">
                                <span class="empty-emoji">🎉</span>
                                <p>No pending reports. Moderation queue is clean!</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="admin-card-footer">
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost btn-sm btn-block">
                            View All Reports →
                        </a>
                    </div>
                </div>

                {{-- Recent Reviews Stream --}}
                <div class="card admin-panel">
                    <div class="admin-card-header">
                        <div class="admin-panel-title">
                            <span class="admin-panel-icon">📝</span>
                            <div>
                                <h3 class="card-title mb-0">Recent Reviews</h3>
                                <p class="text-muted text-sm">Latest feedback submitted by students</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost btn-xs">All Reviews →</a>
                    </div>

                    <div class="admin-list-container">
                        @forelse($recentReviews as $review)
                            <div class="admin-activity-item">
                                <div class="admin-activity-main">
                                    <div class="admin-activity-meta">
                                        <span class="admin-reviewer-badge">{{ '@' . ($review->user->username ?? 'deleted') }}</span>
                                        <span class="text-muted text-xs">reviewed</span>
                                        <a href="{{ route('teachers.show', $review->teacher) }}" class="admin-target-link">
                                            {{ $review->teacher->name ?? 'deleted' }}
                                        </a>
                                        <x-rating-stars :rating="$review->overall_rating" size="sm" />
                                        <span class="text-muted text-xs">• {{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="admin-activity-content">
                                        "{{ Str::limit(\App\Helpers\ProfanityFilter::filter($review->comment), 85) }}"
                                    </p>
                                </div>
                                <div class="admin-item-actions">
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                        onsubmit="return confirm('Delete this review permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-xs btn-danger-text">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty-state-sm">
                                <span class="empty-emoji">📝</span>
                                <p>No reviews submitted yet.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="admin-card-footer">
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost btn-sm btn-block">
                            Manage All Reviews →
                        </a>
                    </div>
                </div>
            </div>

            {{-- Secondary Row: Top Teachers Leaderboard & New Users --}}
            <div class="admin-panels-grid mt-4">
                {{-- Top Rated Teachers Leaderboard --}}
                <div class="card admin-panel">
                    <div class="admin-card-header">
                        <div class="admin-panel-title">
                            <span class="admin-panel-icon">🏆</span>
                            <div>
                                <h3 class="card-title mb-0">Top Rated Teachers</h3>
                                <p class="text-muted text-sm">Highest scoring teachers by student reviews</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-ghost btn-xs">Manage →</a>
                    </div>

                    <div class="admin-list-container">
                        @forelse($topTeachers as $index => $teacher)
                            <div class="admin-leaderboard-item">
                                <div class="admin-leaderboard-rank rank-{{ $index + 1 }}">
                                    #{{ $index + 1 }}
                                </div>
                                <div class="admin-leaderboard-info">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="admin-leaderboard-name">
                                        {{ $teacher->name }}
                                    </a>
                                    <span class="admin-leaderboard-subject">{{ $teacher->subject }}</span>
                                </div>
                                <div class="admin-leaderboard-score">
                                    <div class="score-stars">
                                        <x-rating-stars :rating="$teacher->reviews_avg_overall_rating ?? 0" size="sm" />
                                        <span class="score-num">{{ number_format($teacher->reviews_avg_overall_rating ?? 0, 1) }}</span>
                                    </div>
                                    <span class="score-count">{{ $teacher->reviews_count }} {{ Str::plural('review', $teacher->reviews_count) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty-state-sm">
                                <span class="empty-emoji">👨‍🏫</span>
                                <p>No rated teachers yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Users --}}
                <div class="card admin-panel">
                    <div class="admin-card-header">
                        <div class="admin-panel-title">
                            <span class="admin-panel-icon">👥</span>
                            <div>
                                <h3 class="card-title mb-0">Recently Joined Users</h3>
                                <p class="text-muted text-sm">Newest registered student accounts</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-xs">Manage Users →</a>
                    </div>

                    <div class="admin-list-container">
                        @forelse($recentUsers as $user)
                            <div class="admin-user-item">
                                <div class="admin-user-avatar">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                <div class="admin-user-info">
                                    <div class="admin-user-name-row">
                                        <strong>{{ $user->name }}</strong>
                                        <span class="text-muted text-xs">{{ '@' . $user->username }}</span>
                                    </div>
                                    <span class="text-muted text-xs">Joined {{ $user->created_at->format('M d, Y') }} • {{ $user->reviews_count }} reviews</span>
                                </div>
                                <div class="admin-user-status">
                                    @if($user->isAdmin())
                                        <span class="badge badge-admin">Admin</span>
                                    @elseif($user->is_suspended)
                                        <span class="badge badge-danger">Suspended</span>
                                    @else
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty-state-sm">
                                <span class="empty-emoji">👥</span>
                                <p>No users found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
