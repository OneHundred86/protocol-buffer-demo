<?php


namespace Protobuf\Test\Server;


use Message\Bar;
use Message\Foo;
use Protobuf\Test\Lib\Pt;
use Swoole\WebSocket\Frame;

class Tcp
{
    protected $server;

    public static function start(string $ip, int $port)
    {
        $server = new \Swoole\Server($ip, $port);

        $tcp = new Static;
        $tcp->server = $server;

        $server->on('Start', [$tcp, 'onStart']);

        //监听连接进入事件
        $server->on('Connect', [$tcp, 'onConnect']);

        //监听数据接收事件
        $server->on('Receive', [$tcp, 'onReceive']);

        //监听连接关闭事件
        $server->on('Close', [$tcp, 'onClose']);

        //启动服务器
        $server->start();
    }

    public function onStart(\Swoole\Server $server)
    {
        echo "server start" . PHP_EOL;
        foreach($server->ports as $port){
            echo sprintf("linsten at: %s:%s", $port->host, $port->port) . PHP_EOL;
        }
    }

    public function onConnect($server, $fd)
    {
        echo sprintf("[%s] connect", $fd) . PHP_EOL;
    }

    public function onReceive($server, $fd, $reactor_id, $data)
    {
        echo sprintf("[%d] receive msg", $fd) . PHP_EOL;

        [$route, $bin] = Pt::bin2int($data);

        if($route == 1){
            $bar = new Bar();
            $bar->mergeFromString($bin);
            var_dump($bar->getFoo()->getId());
            var_dump($bar->getName());
        }else{
            echo sprintf("unknown route: %s", $route) . PHP_EOL;
        }

        $server->send($fd, $data);
    }

    public function onClose($server, $fd)
    {
        echo sprintf("[%s] close", $fd) . PHP_EOL;
    }
}