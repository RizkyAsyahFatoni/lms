<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of user notifications.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->query('filter', 'all'); // 'all' or 'unread'

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount', 'totalCount'));
    }

    /**
     * Mark a specific notification as read and redirect to target URL if available.
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $targetUrl = $notification->data['url'] ?? null;

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'target_url' => $targetUrl,
                'unread_count' => auth()->user()->unreadNotifications()->count(),
            ]);
        }

        if ($targetUrl && $targetUrl !== '#' && !str_starts_with($targetUrl, 'javascript:')) {
            return redirect($targetUrl);
        }

        return back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    /**
     * Mark all unread notifications for current user as read.
     */
    public function markAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
                'message' => 'Semua notifikasi telah ditandai dibaca.'
            ]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(string $id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Provide JSON payload for AJAX / Alpine.js dropdown.
     */
    public function dropdownData()
    {
        $user = auth()->user();
        $unreadCount = $user->unreadNotifications()->count();
        $recentNotifications = $user->notifications()
            ->latest()
            ->take(6)
            ->get()
            ->map(function (DatabaseNotification $n) {
                return [
                    'id'         => $n->id,
                    'title'      => $n->data['title'] ?? 'Notifikasi',
                    'message'    => $n->data['message'] ?? '',
                    'url'        => route('notifications.read', $n->id),
                    'category'   => $n->data['category'] ?? 'system',
                    'icon'       => $n->data['icon'] ?? 'bell',
                    'is_read'    => !is_null($n->read_at),
                    'time_ago'   => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $recentNotifications,
        ]);
    }
}
