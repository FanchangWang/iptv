<?php
declare(strict_types=1);

namespace Src\Cmvideo\Driver;


class CdnDriver extends CmvideoDriver
{

    /**
     * @inheritDoc
     */
    public function getM3u8Array(): array
    {
        return [
            'http://gslbserv.itv.cmvideo.cn/index.m3u8?channel-id=ystenlive&Contentid=1000000001000018602&livemode=1&stbId=4'
        ];
    }
}