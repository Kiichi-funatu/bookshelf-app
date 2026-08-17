<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\ReadingPlanStatus;


class ReadingPlanStoreRequest extends FormRequest
{
    /**
     * 認可（ログイン必須）
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * バリデーションルール
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'integer',
                'exists:books,id',
                Rule::unique('reading_plans')
                    ->where(function ($query) {
                        return $query->where('user_id', $this->user()->id)
                                    ->where('status', ReadingPlanStatus::in_progress->value);
                    }),
            ],
            'target_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * バリデーションメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'book_id.required'            => '書籍を選択してください。',
            'book_id.integer'             => '書籍IDは整数で入力してください。',
            'book_id.exists'              => '選択された書籍は存在しません。',
            'book_id.unique'              => 'この書籍は既に進行中の読書計画が存在します。',
            'target_date.required'        => '期日は必須です。',
            'target_date.date'            => '期日は有効な日付形式で入力してください。',
            'target_date.after_or_equal'  => '期日は今日以降の日付を指定してください。',
        ];
    }
}
