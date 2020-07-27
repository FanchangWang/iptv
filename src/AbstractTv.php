<?php
declare(strict_types=1);

namespace Src;

use Src\Constant\EpgConstant;

/**
 * Class AbstractTv
 *
 * @package Src
 */
abstract class AbstractTv
{
    /** @var array */
    private $drivers;

    /** @var string 历史 json 路径 */
    protected $historyJsonPath;

    /** @var array 已检测过的 URL */
    private $checkedUrl = [];

    /**
     * AbstractTv constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * 初始化执行
     */
    abstract protected function init(): void;

    /**
     * 获取 m3u 文件正文
     *
     * @param string $url
     * @param string $groupPrefix
     * @return string
     */
    abstract public function getTvM3uContent(string $url, string $groupPrefix = ''): string;


    /**
     * 初始化驱动列表
     *
     * @param array $drivers
     */
    protected function setDrivers(array $drivers)
    {
        $this->drivers = $drivers;
    }

    /**
     * 检查 m3u8 url 是否可用
     *
     * @param string $url
     * @return bool
     */
    protected function checkM3u8Url(string $url): bool
    {
        // 防止重复检测
        if (in_array($url, $this->checkedUrl)) {
            return false;
        }

        $this->checkedUrl[] = $url;

        $response = (new Http())
            ->setUri($url)
            ->getResponse();
        return $response && strpos($response->getBody()->__toString(), 'EXTM3U') !== false;
    }

    /**
     * 生成 m3u 单行
     *
     * @param string $name
     * @param string $url
     * @param string $groupTitle
     * @param string $tvgName
     * @param string $tvgId
     * @param string $tvgLogo
     * @return string
     */
    public function getM3uLine(
        string $name,
        string $url,
        string $groupTitle = '',
        string $tvgName = '',
        string $tvgId = '',
        string $tvgLogo = ''
    ): string {
        $line = '#EXTINF:-1 tvg-id="%s" tvg-name="%s" tvg-logo="%s" group-title="%s", %s' . PHP_EOL . '%s' . PHP_EOL;
        return sprintf($line, $tvgId, $tvgName, $tvgLogo, $groupTitle, $name, $url);
    }

    /**
     * 检查历史或新获取的
     *
     * @return array
     */
    public function check()
    {
        $data = [ // 失败，历史未改变
            'state' => false, // 成功或失败
            'change' => false, // 是否发生改变
            'url' => '' // m3u8 url 原url 或 新url
        ];

        $historyM3u8Url = $this->_getHistoryM3u8Url();

        if ($historyM3u8Url && $this->checkM3u8Url($historyM3u8Url)) { // 成功，历史未改变
            $data['state'] = true;
            $data['change'] = false;
            $data['url'] = $historyM3u8Url;
            return $data;
        }

        if ($url = $this->_checkDriverM3u8Url()) { // 成功，改变了新的 url
            $data['state'] = true;
            $data['change'] = true;
            $data['url'] = $url;
            $this->_saveHistory($url);
            return $data;
        }

        if ($historyM3u8Url) { // 失败，历史已改变
            $data['state'] = false;
            $data['change'] = true;
            $data['url'] = $historyM3u8Url;
            $this->_saveHistory('');
        }

        return $data;
    }

    /**
     * 获取历史保存 m3u8 url
     *
     * @return string
     */
    private function _getHistoryM3u8Url(): string
    {
        $path = BASE_PATH . $this->historyJsonPath;
        if (is_file($path)) {
            $content = @file_get_contents($path);
            if ($content) {
                $jsonArr = json_decode($content, true);
                if (is_array($jsonArr) && isset($jsonArr['url'])) {
                    return $jsonArr['url'];
                }
            }
        }
        return '';
    }

    /**
     * 保存历史记录
     *
     * @param string $url
     */
    private function _saveHistory(string $url)
    {
        $time = time();
        $data = [
            'url' => $url,
            'time' => $time,
            'date' => date('Y-m-d H:i:s', $time)
        ];
        file_put_contents(BASE_PATH . $this->historyJsonPath,
            json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 检查历史保存 m3u8 url 是否可用
     *
     * @return bool|string
     */
    private function _checkHistoryM3u8Url()
    {
        $m3u8 = $this->_getHistoryM3u8Url();
        if ($m3u8 && $this->checkM3u8Url($m3u8)) {
            return $m3u8;
        }
        return false;
    }

    /**
     * 检查驱动类获取的 m3u8 url 是否可用
     *
     * @return bool|string
     */
    private function _checkDriverM3u8Url()
    {
        foreach ($this->drivers as $driverClass) {
            /** @var AbstractDriver $class */
            $class = new $driverClass();
            $m3u8Arr = $class->getM3u8Array();
            foreach ($m3u8Arr as $m3u8) {
                if ($this->checkM3u8Url($m3u8)) {
                    return $m3u8;
                }
            }
            unset($class);
        }
        return false;
    }
}