<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Contracts\View\View;


class RankingController extends Controller
{
    /**
     * レビュー平均評価のランキング（TOP10）を表示する
     *
     * @return View
     */
    public function index(): View
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
