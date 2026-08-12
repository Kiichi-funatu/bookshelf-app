<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Review;
use App\Models\Book;
use App\Models\Genre;

class ReportController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        // 基本統計
        $totalReviews = Review::where('user_id', $userId)->count();
        $booksRead = Review::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->distinct('book_id')
            ->count('book_id');

        $averageRating = Review::where('user_id', $userId)->avg('rating');

        // 評価分布（1〜5）
        $ratingDistribution = Review::where('user_id', $userId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        // ★ Blade が 1〜5 の配列を要求しているので整形
        $ratingDistribution = collect(range(1, 5))
            ->map(fn($r) => $ratingDistribution[$r] ?? 0);

        // 高評価書籍 TOP5
        $topRatedBooks = Review::where('user_id', $userId)
            ->selectRaw('book_id, AVG(rating) as avg_rating')
            ->groupBy('book_id')
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $book = Book::find($item->book_id);
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'rating' => (int) round($item->avg_rating),
                ];
            });

        // ジャンル別評価傾向 TOP5
        $genreRatings = Review::where('reviews.user_id', $userId)
            ->join('books', 'reviews.book_id', '=', 'books.id')
            ->join('book_genre', 'books.id', '=', 'book_genre.book_id')   // ★ 多対多の正しいJOIN
            ->join('genres', 'book_genre.genre_id', '=', 'genres.id')     // ★ 正しいJOIN
            ->selectRaw('genres.id, genres.name, COUNT(*) as count, AVG(reviews.rating) as avg_rating')
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'count' => $item->count,
                    'average_rating' => round($item->avg_rating, 1),
                ];
            });

        $stats = [
            'summary' => [
                'total_reviews' => $totalReviews,
                'books_read' => $booksRead,
                'average_rating' => $averageRating,
            ],
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreRatings,
        ];

        return view('reports.index', compact('stats'));
    }
}
