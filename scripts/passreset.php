<?php
$dummy	= array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z'), array('#','&','@','$','_','%','?','+'));
mt_srand((double)microtime()*1000000);
for ($i = 1; $i <= (count($dummy)*2); $i++)
	{
		$swap		= mt_rand(0,count($dummy)-1);
		$tmp		= $dummy[$swap];
		$dummy[$swap]	= $dummy[0];
		$dummy[0]	= $tmp;
	}
$newpass =  substr(implode('',$dummy),0, 10);

if($_COOKIE['loggedIn']) {
	echo "<meta http-equiv='refresh' content='0; URL=./index.php'>";
	exit;
}

if($_REQUEST ['order']=="reset") {
	require_once('config.inc.php');
	mysql_connect(DBHOST, DBUSER,DBPASS) OR DIE ("NICHT Erlaubt");
	mysql_select_db(DBDATABASE) or die ("Die Datenbank existiert nicht.");
	
	$name = $_POST["name"]; 
	$password = md5($newpass);
	$sql    = "SELECT * FROM user WHERE email='$name'";
	$query = mysql_query($sql); 
	$Daten = mysql_fetch_assoc($query); 
	$fullname = $Daten['fullname'];
	mysql_query("UPDATE user SET password='$password' WHERE email='$name'");
	mysql_query("UPDATE user SET loginfails='2' WHERE email='$name'");
	mysql_query("UPDATE user SET active='1' WHERE email='$name'"); 
	$root = $_SERVER['SERVER_NAME'];
	$headers  = "From: admin@".$root;
	mail($name, 'Dein neues OwnSound2-Passwort', 'Hallo '.$fullname.',
Deine Zugangsaten wurden auf
Passwort: '.$newpass.'
zurückgesetzt

Du Kannst Dich jetzt wieder auf '.$_SERVER['SERVER_NAME'].''.OWNURL.' einloggen.', $headers);

	$datei_handle=fopen("../logs/login.log",a);
	$timestamp = time();
	$datum = date("d.m.Y",$timestamp);
	$uhrzeit = date("H:i:s",$timestamp);
	
	fwrite($datei_handle, $datum . " - " . $uhrzeit .  " : Neues Passwort an ".$name." gesandt. IP: ". $_SERVER['REMOTE_ADDR'] . "\r\n");
	fclose($datei_handle);
echo "<meta http-equiv='refresh' content='0; URL=../index.php'>"; 
}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//DE" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<link id="favicon" rel="icon" type="image/png" href="./img/os_icon2.png"> 
<head>
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<style type="text/css" title="currentStyle">
	@import "./test.css";
</style>
</head>		
<center>
<div id="login">
		<center><img src='../image/os_logo_small.jpg' width='70%'></center>
</div>
<fieldset style="width: 150px;">
		<legend style="margin-right: 220px; width: 150px">Passwort-Reset</legend>
			<table>
			<tr>
				<form action="passreset.php" method="post">
				<td>E-Mailadresse</td><td><input type="text" name="name" size="20"/></td>
			<tr>
				<input type="hidden" name="order" value="reset"/>
				<td><input type="submit" value="Zurücksetzen" /></td> 
				</form>
			</tr>
			</table>
	</fieldset>
