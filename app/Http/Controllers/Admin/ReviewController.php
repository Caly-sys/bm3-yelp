<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'teacher'])->withCount(['votes', 'reports']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teacher', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('flagged') && $request->input('flagged') == '1') {
            $query->has('reports');
        }

        $reviews = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $teachers = Teacher::orderBy('name')->get(['id', 'name']);

        return view('admin.reviews.index', compact('reviews', 'teachers'));
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
