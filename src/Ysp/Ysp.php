<?php
declare(strict_types=1);

namespace Src\Ysp;

use Src\AbstractTv;
use Src\Constant\EpgConstant;
use Src\Constant\YspConstant;
use Src\Ysp\Driver\Ajh102026Driver;
use Src\Ysp\Driver\Dyanj311Driver;
use Src\Ysp\Driver\Kkfong820033Driver;
use Src\Ysp\Driver\QwerttvvDriver;
use Src\Ysp\Driver\SasonDriver;
use Src\Ysp\Driver\YuechanDriver;
use Src\Ysp\Driver\Zhxch3Driver;

class Ysp extends AbstractTv
{
    /** @var string 历史 json 路径 */
    protected $historyJsonPath = YspConstant::JSON_PATH;

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $drivers = [
            SasonDriver::class,
            YuechanDriver::class,
            QwerttvvDriver::class,
            Zhxch3Driver::class,
            Dyanj311Driver::class,
            Ajh102026Driver::class,
            Kkfong820033Driver::class
        ];
        $this->setDrivers($drivers);
    }

    /**
     * @inheritDoc
     */
    protected function checkM3u8Url(string $uri): bool
    {
        // 用于检测的央视频 vid
        $ids = [
            2000210103,
            2000296203,
        ];
        foreach ($ids as $id) {
            $url = $uri . $id . '.m3u8';
            if (!parent::checkM3u8Url($url)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    function getTvM3uContent(string $url, string $groupPrefix = ''): string
    {
        $tvList = require(BASE_PATH . YspConstant::TV_LIST);

        $epgList = require(BASE_PATH . EpgConstant::EPG_LIST);
        $epgTvgNameList = array_column($epgList, 'tvg-name');

        $content = '';
        foreach ($tvList as $tv) {
            $idx = array_search($tv['tvg-name'], $epgTvgNameList);
            $tvg = array_merge($tv, $epgList[$idx]);

            if ($groupPrefix) {
                $tvg['group-title'] = $groupPrefix . '·' . $tvg['group-title'];
            }

            $m3u8Url = $url . $tvg['vid'] . '.m3u8';

            $content .= $this->getM3uLine(
                $tvg['name'],
                $m3u8Url,
                $tvg['group-title'],
                $tvg['tvg-name'],
                $tvg['tvg-id'],
                $tvg['tvg-logo']
            );
        }
        return $content;
    }
}