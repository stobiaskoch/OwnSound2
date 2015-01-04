<?php
error_reporting(1);
ini_set('display_errors', 'On');
if($_REQUEST ['order']=="logout") {
	setcookie ("loggedIn", '', time() - 3600);
	setcookie ("loggedInID", '', time() - 3600);
	header('Refresh:0; url=index.php');
	die();
}
if($_REQUEST ['order']=="newuser") {
	setcookie ("newuser", '', time() - 3600);
	die();
}
if($_REQUEST ['activate']) {
require_once('./scripts/config.inc.php');
mysql_connect(DBHOST, DBUSER,DBPASS) OR DIE ("NICHT Erlaubt");
mysql_select_db(DBDATABASE) or die ("Die Datenbank existiert nicht.");
$get_user = mysql_query("SELECT actlink, name FROM user WHERE actlink='".$_REQUEST ['activate']."'"); 
if(mysql_num_rows($get_user)==1) {
	// $username = mysql_result(mysql_query("SELECT name FROM user WHERE actlink='".$_REQUEST ['activate']."'"));
	$query    = "SELECT name, id FROM user WHERE actlink='".$_REQUEST ['activate']."'";
	$resultID = @mysql_query($query);
	$name = mysql_result($resultID,0);
	$id = mysql_result($resultID,0, 1);
	$yearExpire = time() + 60*60*24*365; // 1 Year
	setcookie('loggedIn', $name, $yearExpire);
	setcookie('loggedInID', $id, $yearExpire);
	setcookie('newuser', $id, $yearExpire);
	mysql_query("UPDATE user SET active = '1', actlink = '' WHERE actlink = '".$_REQUEST ['activate']."'");
	echo "Dein Account wurde aktiviert, du wirst jetzt eingeloggt";
	echo "<meta http-equiv='refresh' content='3; URL=index.php'>";
}
else
{
	echo "Ungültiger Link";
}
die();
}
if($_COOKIE['loggedIn']) {
require_once('./scripts/config.inc.php');
include('./scripts/functions.php');
?> 
<!doctype html>
<html lang="en">
<link id="favicon" rel="icon" type="image/png" href="./image/os_icon2.png"> 
<head>
  <meta charset="utf-8">
  <title>OwnSound 2</title>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.min.css">
  <link rel="stylesheet" type="text/css" href="./css/ownsound.css">
  <link rel="stylesheet" href="./css/styles.css">
  <link rel="stylesheet" href="./css/bar-ui.css">
  <link rel="stylesheet" href="./css/player.css">
  <link rel="stylesheet" href="./css/toastr.min.css">
  <link rel="stylesheet" href="./css/jquery.toolbars.css">
</head>
<body>
<div id="leiste_oben"></div>
<div id="mitte"></div>
<div id="player"></div>
<div id="leiste_unten"></div>
<input type="hidden" id="grunddir" value="<?php echo MUSICDIR; ?>">
</body>
<link href="./css/ft-player-style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./js/jquery.jplayer.js"></script>
<script type="text/javascript" src="./js/jplayer.playlist.js"></script>
<script src="./js/ownsound2.js"></script>
<script src="./js/toastr.min.js"></script>
<script src="./js/jquery.toolbar.js"></script>
<script>
toastr.options = {
  "closeButton": false,
  "debug": false,
  "progressBar": false,
  "positionClass": "toast-top-center",
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "2000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "slideDown",
  "hideMethod": "slideUp"
}
</script>
</html>
<?php echo "<div id='cssmenu'><center>OwnSound 2 (RC" . VERSION . ") <div id='systemlog' style='display: none;'></div><a href='http://www.wtfpl.net/'><img src='http://www.wtfpl.net/wp-content/uploads/2012/12/wtfpl-badge-2.png' width='80' height='15' alt='WTFPL' /></a></div></center>"; ?>
<iframe style="display: none;" src="" width="1%" name="zip" id="zip"></iframe>
<?php 
}
if(!$_COOKIE['loggedIn']) {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//DE" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<link id="favicon" rel="icon" type="image/png" href="./image/os_icon2.png"> 
<head>
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
	<style type="text/css" title="currentStyle">
	@import "./css/ownsound.css";
</style>
</head>		
<center>
<div id="login">
	<center><img src='./image/os_logo_small.jpg' width='70%'></center>
</div>
<fieldset style="width: 300px;">
	<legend style="margin-right: 240px;">Login</legend>
		<table>
			<tr>
				<form action="index.php" method="post">
				<td>Username</td><td><input type="text" name="name" size="20"/></td>
			</tr>
				<td>Passwort</td><td><input type="password" name="password" size="20"/></td>
			<tr>
				<input type="hidden" name="order" value="login"/>
				<td><input style="float: right;" type="submit" value="Anmelden" /></td>
				</form>
			</tr>
		</table>
		<a style="font-size: 9px;" href="./scripts/passreset.php">Passwort vergessen?</a>
</fieldset>
<?php }
if($_REQUEST ['order']=="login") {
	require_once('./scripts/config.inc.php');
	mysql_connect(DBHOST, DBUSER,DBPASS) OR DIE ("NICHT Erlaubt");
	mysql_select_db(DBDATABASE) or die ("Die Datenbank existiert nicht.");
	$name = $_POST["name"]; 
	$password = md5($_POST["password"]);
	$ergebnis = mysql_query("SELECT * FROM user WHERE BINARY name='$name'"); 
	$row = mysql_fetch_object($ergebnis);
	$datei_handle=fopen("./logs/login.log",a);
	$timestamp = time();
	$datum = date("d.m.Y",$timestamp);
	$uhrzeit = date("H:i:s",$timestamp);
	if($row->active=="0") {
		fwrite($datei_handle, $datum . " - " . $uhrzeit . " : ".$name." Login fehlgeschlagen da gesperrt. IP: ". $_SERVER['REMOTE_ADDR'] . "\r\n");
		echo "<center>Login fehlgeschlagen. Grund: Account gelöscht/gesperrt.</center>";
		echo "<meta http-equiv='refresh' content='3; URL=index.php'>";
		die();
		}
	$id = $row->id;
	if($row->password != $password){
		fwrite($datei_handle, $datum . " - " . $uhrzeit . " : ".$name." Login fehlgeschlagen. IP: ". $_SERVER['REMOTE_ADDR'] . "\r\n");
		mysql_query("UPDATE user SET loginfails = loginfails + 1 WHERE name = '$name'"); 
		echo "<center>Login fehlgeschlagen</center>";
		$ergebnis = mysql_query("SELECT loginfails FROM user WHERE BINARY name='$name'"); 
		$row = mysql_fetch_object($ergebnis);
		if($row->loginfails=="3") {
			mysql_query("UPDATE user SET active = '0' WHERE name = '$name'"); 
			echo "<center>Dein Account wurde nach 3 Fehlversuchen gesperrt. Schade...</center>"; 
			die();
		}
		else
		{
			echo "<meta http-equiv='refresh' content='3; URL=index.php'>";
			die();
		}
		}
	else
		{
		fwrite($datei_handle, $datum . " - " . $uhrzeit . " : ".$name." Login erfolgreich. IP: ". $_SERVER['REMOTE_ADDR'] . "\r\n");
		$yearExpire = time() + 60*60*24*365; // 1 Year
		setcookie('loggedIn', $name, $yearExpire);
		setcookie('loggedInID', $id, $yearExpire);
		mysql_query("UPDATE user SET loginfails = '0' WHERE name = '$name'");
		echo "<meta http-equiv='refresh' content='0; URL=index.php'>";
		}
	fclose($datei_handle);
}	
?>