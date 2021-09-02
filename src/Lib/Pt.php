<?php


namespace Protobuf\Test\Lib;


class Pt
{
    /**
     * 定义字符串的解析为：前16位（2字节）表示字符串的长度，后续的字节为字符串的内容;
     * @param string $str
     * @return string
     */
    public static function str2bin(string $str) : string
    {
        $len = strlen($str);
        return pack("na*", $len, $str);
    }

    /**
     * @param string $bin
     * @return [string $str, string $left]
     */
    public static function bin2str(string $bin)
    {
        if(strlen($bin) < 2){
            throw new \Exception("字节长度不足");
        }

        $arr = unpack("n", $bin);
        if($arr === false){
            throw new \Exception("解析字符串长度失败");
        }

        $len = $arr[1];

        $left = substr($bin, 2);

        $arr = unpack("a$len", $left);
        if($arr === false){
            throw new \Exception("解析字符串失败");
        }

        $str = $arr[1];

        $left = substr($left, $len);

        return [$str, $left];
    }

    /**
     * 有符号整型数，占用4个字节
     * @param int $n
     * @return string
     */
    public static function int2bin(int $n) : string
    {
        return pack("l", $n);
    }

    /**
     * @param string $bin
     * @return [int $n, string $left]
     */
    public static function bin2int(string $bin)
    {
        if(strlen($bin) < 4){
            throw new \Exception("字节长度不足");
        }

        $arr = unpack("l", $bin);
        if($arr === false){
            throw new \Exception("解析int失败");
        }

        $n = $arr[1];

        $left = substr($bin, 4);

        return [$n, $left];
    }
}