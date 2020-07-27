<?php
declare(strict_types=1);

namespace Src\Ysp\Driver;

use Psr\Http\Message\ResponseInterface;
use Src\AbstractDriver;
use Src\Http;

/**
 * Class AbstractDriver
 *
 * @package Src\Ysp\Driver
 * @see     https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html
 */
class YspDriver extends AbstractDriver
{
    /** @var string */
    protected $uri;

    /**
     * get guzzle response
     *
     * @return bool|ResponseInterface
     */
    protected function getResponse()
    {
        return (new Http())
            ->setUri($this->uri)
            ->getResponse();
    }

    /**
     * 获取 m3u8 链接
     *
     * @return bool|array
     */
    public function getM3u8Array(): array
    {
        $response = $this->getResponse();
        if ($response) {
            if (preg_match_all(
                '/(https?:\/\/[\d\.]+\/[\w\-\.]+\.cctv\.cn\/\w+\/)\d{10}\.m3u8/',
                $response->getBody()->__toString(),
                $matches)) {
                if (isset($matches[1]) && $matches[1]) {
                    return array_unique($matches[1]);
                }
            }
        }
        return [];
    }
}