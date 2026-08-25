<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            'comment' => $this->faker->paragraph,
            'rating' => $this->faker->numberBetween(1, 5),
            'book_id' => Book::factory(),
            'user_id' => User::factory(),
            'completed_at' => null,
        ];
    }
}
