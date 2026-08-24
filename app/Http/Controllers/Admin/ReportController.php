<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'review.user', 'review.teacher'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Resolve a report (delete the reported review).
     */
    public function resolve(Report $report)
    {
        // Hide the reported review so it is removed from public view
        if ($report->review) {
            $report->review->update(['status' => 'hidden']);
        }

        // Mark this and all related reports as resolved
        Report::where('review_id', $report->review_id)
            ->update(['status' => 'resolved']);

        return back()->with('success', 'Report resolved. The review has been removed from public view.');
    }

    /**
     * Dismiss a report (keep the review).
     */
    public function dismiss(Report $report)
    {
        $report->update(['status' => 'dismissed']);

        return back()->with('success', 'Report dismissed.');
    }
}
