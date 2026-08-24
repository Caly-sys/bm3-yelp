<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use App\Models\Teacher;
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
                ->with('info', 'You have already reviewed this teacher. You can edit your existing review.');
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

        Review::create([
            'teacher_id' => $teacher->id,
            'user_id' => auth()->id(),
            ...$request->validated(),
        ]);

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

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Your review has been deleted.');
    }
}
