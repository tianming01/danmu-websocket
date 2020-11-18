<?php
//创建WebSocket Server对象，监听0.0.0.0:9502端口

$ws = new Swoole\WebSocket\Server('0.0.0.0', 9599);
$ws->set(['daemonize'=>1]);

//监听WebSocket连接打开事件
$ws->on('open', function (Swoole\WebSocket\Server $server, Swoole\Http\Request $request) {
    echo 'WebSocket 连接建立:' . $request->fd . PHP_EOL;
    $server->push($request->fd, "WebSocket 连接建立 ：{$request->fd}");
});
//监听WebSocket消息事件
$ws->on('message', function (Swoole\WebSocket\Server $server, Swoole\WebSocket\Frame $frame) {
    echo "从 {$frame->fd} 接收到的数据: {$frame->data}" . PHP_EOL;
    foreach($server->connections as $fd){
        if (!$server->isEstablished($fd)) { // 如果连接不可用则忽略
            continue;
        }
        $server->push($fd , $frame->data); // 服务端通过 push 方法向所有连接的客户端发送数据
    }
});
//监听WebSocket连接关闭事件
$ws->on('close', function (Swoole\Server $server, int $fd) {
    echo "WebSocket 连接关闭:{$fd}" . PHP_EOL;
});
$ws->start();
