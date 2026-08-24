<x-layout title="Manage Reviews">
    <section class="section page-header-section">
        <div class="container">
            <a href="{{ route('admin.dashboard') }}" class="back-link">← Admin Dashboard</a>
            <h1 class="page-title">Manage Reviews</h1>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Author</th>
                            <th>Teacher</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Votes</th>
                            <th>Reports</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                            <tr class="{{ $review->reports_count > 0 ? 'row-flagged' : '' }}">
                                <td>{{ '@' . ($review->user->username ?? 'deleted') }}</td>
                                <td><a href="{{ route('teachers.show', $review->teacher) }}">{{ $review->teacher->name ?? 'deleted' }}</a></td>
                                <td>{{ $review->overall_rating }}/5</td>
                                <td class="admin-comment-cell">{{ Str::limit($review->comment, 60) }}</td>
                                <td>{{ $review->votes_count }}</td>
                                <td>
                                    @if($review->reports_count > 0)
                                        <span class="badge badge-danger">{{ $review->reports_count }}</span>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td>{{ $review->created_at->format('M d, Y') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                        onsubmit="return confirm('Delete this review?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm btn-danger-text">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrapper">{{ $reviews->links() }}</div>
        </div>
    </section>
</x-layout>
