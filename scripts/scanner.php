<?php 
error_reporting(-1);
ini_set('display_errors', 'On');
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.

function send_message($id, $message, $progress) 
{
    $d = array('message' => $message , 'progress' => $progress);
     
    echo "id: $id" . PHP_EOL;
    echo "data: " . json_encode($d) . PHP_EOL;
    echo PHP_EOL;
    ob_flush();
    flush();
}
 
$serverTime = time();


require_once('./getid3/getid3.php');
require_once('./config.inc.php');
$getID3 = new getID3;
$i=1;
//starten



mysql_connect(DBHOST, DBUSER,DBPASS) OR DIE ("NICHT Erlaubt");
mysql_select_db(DBDATABASE) or die ("Die Datenbank existiert nicht.");
mysql_query("SET NAMES 'utf8'");

// Logdatei öffnen

$datei_handle=fopen("../logs/scanner.log",a);
$timestamp = time();
$datum = date("d.m.Y",$timestamp);
$uhrzeit = date("H:i",$timestamp);
fwrite($datei_handle, $datum . " - " . $uhrzeit . ": Scan gestartet.\r\n");

//statistik
mysql_query("UPDATE scanner_log SET folderscanned=folderscanned+1 WHERE id='0'");
$sql    = "SELECT * FROM scanner_log WHERE id = '0'";
$query = mysql_query($sql); 
$Daten = mysql_fetch_assoc($query); 
$foldertoscan = $Daten['foldertoscan'];
$foldertoscan = $foldertoscan - 1;
$folderscanned = $Daten['folderscanned'];

$sql = "SELECT path FROM scanner ORDER BY `ID`";
 
$db_erg = mysql_query( $sql );
if ( ! $db_erg )
{
	die('Ungültige Abfrage: ' . mysql_error());
}

				$result = mysql_query("SELECT * FROM scanner"); 
				$checkscan = mysql_num_rows($result);
				$progress = 100 / $checkscan;
		
$hurz = 1;
while ($zeile = mysql_fetch_array( $db_erg, MYSQL_ASSOC)) {
		$FullFileName = $zeile['path'];
		set_time_limit(180);
		$ThisFileInfo = $getID3->analyze($FullFileName);
		getid3_lib::CopyTagsToComments($ThisFileInfo);
		
//überspringe datei, wenn keine mp3 (hey, that rhymes...)


		
//variablen ermitteln

		$artist = $ThisFileInfo['comments_html']['artist'][0];
		$artist = addslashes($artist);
		$artist = html_entity_decode($artist, ENT_QUOTES, 'UTF-8');

		$album = $ThisFileInfo['comments_html']['album'][0];
		$album = addslashes($album);
		
		$title = $ThisFileInfo['comments_html']['title'][0];
		$title = addslashes($title);
		$title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
		
		$track = $ThisFileInfo['comments_html']['track'][0];
				
		$path = $ThisFileInfo['filenamepath'];
		$path = addslashes($path);
		$path = utf8_encode($path);
		
		$playtime = $ThisFileInfo['playtime_string'];
		$genre = $ThisFileInfo['comments_html']['genre'][0];
		
//checke, ob artist vorhanden


		$sql    = "SELECT * FROM artist WHERE name = '$artist'";
		$query = mysql_query($sql); 
		$Daten = mysql_fetch_assoc($query); 
		$checkartist = $Daten['name'];
		$checkartist = addslashes($checkartist);

	//artistID ermitteln
	
		
		
		//wenn nicht, schreiben

			if($checkartist!=$artist) {
				mysql_query("INSERT INTO artist (name) VALUES ('$artist')");
				$timestamp = time();
				$datum = date("d.m.Y",$timestamp);
				$uhrzeit = date("H:i",$timestamp);
				fwrite($datei_handle, $datum . " - " . $uhrzeit . ": Interprest ".$artist . " erstellt.\r\n");
				mysql_query("UPDATE scanner_log SET artist=artist+1 WHERE id='0'");

			}

			//artistID ermitteln
				$sql    = "SELECT id FROM artist WHERE name = '$artist'";
				$query = mysql_query($sql); 
				$Daten = mysql_fetch_assoc($query); 
				$artistID = $Daten['id'];

//checke, ob album  von diesem artist vorhanden

		$sql    = "SELECT * FROM album WHERE name = '$album' AND artist = '$artistID'";
		$query = mysql_query($sql); 
		$Daten = mysql_fetch_assoc($query); 
		$checkalbum = $Daten['name'];
		$checkalbum = addslashes($checkalbum);
		
	//wenn nicht, schreiben
		
		//if($Daten['artist']!=$artistID) {
		
			if($checkalbum!=$album) {
			
				mysql_query("INSERT INTO album (name, artist, genre) VALUES ('$album', '$artistID', '$genre') ON DUPLICATE KEY UPDATE name='$album', artist='$artistID'");
				$timestamp = time();
				$datum = date("d.m.Y",$timestamp);
				$uhrzeit = date("H:i:s",$timestamp);
				fwrite($datei_handle, $datum . " - " . $uhrzeit . ": Album ".$album . " erstellt.\r\n");
				mysql_query("UPDATE scanner_log SET album=album+1 WHERE id='0'");



				
				
			//albumID ermitteln

			}

		//}
		$sql    = "SELECT id, cover FROM album WHERE name = '$album' AND artist = '$artistID'";
		$query = mysql_query($sql) OR DIE("Konnte Album ID nicht auslesen:<br/>".$sql); 
		$Daten = mysql_fetch_assoc($query); 
		$albumID = $Daten['id'];
		
		//cover vorhanden? schreiben
		if($Daten['cover']!="yes") {
		$ThisFileInfoCover = $getID3->analyze($FullFileName);
		
			getid3_lib::CopyTagsToComments($ThisFileInfoCover);
			
			if($getID3->info['id3v2']['APIC'][0]['data']!="") {
				$artworktmp = './tmp/front'.$albumID.'.jpeg';
				file_put_contents($artworktmp, $getID3->info['id3v2']['APIC'][0]['data']);
				require_once './thumb/ThumbLib.inc.php'; 
				if(mime_content_type($artworktmp)=="image/jpeg") {  
				$options = array('resizeUp' => true, 'jpegQuality' => 60);
				$optionsbig = array('resizeUp' => true, 'jpegQuality' => 90);
				
				$thumb = PhpThumbFactory::create($artworktmp, $optionsbig);
				$thumb->resize(140, 140)->save('./tmp/'.$albumID.'_grossesbild.jpg', 'jpg');

			//	$thumb = PhpThumbFactory::create($artworktmp, $options);
			//	$thumb->resize(70, 70)->save('./tmp/'.$albumID.'_kleinesbild.jpg', 'jpg');

				$hndFile = fopen('./tmp/'.$albumID.'_grossesbild.jpg', "r");
				$data = addslashes(fread($hndFile, filesize('./tmp/'.$albumID.'_grossesbild.jpg')));

			//	$hndFilesmall = fopen('./tmp/'.$albumID.'_kleinesbild.jpg', "r");
			//	$datasmall = addslashes(fread($hndFilesmall, filesize('./tmp/'.$albumID.'_kleinesbild.jpg')));

				$type = mime_content_type('./tmp/'.$albumID.'_grossesbild.jpg');

				mysql_query("UPDATE album SET imgdata = '$data', imgtype = '$type' WHERE id='$albumID'");
				$timestamp = time();
				$datum = date("d.m.Y",$timestamp);
				$uhrzeit = date("H:i.s",$timestamp);
				fwrite($datei_handle, $datum . " - " . $uhrzeit . ": Cover zu Album ".$album . " gespeichert.\r\n");
			//	mysql_query("UPDATE album SET imgdata_small = '$datasmall', imgtype = '$type' WHERE id='$albumID'");
				mysql_query("UPDATE album SET cover='yes' WHERE id = '$albumID'");
				
				unlink($artworktmp);
				unlink('./tmp/'.$albumID.'_grossesbild.jpg');

				
				}
				else
				{
					$timestamp = time();
					$datum = date("d.m.Y",$timestamp);
					$uhrzeit = date("H:i",$timestamp);
					fwrite($datei_handle, $datum . " - " . $uhrzeit . ": Kein Cover zu Album ".$album . " gefunden.\r\n");
					mysql_query("UPDATE album SET cover='no' WHERE id = '$albumID'");
					mysql_query("UPDATE album SET coverbig='no' WHERE id = '$albumID'");
				}	

		}
	}
		
		
		
//checke, ob titel von diesem artist vorhanden

		$sql    = "SELECT path FROM title WHERE path = '$path' AND artist='$artistID'";
		$query = mysql_query($sql); 
		$Daten = mysql_fetch_assoc($query); 
		$checkpath = $Daten['path'];
		$checkpath = addslashes($checkpath);
		
	//wenn nicht, schreiben
		
		if($checkpath!=$path) {
			$sql="INSERT INTO title (name, artist, path, album, duration, track) VALUES ('$title', '$artistID', '".utf8_decode($path)."', '$albumID', '$playtime', '$track') ON DUPLICATE KEY UPDATE name='$title', artist='$artistID', path='$path', album='$albumID', duration='$playtime'";
			mysql_query($sql) OR DIE (mysql_error()."Title konnte nicht eingetragen werden.<br/>".$sql);
			$timestamp = time();
			$datum = date("d.m.Y",$timestamp);
			$uhrzeit = date("H:i",$timestamp);
			fwrite($datei_handle, $datum . " - " . $uhrzeit . ": Titel $title zu ".$album . " hinzugefügt\r\n");			
			$titlecount++;

			mysql_query("UPDATE scanner_log SET title=title+1 WHERE id='0'");

			}
			$scannerid = $zeile['path'];
			mysql_query("DELETE FROM scanner WHERE path = '$scannerid'");
			$progress2 = $progress * $hurz;
			send_message($serverTime, str_replace(MUSICDIR.'/', "", $scannerid), $progress2); 
			$hurz++;
}


send_message($serverTime, 'TERMINATE'); 
include('./navname.php');
$timestamp = time();
$datum = date("d.m.Y",$timestamp);
$uhrzeit = date("H:i",$timestamp);
fwrite($datei_handle, $datum . " - " . $uhrzeit . ": Scan beendet.\r\n");
fclose($datei_handle);
?>
