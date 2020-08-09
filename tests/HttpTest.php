<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Exception;
use Psr\Http\Message\ResponseInterface;
use Src\Http;

class HttpTest extends InitTest
{
    /**
     * 测试 m3u8 url
     *
     * @param string $url
     */
    public function testMu38Url(
        string $url = 'http://gslbserv.itv.cmvideo.cn/index.m3u8?channel-id=ystenlive&Contentid=1000000001000018602&livemode=1&stbId=4'
    ) {
        try {
            echo $url;
            $response = (new Http())
                ->setUri($url)
                ->setTimeout(5)
                ->setConnectTimeout(5)
                ->getResponse();
            $this->assertInstanceOf(ResponseInterface::class, $response);

            $code = $response->getStatusCode();
            $this->assertEquals($code, 200);

            $body = $response->getBody()->__toString();
            $this->assertStringContainsString('EXTM3U', $body);
            echo ' assert success' . PHP_EOL;
        } catch (Exception $e) {
            echo sprintf(' assert exception. file:%s msg:%s trace:%s' . PHP_EOL,
                $e->getFile() . ':' . $e->getCode(),
                $e->getMessage(),
                $e->getTraceAsString());
        } finally {
            echo PHP_EOL . '###################################' . PHP_EOL;
        }
    }
}