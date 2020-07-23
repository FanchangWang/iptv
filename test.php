<?php
$chinaMobileList = require('chinaMobile.php');
$epgList = require('epg.php');

$epgTvgNameList = array_column($epgList, 'tvg-name');

$m3uArr = [];

foreach ($chinaMobileList as $k => $item) {
    $idx = array_search($item['tvg-name'], $epgTvgNameList);
    if ($idx !== false) {
        $tvg = $epgList[$idx];
        $m3uArr[] = "#EXTINF:-1 tvg-id=\"{$tvg['tvg-id']}\" tvg-name=\"{$tvg['tvg-name']}\" tvg-logo=\"{$tvg['tvg-logo']}\" group-title=\"{$tvg['group-title']}\", {$tvg['name']}\n{$item['url']}\n";
    } else {
        $m3uArr[] = "#EXTINF:-1 group-title=\"其他\", {$item['tvg-name']}\n{$item['url']}\n";
    }
}

if (!empty($m3uArr)) {
    $m3u = "#EXTM3U url-tvg=\"http://epg.51zmt.top:8000/cc.xml.gz\"\n";
    $m3u .= implode("", $m3uArr);
    file_put_contents('./china_mobile.m3u', $m3u);
}