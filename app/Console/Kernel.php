<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 読書計画の期限チェック（毎日実行）
        $schedule->call(function () {

            $today = now()->startOfDay();

            // 期限3日前
            $threeDaysBefore = $today->copy()->addDays(3);
            $plans = \App\Models\ReadingPlan::whereDate('due_date', $threeDaysBefore)->get();

            foreach ($plans as $plan) {
                $plan->user->notify(new \App\Notifications\ReadingPlanReminder(
                    timing: 'three_days_before',
                    title: '読書計画の期限が近づいています',
                    body: "「{$plan->book->title}」の期限はあと3日です。",
                    planId: $plan->id
                ));
            }

            // 当日
            $plans = \App\Models\ReadingPlan::whereDate('due_date', $today)->get();

            foreach ($plans as $plan) {
                $plan->user->notify(new \App\Notifications\ReadingPlanReminder(
                    timing: 'on_due_date',
                    title: '読書計画の期限日です',
                    body: "「{$plan->book->title}」の期限は今日です。",
                    planId: $plan->id
                ));
            }

            // 期限3日後
            $threeDaysAfter = $today->copy()->subDays(3);
            $plans = \App\Models\ReadingPlan::whereDate('due_date', $threeDaysAfter)->get();

            foreach ($plans as $plan) {
                $plan->user->notify(new \App\Notifications\ReadingPlanReminder(
                    timing: 'three_days_after',
                    title: '読書計画の期限を過ぎています',
                    body: "「{$plan->book->title}」の期限から3日が経過しました。",
                    planId: $plan->id
                ));
            }

        })->everyMinute();//(本番環境)->daily();

        //（期限切れステータス自動更新）
        $schedule->call(function () {

            $today = now()->startOfDay();

            \DB::transaction(function () use ($today) {

                \App\Models\ReadingPlan::whereDate('due_date', '<', $today)
                    ->whereNull('completed_at')
                    ->update([
                        'status' => 'expired',
                    ]);
            });

        })->everyMinute(); // 本番は ->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
