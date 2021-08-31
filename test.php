<?php
require "./vendor/autoload.php";


# 序列化(将类转化成二进制)
$foo = new Message\Foo;
$foo->setId(123);

$msg = new Message\Bar();
$msg->setFoo($foo);
$msg->setName("张三");
$bin = $msg->serializeToString();

# echo $bin . PHP_EOL;

# 反序列化（将二进制转化成类）
$msg1 = new Message\Bar;
$msg1->mergeFromString($bin);
var_dump($msg1->getFoo()->getId());
var_dump($msg1->getName());