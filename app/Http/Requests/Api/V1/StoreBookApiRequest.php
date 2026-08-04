<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'        => ['required', 'integer', 'exists:users,id'],
            'title'          => ['required', 'string', 'max:255'],
            'author'         => ['required', 'string', 'max:255'],
            'isbn'           => ['required', 'string', 'size:13', 'unique:books,isbn'],
            'published_date' => ['required', 'date'],
            'description'    => ['nullable', 'string'],
            'image_url'      => ['nullable', 'url', 'max:255'],
            'genres'         => ['required', 'array', 'min:1'],
            'genres.*'       => ['integer', 'exists:genres,id'],
            ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'        => '登録者IDは必須です。',
            'user_id.exists'          => '指定された登録者は存在しません。',
            'title.required'          => 'タイトルは必須です。',
            'author.required'         => '著者名は必須です。',
            'isbn.required'           => 'ISBNは必須です。',
            'isbn.size'               => 'ISBNは13桁で入力してください。',
            'isbn.unique'             => 'そのISBNは既に使用されています。',
            'published_date.required' => '出版日は必須です。',
            'genres.required'         => 'ジャンルは1つ以上選択してください。',
            'genres.min'              => 'ジャンルは1つ以上選択してください。',
            'genres.*.exists'         => '指定されたジャンルが存在しません。',
        ];
    }
}
