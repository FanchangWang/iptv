<?php
declare(strict_types=1);

namespace Src\Cmvideo;

use Src\AbstractTv;
use Src\Cmvideo\Driver\CdnDriver;
use Src\Constant\CmvideoConstant;
use Src\Constant\EpgConstant;

class Cmvideo extends AbstractTv
{
    /** @var string 历史 json 路径 */
    protected $historyJsonPath = CmvideoConstant::JSON_PATH;

    /** @var string 错误计数器路径 */
    protected $errCounterPath = CmvideoConstant::ERR_COUNTER_PATH;

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $drivers = [
            CdnDriver::class
        ];
        $this->setDrivers($drivers);
    }

    /**
     * 由于循环检测时易出现 curl connect timeout，且 url 变更可能性不大，暂时忽略检测
     *
     * @inheritDoc
     */
    protected function checkM3u8Url(string $url): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    function getTvM3uContent(string $url, string $groupPrefix = ''): string
    {
        $tvList = require(BASE_PATH . CmvideoConstant::TV_LIST);

        $epgList = require(BASE_PATH . EpgConstant::EPG_LIST);
        $epgTvgNameList = array_column($epgList, 'tvg-name');

        $content = '';
        foreach ($tvList as $tv) {
            $idx = array_search($tv['tvg-name'], $epgTvgNameList);
            $tvg = ($idx !== false) ? array_merge($tv, $epgList[$idx]) : $tv;

            if (!isset($tvg['group-title']) || !in_array($tvg['group-title'], ['央视', '卫视', 'NewTV'])) {
                $tvg['group-title'] = '卫视';
            }

            if ($groupPrefix) {
                $tvg['group-title'] = $groupPrefix . '·' . $tvg['group-title'];
            }

            $content .= $this->getM3uLine(
                $tvg['name'] ?? $tvg['tvg-name'],
                $tvg['url'],
                $tvg['group-title'] ?? '',
                $tvg['tvg-name'] ?? '',
                $tvg['tvg-id'] ?? '',
                $tvg['tvg-logo'] ?? ''
            );
        }
        return $content;
    }
}