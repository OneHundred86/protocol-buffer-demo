<?php
require "./vendor/autoload.php";

use Swoole\Coroutine\Client;
use function Swoole\Coroutine\run;

$ip = "127.0.0.1";
$port = 9601;

run(function () use($ip, $port) {
    $client = new Client(SWOOLE_SOCK_TCP);
    if (!$client->connect($ip, $port, 1))
    {
        echo "connect failed. Error: {$client->errCode}\n";
        return;
    }

    $foo = new \Pt\Message\Foo();
    $foo->setId(123);
    $bar = new \Pt\Message\Bar();
    $bar->setFoo($foo);
    $bar->setName("张三");
    $bar->setSex(\Pt\Enums\Sex::MALE);
    $bar->setHobbies(["basketball", "football"]);

    # 定义协议，前4个字节表示消息头（用于路由），后面内容表示消息体
    # 1表示使用 \Pt\Message\Foo 作为消息体
    $bin = \Protobuf\Test\Lib\Pt::int2bin(1) . $bar->serializeToString();
    $client->send($bin);

    echo $client->recv();
    $client->close();
});