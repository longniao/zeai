<?php
/**
 * Created by PhpStorm.
 * User: panghe
 * Date: 2020-07-10
 * Time: 16:10
 */

$destination = '/data/wwwroot/zeai/up/p/v/2020/07/119580_1594368143huq.mp4';

$extname = get_ext($destination);
$_img = str_replace('.'.$extname,'.jpg',$destination);
$cmd = 'ffmpeg -i '. $destination .' -ss 1 -vframes 1 ' . $_img;
exec($cmd);

function get_ext($file_name){
    $arr = explode('.', $file_name);
    return array_pop($arr);
}

// ffmpeg -i 119580_1594368143huq.mp4 -ss 1 -f image2 119580_1594368143huq.jpg
// ffmpeg -i 119580_1594368143huq.mp4 -ss 1 -vframes 1 119580_1594368143huq.jpg


