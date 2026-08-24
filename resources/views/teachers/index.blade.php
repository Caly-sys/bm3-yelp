<x-layout title="Teachers">
    <section class="section page-header-section">
        <div class="container">
            <h1 class="page-title">Teacher Directory</h1>
            <p class="page-subtitle">Browse and discover teachers at BM3</p>

            {{-- Search & Filters --}}
            <form action="{{ route('teachers.index') }}" method="GET" class="filters-bar">
                <div class="search-input-wrapper search-input-wrapper-sm">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search teachers..." class="search-input" aria-label="Search teachers">
                </div>

                <select name="subject" class="form-select" aria-label="Filter by subject">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject }}" {{ request('subject') === $subject ? 'selected' : '' }}>
                            {{ $subject }}
                        </option>
                    @endforeach
                </select>

                <select name="sort" class="form-select" aria-label="Sort by">
                    <option value="highest_rated" {{ $sort === 'highest_rated' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="most_reviewed" {{ $sort === 'most_reviewed' ? 'selected' : '' }}>Most Reviewed</option>
                    <option value="recently_reviewed" {{ $sort === 'recently_reviewed' ? 'selected' : '' }}>Recently Reviewed</option>
                    <option value="alphabetical" {{ $sort === 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
                </select>

                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </section>

    <section class="section">
        <div class="container">
            @if($teachers->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">🔍</span>
                    <h3>No teachers found</h3>
                    <p>Try adjusting your search or filters.</p>
                    <a href="{{ route('teachers.index') }}" class="btn btn-primary">Clear Filters</a>
                </div>
            @else
                <div class="teacher-grid">
                    @foreach($teachers as $teacher)
                        <x-teacher-card :teacher="$teacher" />
                    @endforeach
                </div>

                <div class="pagination-wrapper">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>
    </section>
</x-layout>
