<?php
	//Version
	define("VERSION", "2.0");
	//Database Settings
	define("DBHOST", "localhost");
	define("DBUSER", "");
	define("DBPASS", "");
	define("DBDATABASE", "musikdatenbank");
	//Musicdirectory
	// /home/sebkoch/musik/Hörspiele/E/Hoerspiele
	$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
	$sql    = "SELECT path FROM settings";
	$result=mysqli_query($con,$sql);
	$row=mysqli_fetch_assoc($result);
	define("MUSICDIR", $row['path']);
	//define("MUSICDIR", "/home/sebkoch/musik/Hörspiele/E/Hoerspiele/Grusel/Amadeus/");
	//Url
	define("OWNURL", "/ownsound3");
	//UpdateServer
	define("UPDATESERVER", "http://euve30480.vserver.de/ownsound/");
	?>