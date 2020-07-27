<?php
declare(strict_types=1);

/**
 * 公共函数
 */

use Src\Constant\EpgConstant;
use Src\Logger;

/**
 * get monolog\logger
 *
 * @param string $name
 * @return \Monolog\Logger|null
 */
function logger(string $name = 'default')
{
    return Logger::getMonoLogger($name);
}

/**
 * get throwable error log
 *
 * @param Throwable $e
 * @return array
 */
function get_throwable_error_log(\Throwable $e): array
{
    return [
        'code' => $e->getCode(),
        'file' => $e->getFile() . ':' . $e->getLine(),
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ];
}

/**
 * 生成 m3u 头
 */
function get_m3u_head(): string
{
    return sprintf('#EXTM3U url-tvg="%s"' . PHP_EOL, EpgConstant::EPG_URL);
}