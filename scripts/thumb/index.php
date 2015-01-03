<?php
require_once './ThumbLib.inc.php';
 
$thumb = PhpThumbFactory::create('../../tmp/folder.jpeg');
/* Params: $percent, $reflection, $white, $border, $borderColor */
$thumb->createReflection(10, 40, 90, true, '#a4a4a4');
$thumb->show();



?>