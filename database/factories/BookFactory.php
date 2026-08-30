<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            //'title' => $this->faker->sentence,
            //'author' => $this->faker->name,
            'title'          => 'テストタイトル',
            'author'         => 'テスト著者',
            'description' => $this->faker->paragraph,
            'user_id' => User::factory(), // 作成者
            'published_date' => $this->faker->date(),
            'created_at'     => now()->subDays(rand(0, 30)),
            'updated_at'     => now(),
        ];
    }
}
