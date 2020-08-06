<?php
declare(strict_types=1);

namespace Tests;

use Src\Cmvideo\Cmvideo;

class CmvideoTvTest extends InitTest
{
    /**
     * 测试 cm video tv
     */
    public function testDrivers()
    {
        $tv = new Cmvideo();
        $this->assertIsArray($tv->check());
    }
}