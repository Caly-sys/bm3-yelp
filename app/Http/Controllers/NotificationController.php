<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Show all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Fetch notifications for real-time polling.
     */
    public function fetch(Request $request)
    {
        $user = $request->user();
        $unreadCount = $user->unreadNotifications()->count();
        $recentNotifications = $user->notifications()->latest()->take(10)->get()->map(function($notif) {
            return [
                'id' => $notif->id,
                'message' => $notif->data['message'] ?? 'New notification',
                'time' => $notif->created_at->diffForHumans(),
                'is_read' => $notif->read_at !== null,
                'link' => $notif->data['link'] ?? null,
                'mark_as_read_url' => route('notifications.markAsRead', $notif->id),
                'delete_url' => route('notifications.destroy', $notif->id),
            ];
        });

        return response()->json([
            'unreadCount' => $unreadCount,
            'notifications' => $recentNotifications
        ]);
    }

    /**
     * Mark a single notification as read and redirect to its link.
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        $link = $notification->data['link'] ?? route('home');

        return redirect($link);
    }

    /**
     * Mark all unread notifications as read for the authenticated user.
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Delete all notifications for the authenticated user.
     */
    public function destroyAll(Request $request)
    {
        $request->user()->notifications()->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications cleared.');
    }
}
