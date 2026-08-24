<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
}
