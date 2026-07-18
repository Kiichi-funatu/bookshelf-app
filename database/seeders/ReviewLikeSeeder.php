<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Review;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $reviews = Review::all();

        foreach ($reviews as $review) {
            $likeUsers = $users->where('id', '!=', $review->user_id)
                               ->random(rand(0, 3));

            foreach ($likeUsers as $user) {
                $review->likes()->syncWithoutDetaching([$user->id]);
            }
        }
    }
}
