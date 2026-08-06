<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBooksRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // 認証不要（公開画面）
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'genre'   => ['nullable', 'integer', 'exists:genres,id'],
            'sort'    => ['nullable', 'string', 'in:newest,oldest,rating,title'],
            'page'    => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'keyword.string' => 'キーワードは文字列で入力してください。',
            'genre.integer' => 'ジャンルIDは整数で入力してください。',
            'genre.exists' => '指定されたジャンルは存在しません。',
            'sort.in' => '並び順の指定が不正です。',
            'page.integer' => 'ページ番号は整数で入力してください。',
            'page.min' => 'ページ番号は1以上を指定してください。',
        ];
    }
}
