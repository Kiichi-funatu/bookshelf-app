<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBooksRequest extends FormRequest
{
    /**
     * 公開画面のため認可は常に true
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // 認証不要（公開画面）
    }

    /**
     * バリデーションルール
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'genre'   => ['nullable', 'integer', 'exists:genres,id'],
            //'sort'    => ['nullable', 'string', 'in:newest,oldest,rating,title'],
            'sort'    => ['nullable', 'string', 'in:latest,oldest,rating,title'],
            'page'    => ['nullable', 'integer', 'min:1'],
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
            'keyword.string' => 'キーワードは文字列で入力してください。',
            'keyword.max' => 'キーワードは255文字以内で入力してください。',
            'genre.integer' => 'ジャンルIDは整数で入力してください。',
            'genre.exists' => '指定されたジャンルは存在しません。',
            'sort.in' => '並び順の指定が不正です。',
            'page.integer' => 'ページ番号は整数で入力してください。',
            'page.min' => 'ページ番号は1以上を指定してください。',
        ];
    }
}
