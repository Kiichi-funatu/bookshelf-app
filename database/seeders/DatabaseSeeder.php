<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserSeeder::class,         // 1. ユーザー（5件）
            GenreSeeder::class,        // 2. ジャンル（10件）
            BookSeeder::class,         // 3. 書籍（11件 + ジャンル紐付け）
            ReviewSeeder::class,       // 4. レビュー（32件）
            FavoriteSeeder::class,     // 5. お気に入り（各ユーザー3〜5冊）
            ReviewLikeSeeder::class,   // 6. いいね（各レビュー0〜3人）
        ]);
    }
}
