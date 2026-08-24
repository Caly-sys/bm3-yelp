<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Review;
use App\Models\Teacher;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with overview stats.
     */
    public function index()
    {
        $stats = [
            'teachers' => Teacher::count(),
            'students' => User::where('role', 'student')->count(),
            'reviews' => Review::count(),
            'published_reviews' => Review::where('status', 'published')->count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'suspended_users' => User::where('is_suspended', true)->count(),
        ];

        $recentReviews = Review::with(['user', 'teacher'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentReports = Report::with(['user', 'review.teacher'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReviews', 'recentReports'));
    }
}
