<?php
require_once('config.inc.php');
mysql_connect(DBHOST, DBUSER,DBPASS);
mysql_select_db(DBDATABASE) or die ("Die Datenbank existiert nicht."); 
$picid = $_REQUEST['picid'];
$size = $_REQUEST['size'];
$query = "select imgdata,imgtype,imgdata_small,imgdata_big from album where id=$picid"; 
$result = @MYSQL_QUERY($query); 
if($size=="small") {
$data = @MYSQL_RESULT($result,0,"imgdata_small"); 
} elseif($size=="big") {
	$data = @MYSQL_RESULT($result,0,"imgdata_big"); 
} elseif($size!="big" AND $size!="small")
{
	$data = @MYSQL_RESULT($result,0,"imgdata"); 
}
$type = @MYSQL_RESULT($result,0,"imgtype"); 
Header( "Content-type: $type"); 
if($data!="") {
	echo $data;
}
else 
{
	echo readfile('../image/no_cover.jpg');
}

?>
