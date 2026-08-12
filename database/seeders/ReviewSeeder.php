<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Book;
use Carbon\Carbon;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        // 評価別コメントテンプレート（応用版）
        $comments = [
            1 => 'あまり好みではありませんでした。',
            2 => '少し物足りない内容でした。',
            3 => '普通に楽しめました。',
            4 => 'とても良い本でした。',
            5 => '最高の一冊でした！',
        ];

        foreach ($books as $book) {

            // 各書籍に 2〜4 件のレビューを作成
            foreach (range(1, rand(2, 4)) as $i) {

                $rating = rand(1, 5); // ★ 応用版：1〜5 に拡大

                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $users->random()->id, // ★ 応用版：ランダムユーザー
                    'rating' => $rating,
                    'comment' => $comments[$rating],   // ★ 応用版：評価別コメント
                    'completed_at' => rand(0, 1)
                        ? Carbon::today()->subDays(rand(1, 120)) // ★ 応用版：読了日をランダム
                        : null,
                ]);
            }
        }
    }
}
