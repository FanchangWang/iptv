<?php
declare(strict_types=1);

namespace Src\ChinaMobile\Driver;


class BeijingDriver extends ChinaMobileDriver
{

    /**
     * @inheritDoc
     */
    public function getM3u8Array(): array
    {
        return [
            'http://otttv.bj.chinamobile.com/PLTV/88888888/224/3221226226/1.m3u8'
        ];
    }
}