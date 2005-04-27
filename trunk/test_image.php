<?php
$img_number = imagecreate(300,30);
$backcolor = imagecolorallocate($img_number,255,255,255);
$textcolor = imagecolorallocate($img_number,33,127,193);
$font = 4;
imagefill($img_number,0,0,$backcolor);
$number = " Ip Logged $_SERVER[REMOTE_ADDR]";
$number .= " | " . date('H:i');
Imagestring($img_number,3,0,0,$number,$textcolor);
header("Content-type: image/png");
imagepng($img_number);
?>

