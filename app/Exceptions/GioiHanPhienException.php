<?php

namespace App\Exceptions;

use RuntimeException;

class GioiHanPhienException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Tài khoản đã đạt giới hạn 2 phiên đăng nhập');
    }
}
