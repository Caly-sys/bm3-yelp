<x-layout title="Home">
    {{-- Hero Section --}}
    <section class="hero" id="heroSection">
        <canvas id="pixiCanvas" class="hero-canvas"></canvas>
        <div class="hero-content container">
            <h1 class="hero-title">
                Find & Review Your
                <span class="gradient-text">Teachers</span>
            </h1>
            <p class="hero-subtitle">Share your experience. Help fellow BM3 students find the best teachers at SMK Bina Mandiri Multimedia.</p>

            <form action="{{ route('teachers.index') }}" method="GET" class="hero-search">
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="search" placeholder="Search teacher name, subject, or keyword..." class="search-input" aria-label="Search teachers">
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Search</button>
            </form>

            <div class="popular-tags">
                <a href="{{ route('teachers.index', ['subject' => 'Pemrograman Web']) }}" class="tag-chip">#Pemrograman Web</a>
                <a href="{{ route('teachers.index', ['subject' => 'Desain Grafis']) }}" class="tag-chip">#Desain Grafis</a>
                <a href="{{ route('teachers.index', ['subject' => 'Matematika']) }}" class="tag-chip">#Matematika</a>
                <a href="{{ route('teachers.index', ['subject' => 'Bahasa Inggris']) }}" class="tag-chip">#Bahasa Inggris</a>
                <a href="{{ route('teachers.index', ['subject' => 'Multimedia']) }}" class="tag-chip">#Multimedia</a>
                <a href="{{ route('teachers.index', ['subject' => 'Animasi']) }}" class="tag-chip">#Animasi</a>
            </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number" data-count="{{ $stats['teachers'] }}">{{ $stats['teachers'] }}</span>
                    <span class="stat-label">Teachers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="{{ $stats['reviews'] }}">{{ $stats['reviews'] }}</span>
                    <span class="stat-label">Reviews</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="{{ $stats['students'] }}">{{ $stats['students'] }}</span>
                    <span class="stat-label">Students</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Top Rated Teachers (Pixiv Ranking) --}}
    <section class="section" id="topRated">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">🏆 Top Teacher Ranking</h2>
                <a href="{{ route('teachers.index', ['sort' => 'highest_rated']) }}" class="section-link">View Full Ranking →</a>
            </div>
            <div class="teacher-grid">
                @foreach($topRated as $teacher)
                    <x-teacher-card :teacher="$teacher" :rank="$loop->iteration" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Recently Reviewed --}}
    <section class="section section-alt" id="recentlyReviewed">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">📝 Recently Reviewed</h2>
                <a href="{{ route('teachers.index', ['sort' => 'recently_reviewed']) }}" class="section-link">View all →</a>
            </div>
            <div class="teacher-grid">
                @foreach($recentlyReviewed as $teacher)
                    <x-teacher-card :teacher="$teacher" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="section cta-section">
        <div class="container">
            <div class="cta-card">
                <h2>Ready to share your experience?</h2>
                <p>Help other BM3 students by reviewing your teachers. Your feedback matters!</p>
                <div class="cta-buttons">
                    <a href="{{ route('teachers.index') }}" class="btn btn-primary btn-lg">Browse Teachers</a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline btn-lg">Create Account</a>
                    @endguest
                </div>
            </div>
        </div>
    </section>
</x-layout>
