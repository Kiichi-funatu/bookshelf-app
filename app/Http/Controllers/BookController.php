<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\ReviewLike;

class BookController extends Controller
{
    // 書籍一覧（検索・絞り込み・ソート・ページネーション）
    public function index(Request $request)
    {
        // keyword, genre, sort を使った検索処理を書く
        $books = Book::with(['genres'])
        ->withAvg('reviews', 'rating')
        ->orderBy('published_date', 'desc')
        ->paginate(10);

    return view('books.index', compact('books'));
    }

    // 書籍詳細
    public function show(Book $book)
    {
        // 書籍に紐づくレビューを最新順で取得
        $reviews = $book->reviews()
            ->with('user')               // レビュー投稿者
            ->orderBy('created_at', 'desc')
            ->get();

        // 書籍に紐づくジャンル
        $genres = $book->genres;

        // ログインユーザーがお気に入り済みか
        $isFavorite = false;
        
        if (auth()->check()) {
            $isFavorite = $book->favorites()
                ->where('user_id', auth()->id())
                ->exists();
        }

        // ログインユーザーがいいね済みか
        $isLiked = false;

        if (auth()->check()) {
            $isLiked = ReviewLike::where('user_id', auth()->id())
                ->whereIn('review_id', $book->reviews->pluck('id'))
                ->exists();
        }

        return view('books.show', compact(
            'book',
            'reviews',
            'genres',
            'isFavorite',
            'isLiked'
        ));
    }

    // 書籍登録画面
    public function create()
    {
        // ジャンル一覧を渡す
    }

    // 書籍登録
    public function store(Request $request)
    {
        // バリデーション → 書籍作成 → ジャンル紐付け
    }

    // 書籍編集画面
    public function edit(Book $book)
    {
        // 認可 → 編集フォーム表示
    }

    // 書籍更新
    public function update(Request $request, Book $book)
    {
        // 認可 → バリデーション → 更新 → ジャンル紐付け更新
    }

    // 書籍削除
    public function destroy(Book $book)
    {
        // 認可 → 書籍削除 → 関連データ削除
    }

    // ISBN検索（Google Books API）
    public function isbnLookup($isbn)
    {
        // API 呼び出し → JSON返却 or フォーム初期値
    }

    // ランキング（レビュー平均TOP10）
    public function ranking()
    {
        // 平均評価の高い順に10件（仮）
        $books = Book::withAvg('reviews', 'rating')
        ->orderBy('reviews_avg_rating', 'desc')
        ->take(10)
        ->get();

    return view('books.ranking', compact('books'));
    }
}
