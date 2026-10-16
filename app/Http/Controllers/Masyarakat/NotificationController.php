<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Menampilkan notifikasi milik masyarakat yang login.
     */
    public function index()
    {
        $userId = auth()->id();

        $notifications = Notification::where('user_id', $userId)
            ->latest()
            ->paginate(15);

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return Inertia::render('Masyarakat/Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markRead($id)
    {
        $notification = Notification::where(
            'user_id',
            auth()->id()
        )->findOrFail($id);

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
            ]);
        }

        return back()->with(
            'success',
            'Notifikasi berhasil ditandai sudah dibaca.'
        );
    }

    /**
     * Menandai seluruh notifikasi sebagai sudah dibaca.
     */
    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return back()->with(
            'success',
            'Semua notifikasi berhasil ditandai sudah dibaca.'
        );
    }
}
