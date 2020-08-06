<?php
declare(strict_types=1);

namespace Tests;

use Src\Ysp\Ysp;

class YspTvTest extends InitTest
{
    /**
     * 测试 ysp tv
     */
    public function testDrivers()
    {
        $tv = new Ysp();
        $this->assertIsArray($tv->check());
    }
}