<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display the teacher directory with search, sort, and pagination.
     */
    public function index(Request $request)
    {
        $query = Teacher::withCount(['reviews' => function ($q) {
                $q->where('status', 'published');
            }])
            ->withAvg(['reviews' => function ($q) {
                $q->where('status', 'published');
            }], 'overall_rating');

        // Search by name or subject
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Filter by subject
        if ($subject = $request->input('subject')) {
            $query->where('subject', $subject);
        }

        // Sort options
        $sort = $request->input('sort', 'highest_rated');
        switch ($sort) {
            case 'most_reviewed':
                $query->orderByDesc('reviews_count');
                break;
            case 'alphabetical':
                $query->orderBy('name');
                break;
            case 'recently_reviewed':
                $query->orderByDesc(
                    \App\Models\Review::select('created_at')
                        ->whereColumn('teacher_id', 'teachers.id')
                        ->where('status', 'published')
                        ->orderByDesc('created_at')
                        ->limit(1)
                );
                break;
            case 'highest_rated':
            default:
                $query->orderByDesc('reviews_avg_overall_rating');
                break;
        }

        $teachers = $query->paginate(12)->withQueryString();

        // Get unique subjects for the filter dropdown
        $subjects = Teacher::distinct()->pluck('subject')->sort()->values();

        return view('teachers.index', compact('teachers', 'subjects', 'sort'));
    }

    /**
     * Display a specific teacher's profile with reviews.
     */
    public function show(Teacher $teacher)
    {
        $averages = $teacher->allAverageRatings();
        $reviewCount = $teacher->publishedReviewCount();

        $reviews = $teacher->reviews()
            ->where('status', 'published')
            ->with(['user', 'votes'])
            ->withCount('votes')
            ->orderByDesc('created_at')
            ->paginate(10);

        // Check if the current user has already reviewed this teacher
        $userReview = null;
        $hasReviewed = false;
        if (auth()->check()) {
            $userReview = $teacher->reviews()
                ->where('user_id', auth()->id())
                ->first();
            $hasReviewed = $userReview !== null;
        }

        return view('teachers.show', compact(
            'teacher',
            'averages',
            'reviewCount',
            'reviews',
            'userReview',
            'hasReviewed'
        ));
    }
}
