# 水温管理システム

## 概要

水温管理システムは、ラズパイで測った水温(または気温)を管理するためのシステムです。

![UML図](https://github.com/RyunosukeFuku/water-temp-monitor/blob/master/images/uml.png)


## 構築
```
docker compose build
docker compose up -d
```

## 設定
### cron
ホストマシンPCのcronで設定
毎時0分に自動実行
```
0 * * * * curl -X POST http://"ラズパイピコのIPアドレス"/measure_and_send
```
動作確認として手動実行も可能
```
curl -X POST http://"ラズパイピコのIPアドレス"/measure_and_send
```





