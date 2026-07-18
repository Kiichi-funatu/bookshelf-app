<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReadingPlan;

class ReadingPlanController extends Controller
{
    // 読書計画一覧
    public function index(Request $request)
    {
        // status 絞り込み
    }

    // 読書計画作成画面
    public function create()
    {
        // 書籍一覧を渡す
    }

    // 読書計画登録
    public function store(Request $request)
    {
        // バリデーション → 登録
    }

    // 読書計画編集画面
    public function edit(ReadingPlan $plan)
    {
        // 認可 → 編集フォーム表示
    }

    // 読書計画更新
    public function update(Request $request, ReadingPlan $plan)
    {
        // 認可 → バリデーション → 更新
    }

    // 読書計画削除
    public function destroy(ReadingPlan $plan)
    {
        // 認可 → 削除
    }

    // 読了ボタン
    public function complete(ReadingPlan $plan)
    {
        // 認可 → ステータス更新
    }
}
