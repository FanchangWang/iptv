<?php
declare(strict_types=1);

namespace Tests;

use Src\Logger;

class LoggerTest extends InitTest
{
    /**
     * 测试日志引擎
     */
    public function testLogger()
    {
        $logger = Logger::getMonoLogger();
        $this->assertTrue($logger instanceof \Monolog\Logger);
    }
}