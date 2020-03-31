# iptv
iptv 国内视频源

## 视频源来源项目
[iptv-m3u-maker](https://github.com/EvilCult/iptv-m3u-maker)

[tv.m3u](https://raw.githubusercontent.com/EvilCult/iptv-m3u-maker/master/http/tv.m3u)

[tv.json](https://raw.githubusercontent.com/EvilCult/iptv-m3u-maker/master/http/tv.json)

## 视频源地址
- 央视频源，强烈推荐
<https://raw.githubusercontent.com/FanchangWang/iptv/master/ysp.m3u>
- 央视频 + 其他源
<https://raw.githubusercontent.com/FanchangWang/iptv/master/gn.m3u>

## EPG 地址
<http://epg.51zmt.top:8000/cc.xml>

## 生成新的 gn.m3u / ysp.m3u 文件
```code
php ./iptv_ysp.php

php ./iptv.php
```

### 当前频道列表

- [ysp频道列表](./CHANNEL_YSP.md)
- [gn频道列表](./CHANNEL.md)