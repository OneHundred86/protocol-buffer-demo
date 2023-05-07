<?php
require "./vendor/autoload.php";


# 序列化(将类转化成二进制)
$foo = new \Pt\Message\Foo;
$foo->setId(123);

$bar = new \Pt\Message\Bar();
$bar->setFoo($foo);
$bar->setName("张三");
$bar->setSex(\Pt\Enums\Sex::MALE);
$bar->setHobbies(["basketball", "football"]);

// 将class系列化为为binary
$bin = $bar->serializeToString();

echo "系列化：\n";
var_dump($bin);

echo PHP_EOL;
echo "反系列化:" . PHP_EOL;
# 反序列化（将二进制转化成类）
$bar1 = new \Pt\Message\Bar;
try {
    // 将binary反系列化为class
    $bar1->mergeFromString($bin);

    var_dump($bar1->getFoo()->getId());
    var_dump($bar1->getName());
    var_dump($bar1->getSex());
    foreach ($bar1->getHobbies() as $hobby) {
        var_dump($hobby);
    }

} catch (Exception $e) {
    throw $e;
}
