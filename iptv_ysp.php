<?php

date_default_timezone_set("PRC");

/**
 * 返回直播台信息
 *
 * @return array 直播台信息
 */
function getTvgList()
{
    return require('tv_list.php');
}

/**
 * 创建 bak 备份文件夹
 */
function mkdirBak()
{
    if (!is_dir('./bak')) {
        @mkdir('./bak');
    }
}

/**
 * 获取 html 正文
 *
 * @param string $link
 * @param bool   $header 是否需要 response header
 * @return bool|string
 */
function getHtml($link, $header = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_REFERER, $link);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, $header);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

/**
 * 检查 m3u8 链接是否有效
 *
 * @param string $url
 * @return bool
 */
function checkM3u8Url($url)
{
    static $list = [];

    $arr = parse_url($url);
    if (!$arr || !is_array($arr) || !isset($arr['host'])) {
        logger('the url format error. m3u8 url: ' . $url, 'error');
        return false;
    }

    if (in_array($arr['host'], $list)) {
        return false;
    }

    $list[] = $arr['host'];

    $m3u = getHtml($url);
    if ($m3u && strpos($m3u, 'EXTM3U') !== false) {
        return true;
    } else {
        logger('the url has expired. m3u8 url: ' . $url);
        return false;
    }
}

/**
 * 获取 html 中的 m3u8 链接
 *
 * @param string $html
 * @return bool|string
 */
function getM3u8Url($url, $header = false)
{
    $html = getHtml($url, $header);
    if ($html && preg_match('/(https?:\/\/[\d\.]+\/[\w\-\.]+\.cctv\.cn\/\w+\/\d{10}\.m3u8)/', $html, $matches)) {
        if (isset($matches[1]) && $matches[1]) {
            if (checkM3u8Url($matches[1])) {
                return preg_replace('/\/\d{10}\.m3u8/', '/', $matches[1]);
            }
        }
    }
    return false;
}

/**
 * 获取新的 ysp ip url
 *
 * @return bool|string
 */
function getNewUrl()
{
    // sason
    if ($url = getM3u8Url('http://tv.sason.xyz/new.m3u')) {
        return $url;
    }

    /*
     * 启用了代理 m3u8, 为防止被恶意替换非法电视内容，暂停解析
    // 奇哈
    $qiha = 'http://www.dszbdq.cn/play/ysp0522.php?id=0210103';
    if ($url = getM3u8Url($qiha, true)) {
        return $url;
    } else {
        $html = getHtml("http://www.dszbdq.cn/play/ysp.html?id=0210103");
        if ($html && preg_match('/(http.*dszbdq.*id=)/', $html, $matches)) {
            if (isset($matches[1]) && $matches[1]) {
                $qiha2 = $matches[1] . '0210103';
                if ($qiha2 != $qiha && $url = getM3u8Url($qiha2, true)) {
                    return $url;
                }
            }
        }
    }
    */

    // https://github.com/YueChan/IPTV
    // https://raw.githubusercontent.com/YueChan/IPTV/master/IPTV.m3u // 域名被墙
    if ($url = getM3u8Url('https://github.com/YueChan/IPTV/blob/master/IPTV.m3u')) {
        return $url;
    }

    // https://github.com/qwerttvv/Beijing-IPTV
    // https://raw.githubusercontent.com/qwerttvv/Beijing-IPTV/master/IPTV-Unicom.m3u // 域名被墙
    if ($url = getM3u8Url('https://github.com/qwerttvv/Beijing-IPTV/blob/master/IPTV-Unicom.m3u')) {
        return $url;
    }

    // https://gitee.com/zhxch3/list
    if ($url = getM3u8Url('https://gitee.com/zhxch3/list/raw/master/nj.md')) {
        return $url;
    }

    // https://gitee.com/dyanj311/iptv
    if ($url = getM3u8Url('https://gitee.com/dyanj311/iptv/raw/master/%E4%B8%AD%E5%A4%AE%E7%94%B5%E8%A7%86%E5%8F%B0.m3u')) {
        return $url;
    }

    // https://gitee.com/ajh102026/jmy
    if ($url = getM3u8Url('https://gitee.com/ajh102026/jmy/raw/master/gq.txt')) {
        return $url;
    }

    // https://gitee.com/kkfong820033/zzz
    if ($url = getM3u8Url('https://gitee.com/kkfong820033/zzz/raw/master/tv.txt')) {
        return $url;
    }

    logger('get new url fail', 'error');
    return false;
}

/**
 * 检查 URL 是否变更
 *
 * @return bool|string 值：string 新的 url ;  false 未变更
 */
function checkUrlChange()
{
    $tvg_list = getTvgList();
    $tv_vid = isset($tvg_list[0]['vid']) ? $tvg_list[0]['vid'] : 0;
    if (!$tv_vid) {
        logger('tv list error', 'error');
        die();
    }

    if ($json = json_decode(@file_get_contents('./url.json'), true)) {
        if (is_array($json) && isset($json['url'])) {
            $m3u8_url = $json['url'] . $tv_vid . '.m3u8';
            if (checkM3u8Url($m3u8_url)) {
                return false;
            }
        }
    }

    return getNewUrl();
}

/**
 * URL 变更后备份当前
 *
 * @param string $url 新的 URL 地址
 */
function backUpUrl($url)
{
    $time = time();
    $data = [
        'url' => $url,
        'time' => $time,
        'date' => date('Y-m-d H:i:s', $time)
    ];
    file_put_contents('./url.json', json_encode($data));
    file_put_contents('./bak/url_' . date('YmdHis') . '.json', json_encode($data));
}

/**
 * 创建新的 m3u 文件
 *
 * @param string $url 新的 IP 地址
 * @return int 输出数量
 */
function fetchM3u($url)
{
//    $m3u = "#EXTM3U x-tvg-url=\"http://epg.51zmt.top:8000/cc.xml.gz\" url-tvg=\"http://epg.51zmt.top:8000/cc.xml.gz\" tvg-url=\"http://epg.51zmt.top:8000/cc.xml.gz\n";
    $m3u = "#EXTM3U url-tvg=\"http://epg.51zmt.top:8000/cc.xml.gz\"\n";

    $channel = "| tvg-id | tvg-logo | name | tvg-name | group-title |\n";
    $channel .= "| :---- | :---- | :---- | :---- | :---- |\n";

    $tvg_list = getTvgList();

    foreach ($tvg_list as $tvg) {

        $m3u .= "#EXTINF:-1 tvg-id=\"{$tvg['tvg-id']}\" tvg-name=\"{$tvg['tvg-name']}\" tvg-logo=\"{$tvg['tvg-logo']}\" group-title=\"{$tvg['group-title']}\", {$tvg['name']}\n";

//        $m3u .= isset($tvg['url']) && $tvg['url'] ? $tvg['url'] . "\n" : "http://{$url}/tlivecloud-cdn.ysp.cctv.cn/001/{$tvg['vid']}.m3u8\n";
//        $m3u .= isset($tvg['url']) && $tvg['url'] ? $tvg['url'] . "\n" : "http://{$url}/live-cnc-cdn.ysp.cctv.cn/ysp/{$tvg['vid']}.m3u8\n";
        $m3u .= isset($tvg['url']) && $tvg['url'] ? $tvg['url'] . "\n" : "{$url}{$tvg['vid']}.m3u8\n";

        $channel .= "| {$tvg['tvg-id']} | <img src='{$tvg['tvg-logo']}' alt='{$tvg['tvg-name']}' height='30'> | {$tvg['name']} | {$tvg['tvg-name']} | {$tvg['group-title']} |\n";
    }

    file_put_contents('./ysp.m3u', $m3u);
    file_put_contents('./CHANNEL_YSP.md', $channel);

    return count($tvg_list);
}


/**
 * 推送变更到 github & gitee
 */
function pushGit()
{
    exec("git add .");
    exec("git commit -m 'url change'");
    exec("git push");
    exec("git push gitee master");
}

/**
 * 输出日志
 *
 * @param string $msg  日志内容
 * @param string $type 日志级别
 */
function logger($msg, $type = 'info')
{
    $log = [
        'time' => date('Y-m-d H:i:s'),
        'level' => $type,
        'pid' => getmypid(),
        'msg' => $msg
    ];
    echo json_encode($log, JSON_UNESCAPED_UNICODE) . "\n";
}

mkdirBak();

if (!$url = checkUrlChange()) {
    logger('iptv url is not change! ');
    die;
}

$num = fetchM3u($url);

if ($num) {
    backUpUrl($url);
    pushGit();
}

logger('iptv ysp over! num:' . $num);
