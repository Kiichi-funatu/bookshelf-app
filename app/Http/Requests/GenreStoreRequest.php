<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenreStoreRequest extends FormRequest
{
    /**
     * リクエストの認可判定
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
            'name' => ['required', 'string', 'max:255', 'unique:genres,name'],
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
            'name.required' => 'ジャンル名は必須です。',
            'name.unique' => 'このジャンル名は既に登録されています。',
        ];
    }
}
