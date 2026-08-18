<?php

namespace App\Console\Commands;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use App\Notifications\ReadingPlanReminder;
use Illuminate\Console\Command;

class RunReadingPlansDaily extends Command
{
    /**
     * コンソールコマンドの名前（Artisan での呼び出し名）
     *
     * @var string
     */
    protected $signature = 'reading-plans:run-daily';

    /**
     * コンソールコマンドの説明
     *
     * @var string
     */
    protected $description = '読書計画の期限チェックとステータス自動更新を行うバッチ';

    /**
     * コマンドの実行処理
     *
     * @return int
     */
    public function handle(): int
    {
        $today = now()->startOfDay();

        // 1. 期限3日前（in_progress のみ通知）
        $threeDaysBefore = $today->copy()->addDays(3);

        $plans = ReadingPlan::whereDate('due_date', $threeDaysBefore)
            ->where('status', ReadingPlanStatus::IN_PROGRESS)
            ->get();

        foreach ($plans as $plan) {
            $plan->user->notify(
                new ReadingPlanReminder($plan, 'three_days_before')
            );
        }

        // 2. 当日（in_progress のみ通知）
        $plans = ReadingPlan::whereDate('due_date', $today)
            ->where('status', ReadingPlanStatus::IN_PROGRESS)
            ->get();

        foreach ($plans as $plan) {
            $plan->user->notify(
                new ReadingPlanReminder($plan, 'on_due_date')
            );
        }

        // 3. 期限3日後（expired のみ通知）
        $threeDaysAfter = $today->copy()->subDays(3);

        $plans = ReadingPlan::whereDate('due_date', $threeDaysAfter)
            ->where('status', ReadingPlanStatus::EXPIRED)
            ->get();

        foreach ($plans as $plan) {
            $plan->user->notify(
                new ReadingPlanReminder($plan, 'three_days_after')
            );
        }

        // 4. 期限切れステータス自動更新（transaction 不要）
        ReadingPlan::whereDate('due_date', '<', $today)
            ->whereNull('completed_at')
            ->where('status', ReadingPlanStatus::IN_PROGRESS)
            ->update([
                'status' => ReadingPlanStatus::EXPIRED,
            ]);

        return Command::SUCCESS;
    }
}
