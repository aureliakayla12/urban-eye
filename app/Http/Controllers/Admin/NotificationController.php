<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Menampilkan notifikasi milik Admin yang sedang login.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $notifications = Notification::where('user_id', $userId)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return Inertia::render('Admin/Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markRead(Request $request, $id)
    {
        $notification = Notification::where(
            'user_id',
            $request->user()->id
        )->findOrFail($id);

        $notification->update([
            'is_read' => true,
        ]);

        return redirect()->route('admin.notifications.index');
    }

    /**
     * Menandai semua notifikasi Admin sebagai sudah dibaca.
     */
    public function markAllRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return redirect()->route('admin.notifications.index');
    }
}
