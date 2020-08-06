<?php
declare(strict_types=1);

namespace Tests;

use Src\AbstractDriver;
use Src\Cmvideo\Driver\CdnDriver;

class CmvideoDriverTest extends InitTest
{
    /**
     * 测试全部驱动
     */
    public function testDrivers()
    {
        $drivers = [
            CdnDriver::class
        ];
        foreach ($drivers as $driverClassName) {
            /** @var AbstractDriver $driver */
            $driver = new $driverClassName();
            $result = $driver->getM3u8Array();
            $this->assertIsArray($result);
        }
    }
}