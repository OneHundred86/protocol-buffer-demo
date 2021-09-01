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

    $foo = new \Message\Foo();
    $foo->setId(123);
    $bar = new \Message\Bar();
    $bar->setFoo($foo);
    $bar->setName("张三");

    $client->send($bar->serializeToString());

    echo $client->recv();
    $client->close();
});