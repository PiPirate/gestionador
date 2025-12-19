<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        $user = $request->user();
        $user?->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'Notificaciones marcadas como leídas.');
    }
}
