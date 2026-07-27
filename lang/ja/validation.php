<?php

return [

    // 共通ルール
    'required' => ':attributeを入力してください。',
    'string'   => ':attributeは文字列で入力してください。',
    'max'      => [
        'string' => ':attributeは:max文字以内で入力してください。',
    ],
    'min' => [
        'string' => ':attributeは:min文字以上で入力してください。',
    ],
    'email' => ':attributeはメール形式で入力してください。',
    'unique' => 'その:attributeは既に使用されています。',
    'confirmed' => ':attributeと一致しません。',

    // フィールド名の日本語化
    'attributes' => [
        'email'                 => 'メールアドレス',
        'password'              => 'パスワード',
        'password_confirmation' => '確認用パスワード',
        'name'                  => 'お名前',
    ],

    // 個別フィールドの専用メッセージ
    'custom' => [
        'name' => [
            'required' => 'お名前を入力してください。',
        ],
        'email' => [
            'unique' => 'そのメールアドレスは既に使用されています。',
        ],
    ],

];
