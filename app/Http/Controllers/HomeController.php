<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with featured teachers and site stats.
     */
    public function index()
    {
        // Site statistics
        $stats = [
            'teachers' => Teacher::count(),
            'reviews' => Review::where('status', 'published')->count(),
            'students' => User::where('role', 'student')->count(),
        ];

        // Top-rated teachers (by average overall_rating)
        $topRated = Teacher::withCount(['reviews' => function ($query) {
                $query->where('status', 'published');
            }])
            ->withAvg(['reviews' => function ($query) {
                $query->where('status', 'published');
            }], 'overall_rating')
            ->whereHas('reviews', function ($query) {
                $query->where('status', 'published');
            })
            ->orderByDesc('reviews_avg_overall_rating')
            ->take(6)
            ->get();

        // Recently reviewed teachers
        $recentlyReviewed = Teacher::withCount(['reviews' => function ($query) {
                $query->where('status', 'published');
            }])
            ->withAvg(['reviews' => function ($query) {
                $query->where('status', 'published');
            }], 'overall_rating')
            ->whereHas('reviews', function ($query) {
                $query->where('status', 'published');
            })
            ->orderByDesc(
                Review::select('created_at')
                    ->whereColumn('teacher_id', 'teachers.id')
                    ->where('status', 'published')
                    ->orderByDesc('created_at')
                    ->limit(1)
            )
            ->take(6)
            ->get();

        return view('home', compact('stats', 'topRated', 'recentlyReviewed'));
    }
}
