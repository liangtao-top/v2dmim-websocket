<?php
declare(strict_types=1);
// +----------------------------------------------------------------------
// | CodeEngine
// +----------------------------------------------------------------------
// | Copyright 艾邦
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: TaoGe <liangtao.gz@foxmail.com>
// +----------------------------------------------------------------------
// | Version: 2.0 2020/9/9 15:04
// +----------------------------------------------------------------------

namespace common;

/**
 * Class Base
 * @package app\controller
 */
abstract class Base
{
    private mixed $error = '';

    private mixed $result = '';

    public function setError(mixed $error): void
    {
        $this->error = $error;
    }

    public function getError(): mixed
    {
        return $this->error;
    }

    public function setResult(mixed $result): void
    {
        $this->result = $result;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }
}
