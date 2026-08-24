<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Report;
use App\Models\Review;

class ReportController extends Controller
{
    /**
     * Store a report for a review.
     */
    public function store(StoreReportRequest $request, Review $review)
    {
        // Check if user already reported this review
        $exists = Report::where('review_id', $review->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You have already reported this review.'], 422);
            }
            return back()->with('info', 'You have already reported this review.');
        }

        Report::create([
            'review_id' => $review->id,
            'user_id' => auth()->id(),
            ...$request->validated(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Report submitted. Thank you.']);
        }

        return back()->with('success', 'Report submitted. Thank you for helping us keep the community safe.');
    }
}
