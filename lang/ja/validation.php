<?php
// lang/ja/validation.php

return [
    'required' => ':attributeを入力してください。',
    'string'   => ':attributeは文字列で入力してください。',
    'max'      => [
        'string' => ':attributeは:max文字以内で入力してください。',
    ],
    'unique' => ':attributeは既に使用されています。',

    // フィールドごとのメッセージ
    'attributes' => [
        'email'    => 'メールアドレス',
        'password' => 'パスワード',
        'name'     => '名前',
    ],

];