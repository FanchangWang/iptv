<?php
declare(strict_types=1);

namespace Src\Constant;

/**
 * Class YspConstant
 *
 * @package Src\Constant
 */
class YspConstant
{
    /** @var string 历史执行 json 记录 */
    public const JSON_PATH = '/runtime/history/ysp.json';

    /** @var string 生成的直播源文件位置 */
    public const M3U_PATH = '/dist/ysp.m3u';

    /** @var string 节目列表 */
    public const TV_LIST = '/data/ysp.php';
}