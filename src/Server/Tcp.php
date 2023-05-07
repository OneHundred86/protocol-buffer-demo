<?php


namespace Protobuf\Test\Server;


use Pt\Message\Bar;
use Pt\Message\Foo;
use Protobuf\Test\Lib\Pt;
use Swoole\WebSocket\Frame;

class Tcp
{
    // 服务器
    protected \Swoole\Server $server;

    // 当前剩下的窗口字节 [$fd => binary]
    protected $binLeftMap;

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

        $this->binLeftMap[$fd] = "";
    }

    public function onReceive($server, $fd, $reactor_id, $data)
    {
        echo sprintf("[%d] receive msg", $fd) . PHP_EOL;

        try {
            // 解决粘包问题
            $this->binLeftMap[$fd] .= $data;
            [$msgLen, $bin] = Pt::bin2int($this->binLeftMap[$fd]);
            if(strlen($bin) < $msgLen){
                return;
            }
            // 只获取一个包的binary
            $routeAndMsgBin = substr($bin, 0, $msgLen);
            $this->binLeftMap[$fd] = substr($bin, $msgLen);


            [$route, $bin] = Pt::bin2int($routeAndMsgBin);

            // 根据路由反系列化binary到class
            switch ($route){
                case 1 :
                    $bar = new Bar();
                    $bar->mergeFromString($bin);

                    var_dump($bar->getFoo()->getId());
                    var_dump($bar->getName());
                    var_dump($bar->getSex());
                    foreach ($bar->getHobbies() as $hobby) {
                        var_dump($hobby);
                    }

                    break;
                default:
                    echo sprintf("unknown route: %s", $route) . PHP_EOL;
            }

            $server->send($fd, $data);
        }catch (\Exception $e){
            echo sprintf("Exception: %s", $e->getMessage()) . PHP_EOL;
        }

    }

    public function onClose($server, $fd)
    {
        echo sprintf("[%s] close", $fd) . PHP_EOL;
        unset($this->binLeftMap[$fd]);
    }
}