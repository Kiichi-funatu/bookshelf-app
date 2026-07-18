<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Book;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($books as $book) {
            $reviewCount = rand(2, 4);

            for ($i = 0; $i < $reviewCount; $i++) {
                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $users->random()->id,
                    'rating' => rand(3, 5),
                    'comment' => $book->title . ' に対するレビューコメント ' . ($i + 1),
                ]);
            }
        }
    }
}
