# iptv
iptv 国内视频源

## 视频源地址·推荐 (已内置 EPG)(国内 gitee)
- 央视频 & 中国移动 <https://gitee.com/guyuexuan/iptv/raw/master/dist/iptv.m3u>
- 央视频 <https://gitee.com/guyuexuan/iptv/raw/master/dist/ysp.m3u>
- cmvideo <https://gitee.com/guyuexuan/iptv/raw/master/dist/cmvideo.m3u>
- 中国移动 <https://gitee.com/guyuexuan/iptv/raw/master/dist/china_mobile.m3u>

## EPG 地址
    > M3U 视频源已内置 EPG，此地址仅提供给不支持内置 EPG 源的 IPTV 客户端使用
<http://epg.51zmt.top:8000/e.xml.gz>

## 央视频 URI 来源项目
- [TV.Sason](http://tv.sason.xyz/)
- <https://github.com/YueChan/IPTV>
- <https://github.com/qwerttvv/Beijing-IPTV>
- <https://gitee.com/zhxch3/list>
- <https://gitee.com/dyanj311/iptv>
- <https://gitee.com/ajh102026/jmy>
- <https://gitee.com/kkfong820033/zzz>

## 生成新的 m3u 文件
```code
composer install
php ./bin/iptv.php
```