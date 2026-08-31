@props(['title' => 'Admin Dashboard', 'subtitle' => 'Manage and moderate BM3 platform'])

@php
    $pendingReportsCount = \App\Models\Report::where('status', 'pending')->count();
    $suspendedUsersCount = \App\Models\User::where('is_suspended', true)->count();
@endphp

<div class="admin-header-wrapper">
    <div class="container">
        <div class="admin-header-top">
            <div class="admin-title-area">
                <div class="admin-portal-badge">⚡ Admin Portal</div>
                <h1 class="admin-title">{{ $title }}</h1>
                @if($subtitle)
                    <p class="admin-subtitle">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="admin-header-actions">
                @if(isset($actions))
                    {{ $actions }}
                @endif
                <a href="{{ route('home') }}" class="btn btn-outline btn-sm" target="_blank">
                    <span>🌐</span> Live Site
                </a>
            </div>
        </div>

        <nav class="admin-nav-tabs" aria-label="Admin Navigation Tabs">
            <a href="{{ route('admin.dashboard') }}" class="admin-tab {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="admin-tab-icon">📊</span>
                <span>Overview</span>
            </a>
            <a href="{{ route('admin.teachers.index') }}" class="admin-tab {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                <span class="admin-tab-icon">👨‍🏫</span>
                <span>Teachers</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="admin-tab {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="admin-tab-icon">👥</span>
                <span>Users</span>
                @if($suspendedUsersCount > 0)
                    <span class="admin-tab-counter counter-muted" title="{{ $suspendedUsersCount }} suspended">{{ $suspendedUsersCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.reviews.index') }}" class="admin-tab {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <span class="admin-tab-icon">📝</span>
                <span>Reviews</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="admin-tab {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <span class="admin-tab-icon">🚩</span>
                <span>Reports</span>
                @if($pendingReportsCount > 0)
                    <span class="admin-tab-counter counter-danger" title="{{ $pendingReportsCount }} pending reports">{{ $pendingReportsCount }}</span>
                @endif
            </a>
        </nav>
    </div>
</div>
