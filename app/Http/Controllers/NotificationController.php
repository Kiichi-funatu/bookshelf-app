<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * 通知一覧を表示する
     *
     * @return View
     */
    public function index(): View
    {
        // ログインユーザーの通知一覧
        $notifications = auth()->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * 通知を既読にする
     *
     * @param string $id 通知ID
     * @return RedirectResponse
     */
    public function read(string $id): RedirectResponse
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return redirect()
            ->route('notifications.index')
            ->with('success', '通知を既読にしました。');
    }
}
