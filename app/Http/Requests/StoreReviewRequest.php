<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'overall_rating' => 'required|integer|min:1|max:5',
            'teaching_rating' => 'required|integer|min:1|max:5',
            'explanation_rating' => 'required|integer|min:1|max:5',
            'fairness_rating' => 'required|integer|min:1|max:5',
            'workload_rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'overall_rating.required' => 'Please provide an overall rating.',
            'overall_rating.min' => 'Rating must be at least 1 star.',
            'overall_rating.max' => 'Rating cannot exceed 5 stars.',
            'comment.required' => 'Please write a comment.',
            'comment.min' => 'Your comment must be at least 10 characters long.',
            'comment.max' => 'Your comment cannot exceed 2000 characters.',
        ];
    }
}
