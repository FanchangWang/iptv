<?php
declare(strict_types=1);


namespace Src;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Http
 *
 * @package Src
 */
class Http
{
    /** @var UriInterface|string 资源 uri */
    private $uri;

    /** @var string|array uri 请求参数体 */
    private $query;

    /** @var array  请求 header */
    private $headers;

    /** @var CookieJarInterface|false */
    private $cookies;

    /** @var bool|array CURL 请求重定向 */
    private $allowRedirects = true;

    /** @var array POST from 表单请求项 */
    private $formParams;

    /** @var array POST 可以 json_encode 转换的 array 数组 */
    private $json;

    /** @var string 请求主体 PUT,POST,PATCH */
    private $body;

    /** @var string CURL method */
    private $method = 'GET';

    /** @var float CURL 请求超时时间 */
    private $timeout = 0;

    /** @var float CURL 等待服务器响应时间 */
    private $connectTimeout = 0;

    /**
     * set url
     *
     * @param UriInterface|string $uri
     * @return $this
     */
    public function setUri($uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * set query
     *
     * @param string|array $query
     * @return $this
     */
    public function setQuery($query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * set headers
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * add header
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }


    /**
     * set user-agent
     *
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent(string $userAgent): self
    {
        $this->addHeader('User-Agent', $userAgent);
        return $this;
    }

    /**
     * set referrer
     *
     * @param string $referer
     * @return $this
     */
    public function setReferer(string $referer): self
    {
        $this->addHeader('referer', $referer);
        return $this;
    }

    /**
     * set cookies
     *
     * @param CookieJarInterface|false $cookies
     * @return $this
     */
    public function setCookies($cookies): self
    {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * set allow_redirects
     *
     * @param bool|array $allowRedirects
     * @return $this
     * @see https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html#allow-redirects
     */
    public function setAllowRedirects($allowRedirects): self
    {
        $this->allowRedirects = $allowRedirects;
        return $this;
    }

    /**
     * set post form params
     * 使用此方法时，会自动将 Content-Type 设置为 application/x-www-form-urlencoded
     *
     * @param array $formParams
     * @return $this
     */
    public function setFromParams(array $formParams): self
    {
        $this->formParams = $formParams;
        return $this;
    }

    /**
     * set post json
     * 使用此方法时，会自动将 Content-Type 设置为 application/json
     *
     * @param array $json
     * @return $this
     */
    public function setJson(array $json): self
    {
        $this->json = $json;
        return $this;
    }

    /**
     * set body
     * formParams 与 body 二选一，formParams 最终会自动转换为 body
     *
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * set curl method
     *
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method = 'GET'): self
    {
        $this->method = $method;
        return $this;
    }


    /**
     * set timeout
     *
     * @param float $timeout
     * @return $this
     */
    public function setTimeout(float $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * set connect timeout
     *
     * @param float $connectTimeout
     * @return $this
     */
    public function setConnectTimeout(float $connectTimeout): self
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    /**
     * get client option
     *
     * @return array
     */
    private function getOption(): array
    {
        $option = [
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout
        ];

        if ($this->query) {
            $option['query'] = $this->query;
        }

        if ($this->headers) {
            $option['headers'] = $this->headers;
        }

        if ($this->cookies) {
            $option['cookies'] = $this->cookies;
        }

        if ($this->allowRedirects) {
            $option['allow_redirects'] = $this->allowRedirects;
        }

        if ($this->formParams) {
            $option['form_params'] = $this->formParams;
        }

        if ($this->body) {
            $option['body'] = $this->body;
        }

        if ($this->json) {
            $option['json'] = $this->json;
        }

        return $option;
    }

    /**
     * get guzzle http response
     *
     * @return bool|ResponseInterface
     */
    public function getResponse()
    {
        try {
            $client = new Client();

            try {
                return $client->request(
                    $this->method,
                    $this->uri,
                    $this->getOption()
                );
            } catch (ConnectException $e) {
                /** 连接超时，增加重试 */
                logger('http')->notice(json_encode(get_throwable_error_log($e)));
                return $client->request(
                    $this->method,
                    $this->uri,
                    $this->getOption()
                );
            }
//        } catch (TransferException $e) {
        } catch (\Throwable $t) {
            logger('http')->error(json_encode(get_throwable_error_log($t)));
        }
        return false;
    }
}