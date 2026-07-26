<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class RankingController extends Controller
{
    public function index()
    {
        // レビュー平均評価のTOP10（レビューがある書籍のみ）
        $rankedBooks = Book::withAvg('reviews', 'rating')
            ->whereHas('reviews') // レビューがある書籍のみ
            ->orderByDesc('reviews_avg_rating') // 平均評価の降順
            ->take(10)
            ->get();

        return view('ranking.index', compact('rankedBooks'));
    }
}
