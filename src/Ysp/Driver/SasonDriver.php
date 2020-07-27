<?php
declare(strict_types=1);

namespace Src\Ysp\Driver;

/**
 * Class SasonDriver
 *
 * @package Src\Ysp\Driver
 */
class SasonDriver extends YspDriver
{
    /** @var string */
    protected $uri = 'http://tv.sason.xyz/new.m3u';
}