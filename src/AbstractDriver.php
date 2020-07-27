<?php
declare(strict_types=1);

namespace Src;


/**
 * Class AbstractDriver
 *
 * @package Src
 */
abstract class AbstractDriver
{
    /**
     * 获取 m3u8 链接
     *
     * @return array
     */
    abstract public function getM3u8Array(): array;
}