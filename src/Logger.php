<?php
declare(strict_types=1);

namespace Src;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MonoLogger;
use Monolog\Processor\HostnameProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Src\Constant\LogConstant;

class Logger
{

    /** @var MonoLogger[] */
    private static $monoLogger = [];

    /**
     * get monolog logger
     *
     * @param string $name monolog logger channel name
     * @return MonoLogger|null
     */
    public static function getMonoLogger(string $name = 'default')
    {

        if (!isset($name, static::$monoLogger)) {
            return static::$monoLogger[$name];
        }

        $handler = new RotatingFileHandler(BASE_PATH . LogConstant::LOG_PATH);

        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] %extra% %channel%.%level_name%: %message% %context%\n";
        $formatter = new LineFormatter($output, $dateFormat);

        $handler->setFormatter($formatter);

        $handlers[] = $handler;

        $processors[] = new HostnameProcessor();
        $processors[] = new ProcessIdProcessor();
        $processors[] = new MemoryUsageProcessor();

        $logger = new MonoLogger($name, $handlers, $processors);
        static::$monoLogger[$name] = $logger;
        return $logger;
    }
}