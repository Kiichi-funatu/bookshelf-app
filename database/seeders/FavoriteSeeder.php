<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $user) {
            $favoriteBooks = $books->random(rand(3, 5));

            foreach ($favoriteBooks as $book) {
                $user->favorites()->syncWithoutDetaching([$book->id]);
            }
        }
    }
}
