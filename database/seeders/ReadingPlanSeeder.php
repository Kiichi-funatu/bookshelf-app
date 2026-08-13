<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Models\Book;
use Carbon\Carbon;

class ReadingPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        // 主要シナリオ用ユーザー（山田太郎）
        $mainUser = User::where('name', '山田太郎')->first() ?? $users->first();

        foreach ($books as $book) {

            // planned（期限は未来）
            ReadingPlan::create([
                'user_id' => $mainUser->id,
                'book_id' => $book->id,
                'due_date' => Carbon::today()->addDays(rand(3, 10)),
                'status' => 'planned',
            ]);

            // in_progress（期限は近い未来）
            ReadingPlan::create([
                'user_id' => $mainUser->id,
                'book_id' => $book->id,
                'due_date' => Carbon::today()->addDays(rand(1, 5)),
                'status' => 'in_progress',
            ]);

            // completed（期限は過去、completed_at あり）
            ReadingPlan::create([
                'user_id' => $mainUser->id,
                'book_id' => $book->id,
                'due_date' => Carbon::today()->subDays(rand(5, 15)),
                'status' => 'completed',
                'completed_at' => Carbon::today()->subDays(rand(1, 10)),
            ]);

            // expired（期限切れ、completed_at なし）
            ReadingPlan::create([
                'user_id' => $mainUser->id,
                'book_id' => $book->id,
                'due_date' => Carbon::today()->subDays(rand(1, 5)),
                'status' => 'expired',
                'completed_at' => null,
            ]);

            // 認可判定用：別ユーザーの計画も少し混ぜる
            ReadingPlan::create([
                'user_id' => $users->random()->id,
                'book_id' => $book->id,
                'due_date' => Carbon::today()->addDays(rand(2, 7)),
                'status' => 'planned',
            ]);

            // ===== 通知テスト用データ =====

            // 今日が期限（on_due_date）
            ReadingPlan::create([
                'user_id' => $mainUser->id,
                'book_id' => $books->first()->id,
                'due_date' => Carbon::today(),
                'status' => 'in_progress',
            ]);

            // 3日前が期限（three_days_before）
            ReadingPlan::create([
                'user_id' => $mainUser->id,
                'book_id' => $books->get(1)->id,
                'due_date' => Carbon::today()->addDays(3),
                'status' => 'planned',
            ]);

            // 3日後が期限（three_days_after）
            ReadingPlan::create([
                'user_id' => $mainUser->id,
                'book_id' => $books->get(2)->id,
                'due_date' => Carbon::today()->subDays(3),
                'status' => 'expired',
            ]);

        }
    }
}
