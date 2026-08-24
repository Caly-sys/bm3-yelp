<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewVote;
use Illuminate\Http\Request;

class ReviewVoteController extends Controller
{
    /**
     * Toggle a helpful vote on a review.
     * If the user already voted, remove the vote.
     * If not, add a vote.
     */
    public function toggle(Request $request, Review $review)
    {
        $userId = auth()->id();

        // Don't allow voting on your own review
        if ($review->user_id === $userId) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You cannot vote on your own review.'], 422);
            }
            return back()->with('error', 'You cannot vote on your own review.');
        }

        $vote = ReviewVote::where('review_id', $review->id)
            ->where('user_id', $userId)
            ->first();

        if ($vote) {
            $vote->delete();
            $voted = false;
        } else {
            ReviewVote::create([
                'review_id' => $review->id,
                'user_id' => $userId,
            ]);
            $voted = true;
        }

        $count = $review->votes()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'voted' => $voted,
                'count' => $count,
            ]);
        }

        return back();
    }
}
