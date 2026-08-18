<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * アプリケーションのスケジュール定義
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // 読書計画のバッチ処理（毎日実行）
        $schedule->command('reading-plans:run-daily')->everyMinute();/*本番は->daily();*/
    }

    /**
     * アプリケーションのコンソールコマンド登録
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
