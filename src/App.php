<?php


namespace Src;


use Src\ChinaMobile\ChinaMobile;
use Src\Cmvideo\Cmvideo;
use Src\Constant\ChinaMobileConstant;
use Src\Constant\CmvideoConstant;
use Src\Constant\TvConstant;
use Src\Constant\YspConstant;
use Src\Ysp\Ysp;

class App
{
    public static function run()
    {

        $m3uHead = get_m3u_head();

        /** @var string $allContent 带前缀的正文集合 */
        $allContent = '';

        /** @var array $changeList 发生改变的 group */
        $changeList = [];

        /** @var array $factoryList 要处理的工厂 tv group */
        $factoryList = [
            [
                'className' => Ysp::class, 'm3uPath' => YspConstant::M3U_PATH,
                'groupPrefix' => 'ysp', 'groupName' => 'ysp'
            ],
            [
                'className' => Cmvideo::class, 'm3uPath' => CmvideoConstant::M3U_PATH,
                'groupPrefix' => 'cmvideo', 'groupName' => 'cmvideo'
            ],
            [
                'className' => ChinaMobile::class, 'm3uPath' => ChinaMobileConstant::M3U_PATH,
                'groupPrefix' => '移动', 'groupName' => 'china_mobile'
            ]
        ];

        foreach ($factoryList as $factory) {
            /** @var bool $state 检查状态 */
            /** @var bool $change 是否改变 */
            /** @var string $prefixContent 正文详情 */
            extract(self::factory($factory['className'], $factory['m3uPath'], $factory['groupPrefix'], $m3uHead));

            if ($state && $prefixContent) {
                $allContent .= $prefixContent;
            }
            if ($change) {
                $changeList[] = $factory['groupName'];
            }
        }

        if (!empty($changeList)) {
            file_put_contents(BASE_PATH . TvConstant::M3U_PATH, $m3uHead . $allContent);

            static::_pushGit(implode(' & ', $changeList));

            /**
             * @deprecated 2020-08-07 废除旧 path 路径 兼容
             */
        }
    }

    /**
     * m3u 工厂
     *
     * @param string $className
     * @param string $m3uPath
     * @param string $groupPrefix
     * @param string $m3uHead
     * @return array
     */
    private static function factory(string $className, string $m3uPath, string $groupPrefix, string $m3uHead)
    {
        $data = [
            'change' => false, // 是否改变，无论是否成功
            'state' => false, // 是否成功
            'prefixContent' => '', // 带前缀分组的正文
        ];

        /** @var AbstractTv $class */
        $class = new $className();

        $result = $class->check();

        logger()->info("factory prefix {$groupPrefix}", $result);

        /** @var bool $state 检查状态 */
        /** @var bool $change 是否改变 */
        /** @var string $url url详情 */
        extract($result);

        if ($change) { // 发生改变
            $data['change'] = true;
            if ($state) { // 成功
                $content = $class->getTvM3uContent($url);
                $prefixContent = $class->getTvM3uContent($url, $groupPrefix);
                $data['state'] = true;
                $data['prefixContent'] = $prefixContent;
            } else { // 失败
                $content = $class->getM3uLine('源失效等待更新', 'http://127.0.0.1/1.ts', '失效');
            }
            $content = $m3uHead . $content;
            file_put_contents(BASE_PATH . $m3uPath, $content);
        } else { // 未发生改变
            if ($state) { // 成功 // 历史可用
                $prefixContent = $class->getTvM3uContent($url, $groupPrefix);
                $data['state'] = true;
                $data['prefixContent'] = $prefixContent;
            }
        }

        return $data;
    }

    /**
     * 推送变更到 github & gitee
     */
    private static function _pushGit(string $change = '')
    {
        exec("git add .");
        exec("git commit -m 'change group {$change}'");
        exec("git push");
        exec("git push gitee master");
    }
}