<?php
declare (strict_types=1);

namespace app\index\controller;

use think\support\facade\Cipher;

class CipherController
{
    public function index(): void
    {
        $encryptText = Cipher::encrypt([
            'name' => 'kain'
        ]);
        dump($encryptText);
        $value = Cipher::decrypt($encryptText);
        dump($value);
    }
}