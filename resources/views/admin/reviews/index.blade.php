<x-layout title="Manage Reviews">
    <x-admin-nav title="Review Moderation" subtitle="Inspect all student reviews, monitor flagged content, and remove violations">
    </x-admin-nav>

    <section class="section admin-section">
        <div class="container">
            {{-- Filter Bar --}}
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="admin-filter-bar card mb-4">
                <div class="admin-filter-search">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search comments, students, or teachers..." class="form-input" aria-label="Search reviews">
                </div>

                <div class="admin-filter-controls">
                    <select name="teacher_id" class="form-select" aria-label="Filter by teacher">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="form-select" aria-label="Filter by status">
                        <option value="">All Statuses</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                    </select>

                    <label class="admin-checkbox-label">
                        <input type="checkbox" name="flagged" value="1" {{ request('flagged') == '1' ? 'checked' : '' }}>
                        <span>🚩 Flagged only</span>
                    </label>

                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request()->hasAny(['search', 'teacher_id', 'status', 'flagged']))
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if($reviews->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">📝</span>
                    <h3>No reviews found</h3>
                    <p>Try adjusting your search criteria or filters.</p>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-primary btn-sm">Clear Filters</a>
                </div>
            @else
                <div class="admin-table-wrapper card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Author</th>
                                <th>Teacher</th>
                                <th>Score</th>
                                <th>Comment</th>
                                <th>Votes</th>
                                <th>Flags</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $review)
                                <tr class="{{ $review->reports_count > 0 ? 'row-flagged' : '' }}">
                                    <td>
                                        <span class="admin-reviewer-badge">{{ '@' . ($review->user->username ?? 'deleted') }}</span>
                                    </td>
                                    <td>
                                        @if($review->teacher)
                                            <a href="{{ route('teachers.show', $review->teacher) }}" class="admin-target-link" target="_blank">
                                                {{ $review->teacher->name }} ↗
                                            </a>
                                        @else
                                            <span class="text-muted">Deleted Teacher</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <x-rating-stars :rating="$review->overall_rating" size="sm" />
                                            <span class="font-bold text-sm">{{ $review->overall_rating }}/5</span>
                                        </div>
                                    </td>
                                    <td class="admin-comment-cell" title="{{ $review->comment }}">
                                        "{{ Str::limit($review->comment, 65) }}"
                                    </td>
                                    <td>
                                        <span class="text-xs font-bold text-muted">👍 {{ $review->votes_count }}</span>
                                    </td>
                                    <td>
                                        @if($review->reports_count > 0)
                                            <a href="{{ route('admin.reports.index') }}" class="badge badge-danger">
                                                🚩 {{ $review->reports_count }}
                                            </a>
                                        @else
                                            <span class="text-muted text-xs">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($review->status === 'published')
                                            <span class="badge badge-success">Published</span>
                                        @else
                                            <span class="badge badge-danger">Hidden</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted text-xs">{{ $review->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="admin-actions text-right">
                                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="inline-form"
                                            onsubmit="return confirm('Permanently delete this review?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-xs btn-danger-text">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layout>
