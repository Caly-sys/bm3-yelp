<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\NewReviewNotification;
use App\Notifications\TeacherLowRatingWarning;
use App\Notifications\AdminLowRatingWarning;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show the form to create a new review.
     */
    public function create(Teacher $teacher)
    {
        // Check if user already reviewed this teacher
        $existingReview = Review::where('teacher_id', $teacher->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            return redirect()->route('teachers.show', $teacher)
                ->with('info', 'You have already reviewed this teacher.');
        }

        return view('reviews.create', compact('teacher'));
    }

    /**
     * Store a new review.
     */
    public function store(StoreReviewRequest $request, Teacher $teacher)
    {
        // Double-check for duplicate review
        $exists = Review::where('teacher_id', $teacher->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            return redirect()->route('teachers.show', $teacher)
                ->with('error', 'You have already reviewed this teacher.');
        }

        $review = Review::create([
            'teacher_id' => $teacher->id,
            'user_id' => auth()->id(),
            ...$request->validated(),
        ]);

        // Calculate point changes
        $rating = (int) $request->validated()['overall_rating'];
        $pointsChange = 0;
        
        if ($rating === 1) {
            $pointsChange = -3;
        } elseif ($rating === 2) {
            $pointsChange = -2;
        } elseif ($rating === 3) {
            $pointsChange = -1;
        } elseif ($rating === 5) {
            $pointsChange = 1;
        }
        
        // Update teacher's points
        if ($pointsChange !== 0) {
            $teacher->points += $pointsChange;
            $teacher->save();
        }

        // Send notifications
        if ($teacher->user_id && $teacher->user) {
            if ($rating <= 3) {
                // Low rating warning for teacher
                $teacher->user->notify(new TeacherLowRatingWarning($review, $teacher, abs($pointsChange)));
            } else {
                // Normal notification for 4 or 5 stars
                $teacher->user->notify(new NewReviewNotification($review, auth()->user()));
            }
        }
        
        // Warn admins if rating is low
        if ($rating <= 3) {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminLowRatingWarning($review, $teacher));
            }
        }
        
        event(new \App\Events\ReviewUpdated($teacher->id));

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Your review has been submitted successfully!');
    }

    /**
     * Show the form to edit a review.
     */
    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        $teacher = $review->teacher;

        return view('reviews.edit', compact('review', 'teacher'));
    }

    /**
     * Update an existing review.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);

        $review->update($request->validated());
        
        event(new \App\Events\ReviewUpdated($review->teacher_id));

        return redirect()->route('teachers.show', $review->teacher)
            ->with('success', 'Your review has been updated successfully!');
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $teacher = $review->teacher;
        $review->delete();
        
        event(new \App\Events\ReviewUpdated($teacher->id));

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Your review has been deleted.');
    }
}
