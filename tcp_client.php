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

    // Protocol Buffers本身并没有提供解决TCP粘包问题的机制，在这里使用固定长度的消息头来解决粘包问题：消息头用于指定后续消息的长度，这里定义4个字节作为消息头。
    // 定义协议，前4个字节表示消息头（用于路由），后面内容表示消息体
    // 1表示使用 \Pt\Message\Foo 作为消息体
    // 消息长度（固定4字节） + 路由（自定义，固定4字节） + 消息体（系列化）
    $bin = \Protobuf\Test\Lib\Pt::int2bin(1) . $bar->serializeToString();
    $msgBin = \Protobuf\Test\Lib\Pt::int2bin(strlen($bin)) . $bin;
    $client->send($msgBin);

    echo $client->recv();

    $client->close();
});