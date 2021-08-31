#### 一、根据proto文件生成代码

```shell
./build_proto.sh
```



#### 二、composer

```shell
composer install
```



#### 三、安装google/protobuf代码库

方法一：使用php扩展安装

方法二：使用composer安装

```
composer require "google/protobuf"
```



#### 四、测试demo

```shell
php test.php
```

