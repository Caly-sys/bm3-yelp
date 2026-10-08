<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewComment;
use App\Models\User;
use App\Notifications\ReviewCommentNotification;
use Illuminate\Http\Request;

class ReviewCommentController extends Controller
{
    /**
     * Store a new comment/response on a review.
     * Only the teacher linked to the reviewed profile (or admins) can respond.
     */
    public function store(Request $request, Review $review)
    {
        $request->validate([
            'content' => 'required|string|min:2|max:2000',
        ]);

        $user = $request->user();
        $teacher = $review->teacher;

        // Authorization: only the teacher linked to this profile or admins can comment
        $isLinkedTeacher = $teacher->user_id && $teacher->user_id === $user->id;
        $isAdmin = $user->isAdmin();

        if (!$isLinkedTeacher && !$isAdmin) {
            abort(403, 'You are not authorized to respond to this review.');
        }

        $comment = ReviewComment::create([
            'review_id' => $review->id,
            'user_id' => $user->id,
            'content' => $request->input('content'),
        ]);

        // Collect all user IDs who should be notified:
        // 1. The review author
        // 2. All previous commenters on this review
        // Exclude the current user (they don't need to notify themselves)
        $userIdsToNotify = collect();

        // Add the review author
        if ($review->user_id) {
            $userIdsToNotify->push($review->user_id);
        }

        // Add all previous commenters on this review
        $previousCommenterIds = ReviewComment::where('review_id', $review->id)
            ->where('id', '!=', $comment->id) // exclude current comment
            ->pluck('user_id');
        $userIdsToNotify = $userIdsToNotify->merge($previousCommenterIds);

        // Remove duplicates and exclude the current user
        $userIdsToNotify = $userIdsToNotify->unique()->reject(fn($id) => $id === $user->id);

        // Send notifications to all relevant users
        $usersToNotify = User::whereIn('id', $userIdsToNotify)->get();
        foreach ($usersToNotify as $notifyUser) {
            $notifyUser->notify(new ReviewCommentNotification($comment));
        }
        
        event(new \App\Events\ReviewUpdated($teacher->id));

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Your response has been posted successfully!');
    }
}
