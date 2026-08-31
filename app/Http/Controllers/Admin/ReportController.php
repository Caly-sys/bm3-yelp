<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['user', 'review.user', 'review.teacher']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->input('reason'));
        }

        if (!$request->filled('status')) {
            $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END");
        }

        $reports = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $pendingCount = Report::where('status', 'pending')->count();
        $resolvedCount = Report::where('status', 'resolved')->count();
        $dismissedCount = Report::where('status', 'dismissed')->count();

        return view('admin.reports.index', compact('reports', 'pendingCount', 'resolvedCount', 'dismissedCount'));
    }

    /**
     * Resolve a report (hide the reported review and mark reports resolved).
     */
    public function resolve(Report $report)
    {
        if ($report->review) {
            $report->review->update(['status' => 'hidden']);
        }

        Report::where('review_id', $report->review_id)
            ->update(['status' => 'resolved']);

        return back()->with('success', 'Report resolved. The review has been hidden from public view.');
    }

    /**
     * Dismiss a report (keep the review active).
     */
    public function dismiss(Report $report)
    {
        $report->update(['status' => 'dismissed']);

        return back()->with('success', 'Report marked as dismissed.');
    }
}
