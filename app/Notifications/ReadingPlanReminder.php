<?php

namespace App\Notifications;

use App\Models\ReadingPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReadingPlanReminder extends Notification
{
    use Queueable;

    /**
     * @var ReadingPlan
     */
    private ReadingPlan $plan;

    /**
     * @var string three_days_before | on_due_date | three_days_after
     */
    private string $timing;

    /**
     * コンストラクタ
     *
     * @param ReadingPlan $plan
     * @param string $timing
     */
    public function __construct(ReadingPlan $plan, string $timing)
    {
        $this->plan   = $plan;
        $this->timing = $timing;
    }

    /**
     * 通知チャネル（database）
     *
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * database 通知として保存される JSON
     *
     * @param mixed $notifiable
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable): array
    {
        return [
            'timing'  => $this->timing,
            'title'   => $this->getTitle(),
            'body'    => $this->getBody(),
            'plan_id' => $this->plan->id,
        ];
    }

    /**
     * タイトル生成
     *
     * @return string
     */
    private function getTitle(): string
    {
        return match ($this->timing) {
            'three_days_before' => '読書計画の期限が近づいています',
            'on_due_date'       => '読書計画の期限日です',
            'three_days_after'  => '読書計画の期限を過ぎています',
            default             => '読書計画のお知らせ',
        };
    }

    /**
     * 本文生成
     *
     * @return string
     */
    private function getBody(): string
    {
        $title = $this->plan->book->title;

        return match ($this->timing) {
            'three_days_before' => "「{$title}」の期限はあと3日です。",
            'on_due_date'       => "「{$title}」の期限は今日です。",
            'three_days_after'  => "「{$title}」の期限から3日が経過しました。",
            default             => "「{$title}」に関する読書計画のお知らせです。",
        };
    }
}
