<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReadingPlanReminder extends Notification
{
    use Queueable;

    public string $timing;
    public string $title;
    public string $body;
    public int $planId;

    /**
     * コンストラクタ
     */
    public function __construct(string $timing, string $title, string $body, int $planId)
    {
        $this->timing = $timing;
        $this->title = $title;
        $this->body = $body;
        $this->planId = $planId;
    }

    /**
     * 通知チャネル（メールではなく database）
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * database 通知として保存される JSON
     * Blade が参照するキーと完全一致させる
     */
    public function toDatabase($notifiable): array
    {
        return [
            'timing' => $this->timing,   // three_days_before / on_due_date / three_days_after
            'title'  => $this->title,    // Blade のタイトル表示
            'body'   => $this->body,     // Blade の本文表示
            'plan_id' => $this->planId,  // 読書計画ID（必要なら詳細画面へ遷移できる）
        ];
    }
}
