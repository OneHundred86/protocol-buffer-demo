<?php
require "./vendor/autoload.php";

\Protobuf\Test\Server\Tcp::start("127.0.0.1", 9601);