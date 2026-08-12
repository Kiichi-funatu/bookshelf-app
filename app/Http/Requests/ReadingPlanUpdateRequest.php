<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\ReadingPlanStatus;

class ReadingPlanUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'target_date' => ['required', 'date', 'after_or_equal:today'],

            // ★ 自身を除外した重複チェック
            'book_id' => [
                'required',
                'integer',
                Rule::unique('reading_plans')
                    ->ignore($planId) // ← 自分自身を除外
                    ->where(function ($query) {
                        return $query->where('user_id', $this->user()->id)
                                     ->where('status', ReadingPlanStatus::in_progress->value);
                    }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'target_date.required'        => '期日は必須です。',
            'target_date.date'            => '期日は有効な日付形式で入力してください。',
            'target_date.after_or_equal'  => '期日は今日以降の日付を指定してください。',

            'book_id.required'            => '書籍を選択してください。',
            'book_id.integer'             => '書籍IDは整数で入力してください。',
            'book_id.unique'              => 'この書籍は既に進行中の読書計画が存在します。',
        ];
    }
}
