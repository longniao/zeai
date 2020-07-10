<?php
/**
 * Created by PhpStorm.
 * User: panghe
 * Date: 2020-07-10
 * Time: 16:10
 */

$video_filePath = '/data/wwwroot/zeai/up/p/v/2020/07/119580_1594368143huq.mp4';

$movie = new ffmpeg_movie($video_filePath);
$ff_frame = $movie->getFrame(1);
$gd_image = $ff_frame->toGDImage();
$img="./test.jpg";
imagejpeg($gd_image, $img);
imagedestroy($gd_image);

// ffmpeg -i 119580_1594368143huq.mp4 -ss 1 -f image2 119580_1594368143huq.jpg
// ffmpeg -i 119580_1594368143huq.mp4 -ss 1 -vframes 1 119580_1594368143huq.jpg


