<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

class InitTest extends TestCase
{
    protected function setUp(): void
    {
        !defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
    }
}