<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\ReadingPlan;
use App\Models\Book;
use App\Http\Requests\ReadingPlanStoreRequest;
use App\Http\Requests\ReadingPlanUpdateRequest;
use App\Enums\ReadingPlanStatus;

class ReadingPlanController extends Controller
{
    /**
     * 読書計画一覧（検索・フィルタ・ソート）
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Blade が使う検索条件
        $currentStatus = $request->input('status');     // active / completed / expired
        $keyword       = $request->input('keyword');    // 書籍タイトル検索
        $sort          = $request->input('sort', 'due_date_asc'); // デフォルト

        // 自分の計画のみ
        $query = ReadingPlan::query()
            ->where('user_id', auth()->id())
            ->with('book');

        // 状態フィルタ
        if ($currentStatus === ReadingPlanStatus::Planned->value) {
            $query->where('status', ReadingPlanStatus::Planned);
        } elseif ($currentStatus === ReadingPlanStatus::Completed->value) {
            $query->where('status', ReadingPlanStatus::Completed);
        } elseif ($currentStatus === ReadingPlanStatus::Expired->value) {
            $query->where('status', ReadingPlanStatus::Expired);
        }

        // キーワード検索（書籍タイトル）
        if (!empty($keyword)) {
            $query->whereHas('book', function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%");
            });
        }

        // ソート
        switch ($sort) {
            case 'due_date_desc':
                $query->orderBy('due_date', 'desc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('due_date', 'asc'); // due_date_asc
        }

        // ページネーション（検索条件を引き継ぐ）
        $readingPlans = $query->paginate(10)->appends($request->query());

        return view('reading-plans.index', compact(
            'readingPlans',
            'currentStatus',
            'keyword',
            'sort'
        ));
    }

    /**
     * 読書計画作成画面
     *
     * @return View
     */
    public function create(): View
    {
        $books = Book::orderBy('title')->get();

        return view('reading-plans.create', compact('books'));
    }

    /**
     * 読書計画登録
     *
     * @param ReadingPlanStoreRequest $request
     * @return RedirectResponse
     */
    public function store(ReadingPlanStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        ReadingPlan::create([
            'user_id' => auth()->id(),
            'book_id' => $validated['book_id'],
            'due_date' => $validated['target_date'],
            'status' => ReadingPlanStatus::Planned,
        ]);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を登録しました。');
    }

    /**
     * 読書計画編集画面
     *
     * @param ReadingPlan $plan
     * @return View
     */
    public function edit(ReadingPlan $plan): View
    {
        $this->authorize('update', $plan);

        return view('reading-plans.edit', ['readingPlan' => $plan]);
    }

    /**
     * 読書計画更新
     *
     * @param ReadingPlanUpdateRequest $request
     * @param ReadingPlan $plan
     * @return RedirectResponse
     */
    public function update(ReadingPlanUpdateRequest $request, ReadingPlan $plan): RedirectResponse
    {
        $this->authorize('update', $plan);

        $validated = $request->validated();

        $plan->update([
            'due_date' => $validated['target_date'],
        ]);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を更新しました。');
    }

    /**
     * 読書計画削除
     *
     * @param ReadingPlan $plan
     * @return RedirectResponse
     */
    public function destroy(ReadingPlan $plan): RedirectResponse
    {
        $this->authorize('delete', $plan);

        $plan->delete();

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を削除しました。');
    }

    /**
     * 読了状態に更新
     *
     * @param ReadingPlan $plan
     * @return RedirectResponse
     */
    public function complete(ReadingPlan $plan): RedirectResponse
    {
        $this->authorize('complete', $plan);

        $plan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('reading-plans.index')
            ->with('success', '読書計画を読了状態にしました。');
    }
}