<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookApiRequest extends FormRequest
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
            'title'          => ['required', 'string', 'max:255'],
            'author'         => ['required', 'string', 'max:255'],
            'isbn'           => [
                'required',
                'string',
                'max:20',
                Rule::unique('books', 'isbn')->ignore($this->route('book')),
            ],
            'published_date' => ['nullable', 'date'],
            'genre_ids'      => ['required', 'array'],
            'genre_ids.*'    => ['integer', 'exists:genres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'タイトルは必須です。',
            'author.required'         => '著者名は必須です。',
            'isbn.required'           => 'ISBNは必須です。',
            'isbn.unique'             => 'このISBNは既に登録されています。',
            'genre_ids.required'      => 'ジャンルは1つ以上選択してください。',
            'genre_ids.*.exists'      => '指定されたジャンルが存在しません。',
        ];
    }
}
