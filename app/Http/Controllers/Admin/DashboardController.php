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
     * Display the admin dashboard with overview stats and analytics.
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
            'avg_rating' => round(Review::where('status', 'published')->avg('overall_rating') ?? 0, 2),
            'avg_teaching' => round(Review::where('status', 'published')->avg('teaching_rating') ?? 0, 1),
            'avg_explanation' => round(Review::where('status', 'published')->avg('explanation_rating') ?? 0, 1),
            'avg_fairness' => round(Review::where('status', 'published')->avg('fairness_rating') ?? 0, 1),
            'avg_workload' => round(Review::where('status', 'published')->avg('workload_rating') ?? 0, 1),
        ];

        $topTeachers = Teacher::has('reviews')
            ->withCount('reviews')
            ->withAvg('reviews', 'overall_rating')
            ->orderByDesc('reviews_avg_overall_rating')
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        $recentReviews = Review::with(['user', 'teacher'])
            ->withCount('reports')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $recentReports = Report::with(['user', 'review.user', 'review.teacher'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $recentUsers = User::withCount('reviews')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'topTeachers', 'recentReviews', 'recentReports', 'recentUsers'));
    }
}
