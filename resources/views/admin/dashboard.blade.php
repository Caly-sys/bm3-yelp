<x-layout title="Admin Dashboard">
    <section class="section page-header-section">
        <div class="container">
            <h1 class="page-title">Admin Dashboard</h1>
            <p class="page-subtitle">Manage BM3 Review platform</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            {{-- Stats Grid --}}
            <div class="admin-stats-grid">
                <div class="admin-stat-card">
                    <span class="admin-stat-icon">👨‍🏫</span>
                    <span class="admin-stat-number">{{ $stats['teachers'] }}</span>
                    <span class="admin-stat-label">Teachers</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-icon">👩‍🎓</span>
                    <span class="admin-stat-number">{{ $stats['students'] }}</span>
                    <span class="admin-stat-label">Students</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-icon">📝</span>
                    <span class="admin-stat-number">{{ $stats['published_reviews'] }}</span>
                    <span class="admin-stat-label">Reviews</span>
                </div>
                <div class="admin-stat-card admin-stat-warning">
                    <span class="admin-stat-icon">🚩</span>
                    <span class="admin-stat-number">{{ $stats['pending_reports'] }}</span>
                    <span class="admin-stat-label">Pending Reports</span>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="admin-quick-links">
                <a href="{{ route('admin.teachers.index') }}" class="admin-link-card">
                    <span>👨‍🏫</span> Manage Teachers
                </a>
                <a href="{{ route('admin.users.index') }}" class="admin-link-card">
                    <span>👥</span> Manage Users
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="admin-link-card">
                    <span>📝</span> Manage Reviews
                </a>
                <a href="{{ route('admin.reports.index') }}" class="admin-link-card">
                    <span>🚩</span> View Reports
                    @if($stats['pending_reports'] > 0)
                        <span class="badge">{{ $stats['pending_reports'] }}</span>
                    @endif
                </a>
            </div>

            <div class="admin-panels-grid">
                {{-- Recent Reviews --}}
                <div class="card">
                    <h3 class="card-title">Recent Reviews</h3>
                    @foreach($recentReviews as $review)
                        <div class="admin-list-item">
                            <div>
                                <strong>{{ '@' . ($review->user->username ?? 'deleted') }}</strong> reviewed
                                <strong>{{ $review->teacher->name ?? 'deleted' }}</strong>
                                <span class="text-muted"> — {{ $review->overall_rating }}/5</span>
                            </div>
                            <span class="text-muted text-sm">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Pending Reports --}}
                <div class="card">
                    <h3 class="card-title">Pending Reports</h3>
                    @forelse($recentReports as $report)
                        <div class="admin-list-item">
                            <div>
                                <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $report->reason)) }}</span>
                                by {{ '@' . ($report->user->username ?? 'deleted') }}
                            </div>
                            <span class="text-muted text-sm">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-muted">No pending reports. 🎉</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-layout>
