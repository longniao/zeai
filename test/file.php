<?php
/**
 * Created by PhpStorm.
 * User: panghe
 * Date: 2020-07-10
 * Time: 16:04
 */

$filename="119580_1594368143huq.mp4";

//方法一：
function get_ext($file_name){
    return array_pop(explode('.', $file_name));
}

echo get_ext($filename);

//方法二：
$fileEx=strtolower(substr(strrchr($filename,"."),1));
echo $fileEx;

//方法三：
$extend=pathinfo($filename);
echo $extend['extension'];
