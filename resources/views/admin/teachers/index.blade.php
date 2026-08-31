<x-layout title="Manage Teachers">
    <x-admin-nav title="Teacher Directory" subtitle="Add, edit, and manage teacher profiles and subjects">
        <x-slot:actions>
            <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm">
                <span>➕</span> Add New Teacher
            </a>
        </x-slot:actions>
    </x-admin-nav>

    <section class="section admin-section">
        <div class="container">
            {{-- Search & Filter Toolbar --}}
            <form action="{{ route('admin.teachers.index') }}" method="GET" class="admin-filter-bar card mb-4">
                <div class="admin-filter-search">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by teacher name or subject..." class="form-input" aria-label="Search teachers">
                </div>

                <div class="admin-filter-controls">
                    <select name="subject" class="form-select" aria-label="Filter by subject">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub }}" {{ request('subject') === $sub ? 'selected' : '' }}>
                                {{ $sub }}
                            </option>
                        @endforeach
                    </select>

                    <select name="sort" class="form-select" aria-label="Sort by">
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="rating_desc" {{ request('sort') === 'rating_desc' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="reviews_desc" {{ request('sort') === 'reviews_desc' ? 'selected' : '' }}>Most Reviewed</option>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request()->hasAny(['search', 'subject', 'sort']))
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            {{-- Teachers Table --}}
            @if($teachers->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">👨‍🏫</span>
                    <h3>No teachers found</h3>
                    <p>Try adjusting your search criteria or add a new teacher.</p>
                    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm">➕ Add Teacher</a>
                </div>
            @else
                <div class="admin-table-wrapper card">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Subject</th>
                                <th>Rating</th>
                                <th>Reviews</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $teacher)
                                <tr>
                                    <td>
                                        <div class="admin-cell-user">
                                            <div class="admin-avatar-sm" style="background-color: #6C5CE7">
                                                @if($teacher->photo)
                                                    <img src="{{ Storage::url($teacher->photo) }}" alt="{{ $teacher->name }}">
                                                @else
                                                    <span>{{ $teacher->initials }}</span>
                                                @endif
                                            </div>
                                            <div class="admin-cell-details">
                                                <a href="{{ route('teachers.show', $teacher) }}" class="admin-item-title" target="_blank">
                                                    {{ $teacher->name }} ↗
                                                </a>
                                                @if($teacher->description)
                                                    <span class="admin-item-subtitle">{{ Str::limit($teacher->description, 50) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $teacher->subject }}</span>
                                    </td>
                                    <td>
                                        @if($teacher->reviews_count > 0)
                                            <div class="d-flex align-items-center gap-1">
                                                <x-rating-stars :rating="$teacher->reviews_avg_overall_rating ?? 0" size="sm" />
                                                <span class="font-bold text-sm">{{ number_format($teacher->reviews_avg_overall_rating ?? 0, 1) }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted text-sm">No ratings</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge">{{ $teacher->reviews_count }} {{ Str::plural('review', $teacher->reviews_count) }}</span>
                                    </td>
                                    <td class="admin-actions text-right">
                                        <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-ghost btn-xs">
                                            ✏️ Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" class="inline-form"
                                            onsubmit="return confirm('Are you sure you want to delete {{ $teacher->name }}? All associated reviews will also be removed.')">
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
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layout>
