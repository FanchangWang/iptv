<?php
$chinaMobileList = require('china_mobile.php');
$epgList = require('epg.php');

$epgTvgNameList = array_column($epgList, 'tvg-name');

$m3uArr = [];

foreach ($chinaMobileList as $k => $item) {
    $idx = array_search($item['tvg-name'], $epgTvgNameList);
    $tvg = array_merge(['tvg-id' => '', 'tvg-name' => '', 'tvg-logo' => '', 'group-title' => '其他'], $item);
    if ($idx !== false) {
        $tvg = array_merge($tvg, $epgList[$idx]);
    }
    if (!isset($tvg['name'])) {
        $tvg['name'] = $tvg['tvg-name'];
    }

    if (!in_array($tvg['group-title'], ['央视', '卫视', 'NewTV'])) {
        $tvg['group-title'] = '卫视';
    }

    $m3uArr[] = "#EXTINF:-1 tvg-id=\"{$tvg['tvg-id']}\" tvg-name=\"{$tvg['tvg-name']}\" tvg-logo=\"{$tvg['tvg-logo']}\" group-title=\"{$tvg['group-title']}\", {$tvg['name']}\n{$tvg['url']}\n";
}

if (!empty($m3uArr)) {
    $m3u = "#EXTM3U url-tvg=\"http://epg.51zmt.top:8000/e.xml.gz\"\n";
    $m3u .= implode("", $m3uArr);
    file_put_contents('./china_mobile.m3u', $m3u);
}