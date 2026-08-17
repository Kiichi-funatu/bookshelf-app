<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    /**
     * レビュー編集の認可判定
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string'],
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
            'rating.required' => '評価は必須です。',
            'rating.integer' => '評価は数値で入力してください。',
            'rating.min' => '評価は1以上で入力してください。',
            'rating.max' => '評価は5以下で入力してください。',
            'comment.required' => 'レビュー本文は必須です。',
            'comment.string' => 'レビュー本文は文字列で入力してください。',
        ];
    }
}
