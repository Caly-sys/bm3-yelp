<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewComment;
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

        // Notify the review author about the teacher response
        $reviewAuthor = $review->user;
        if ($reviewAuthor && $reviewAuthor->id !== $user->id) {
            $reviewAuthor->notify(new ReviewCommentNotification($comment));
        }

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Your response has been posted successfully!');
    }
}
