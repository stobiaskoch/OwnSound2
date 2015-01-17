<?php
error_reporting(1);
ini_set('display_errors', 'On');

if($_REQUEST['order']=="download") {

	if (is_writable("./tmp")) {
	} else {
		die('Die Datei kann nicht geschrieben werden');
	}
	require_once('config.inc.php');
	$path = array();
	$albumID = $_REQUEST['albumID'];
	$artistID = getartistIDfromalbumID($albumID);
	$artist = getartist($artistID);
	$album = getalbum($albumID);

	$datei_handle=fopen("../logs/downloads.log",a);
	$timestamp = time();
	$datum = date("d.m.Y",$timestamp);
	$uhrzeit = date("H:i",$timestamp);
	$loggeduser = $_COOKIE['loggedIn'];
	$name = GetUserDetails($loggeduser, 'fullname');
	fwrite($datei_handle, $datum . " - " . $uhrzeit . ": Album ".$album." von ".$artist." heruntergeladen. Benutzer: ".$name."\r\n");
	fclose($datei_handle);
	
	$db_link = mysqli_connect (DBHOST, DBUSER, DBPASS, DBDATABASE );
	$sql = "SELECT * FROM `title` WHERE album='".$albumID."' ORDER BY path";
	$zipname = $artist . " - " . $album;

		$db_erg = mysqli_query( $db_link, $sql );
		if ( ! $db_erg )
		{
			die('Ungültige Abfrage: ' . mysqli_error());
		}

		if (!extension_loaded('zip')) {
			return false;
		}
		$tempfile = tempnam("tmp","zip");
		$zip = new ZipArchive();
		$zip->open($tempfile,ZipArchive::OVERWRITE);

		while ($zeile = mysqli_fetch_array( $db_erg, MYSQL_ASSOC))
		{
		
			$path = $zeile['path'];
			$new_filename = substr($path,strrpos($path,'/') + 1);
			$zip->addFile($path,$new_filename);
			
		}

		$file = './tmp/folder.jpeg';
		$handle = fopen ($file, 'w+');
		
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$sql    = "SELECT imgdata from album where id=$albumID";
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				$data = $row['imgdata'];
		
		fwrite($handle, $data);
		fclose($handle);
		$new_filename = substr($file,strrpos($file,'/') + 1);
		$zip->addFile($file,$new_filename);
		$zip->close();
		header('Content-Type: application/zip; charset=ISO-8859-1');
		header('Content-Length: ' . filesize($tempfile));
		header('Content-Disposition: attachment; filename='.$zipname.'.zip');
		readfile($tempfile);
		unlink($file);
		unlink($tempfile); 
		$sql    = "UPDATE album SET downloads=downloads+1 WHERE id = '$albumID'";
		$result=mysqli_query($con,$sql);
		die();
}  
  
function getartist ($id) {
				require_once('./config.inc.php');
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$sql    = "SELECT name FROM artist WHERE id = '$id'";
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return utf8_encode($row['name']);
				mysqli_free_result($result);
				mysqli_close($con);
}


function getalbum ($id) {
				$sql = "SELECT name FROM album WHERE id = '$id'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return utf8_encode($row['name']);
				mysqli_free_result($result);
				mysqli_close($con);
}

function getTrackTitle($id){
				require_once('./config.inc.php');
				$sql    = "SELECT name FROM title WHERE id = '$id'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return utf8_encode($row['name']);
				mysqli_free_result($result);
				mysqli_close($con);
}

function getartistalbumcount($id) {
				require_once('./config.inc.php');
				$sql    = "SELECT * FROM album WHERE artist = '$id'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row_cnt = $result->num_rows;
				return $row_cnt;
				mysqli_free_result($result);
				mysqli_close($con);
}

function GetTitlesfromAlbumID($id) {
				require_once('./config.inc.php');
				$sql    = "SELECT * FROM title WHERE album = '$id' ORDER BY track";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				while ($row = mysqli_fetch_assoc($result)) {
					$track[] = $row['id'];
				}
				return $track;
				mysqli_free_result($result);
				mysqli_close($con);
}

function GetAlbumIDfromTrackID($id) {
				require_once('./config.inc.php');
				$sql    = "SELECT * FROM title WHERE id = '$id'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return $row['album'];
				mysqli_free_result($result);
				mysqli_close($con);
}


function getartistIDfromalbumID($id) {
				require_once('./config.inc.php');
				$sql    = "SELECT artist FROM album WHERE id = '$id'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return $row['artist'];
				mysqli_free_result($result);
				mysqli_close($con);
}

function getartistIDfromtrackID ($id) {
				require_once('./config.inc.php');
				$sql    = "SELECT artist FROM title WHERE id = '$id'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return $row['artist'];
				mysqli_free_result($result);
				mysqli_close($con);
}

function GetPathfromTrackID ($id) {
				require_once('./config.inc.php');
				$sql    = "SELECT path FROM title WHERE id = '$id'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return $row['path'];
				mysqli_free_result($result);
				mysqli_close($con);
}

function GetDurationfromTrackID ($id) {
				require_once('./config.inc.php');
				$sql    = "SELECT duration FROM title WHERE id = '$id'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return $row['duration'];
				mysqli_free_result($result);
				mysqli_close($con);
}

function GetUserDetails ($username, $detail) {
				require_once('./config.inc.php');
				$sql    = "SELECT * FROM user WHERE name = '$username'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return $row[$detail];
				mysqli_free_result($result);
				mysqli_close($con);
}

function GetUserDetailsByID ($id, $detail) {
				require_once('./config.inc.php');
				$sql    = "SELECT * FROM user WHERE id = '$id'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row=mysqli_fetch_assoc($result);
				return $row[$detail];
				mysqli_free_result($result);
				mysqli_close($con);
}

function GetUserList() {
				require_once('./config.inc.php');
				$sql    = "SELECT name FROM user ORDER BY id";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				while ($row = mysqli_fetch_assoc($result)) {
					$track[] = $row['name'];
				}
				return $track;
				mysqli_free_result($result);
				mysqli_close($con);
}

function CheckUserExist($name) {
				require_once('./config.inc.php');
				$sql    = "SELECT name FROM user WHERE name='$name'";
				$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$result=mysqli_query($con,$sql);
				$row_cnt = mysqli_num_rows($result);
				if(mysqli_num_rows($result)!="0") {
					return "exist";
				}
				mysqli_free_result($result);
				mysqli_close($con);
}

function coverinmysql ($url, $albumID) { 
	require_once('./config.inc.php');
	$file = "./tmp/".basename($url);
	$file = strtolower(str_replace(" ", "", $file));
	file_put_contents($file, fopen($url, 'r'));
	require_once './thumb/ThumbLib.inc.php';
	if(mime_content_type($file)=="image/jpeg") {  
		$optionsbig = array('resizeUp' => true, 'jpegQuality' => 90);
		// copy($file, "./tmp/".$albumID."_copy1.jpg");
		$thumb = PhpThumbFactory::create($file, $optionsbig);
		$thumb->resize(140, 140)->save('./tmp/'.$albumID.'_grossesbild.jpg', 'jpg');
		$hndFile = fopen('./tmp/'.$albumID.'_grossesbild.jpg', "r");
		$data = addslashes(fread($hndFile, filesize('./tmp/'.$albumID.'_grossesbild.jpg')));
		$type = mime_content_type('./tmp/'.$albumID.'_reflection.jpg');
		$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
		$mysqli->query("UPDATE album SET imgdata = '$data', imgtype = '$type' WHERE id='$albumID'");
		$mysqli->query("UPDATE album SET cover='yes' WHERE id = '$albumID'");
		$mysqli->query("UPDATE album SET coverbig='yes' WHERE id = '$albumID'");
		unlink($file);
		unlink('./tmp/'.$albumID.'_grossesbild.jpg');
		echo "Cover für ".$albumID." geändert";
	}
	else
	{
		$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
		$mysqli->query("UPDATE album SET cover='no' WHERE id = '$albumID'");
		$mysqli->query("UPDATE album SET coverbig='no' WHERE id = '$albumID'");
		echo "Fehler...";
	}
}

if($_REQUEST['order']=="coverdown") {
	coverinmysql ($_REQUEST['url'], $_REQUEST['id']);
}

if($_REQUEST['order']=="opendir") {
		opennewdir ($_REQUEST['dir']);
}

if($_REQUEST['order']=="deletealbum") {
		$id = $_REQUEST['id'];
		require_once('./config.inc.php');
		$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
		$mysqli->query("DELETE FROM album WHERE id = $id");
		$mysqli->query("DELETE FROM title WHERE album = $id");
}

if($_REQUEST['order']=="changerole") {
		$id = $_REQUEST['id'];
		$newrole = $_REQUEST['newrole'];
		require_once('./config.inc.php');
		$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
		$mysqli->query("UPDATE `musikdatenbank`.`user` SET `role` = '$newrole' WHERE `user`.`id` = $id");
		echo "Rolle von ".GetUserDetailsByID($id, 'fullname')." auf $newrole geändert";
}

if($_REQUEST['order']=="deleteartist") {
		$id = $_REQUEST['id'];
		require_once('./config.inc.php');
		$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
		$mysqli->query("DELETE FROM artist WHERE id = $id");
		$mysqli->query("DELETE FROM title WHERE artist = $id");
		$mysqli->query("DELETE FROM album WHERE artist = $id");
}

if($_REQUEST['order']=="deleteuser") {
		$id = $_REQUEST['id'];
		$status = GetUserDetailsByID($id, "active");
		if($status=="1") {
			$status = "0";
			$text = GetUserDetailsByID($id, "fullname") . " deaktiviert";
		}
		else
		{
			$status = "1";
			$text = GetUserDetailsByID($id, "fullname") . " aktiviert";
		}
		require_once('./config.inc.php');
		$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
		$mysqli->query("UPDATE `user` SET `active` = '$status' WHERE `user`.`id` = $id;");
		echo $text;
}

if($_REQUEST['order']=="renameuser") {
		$id = $_REQUEST['id'];
		$newname = $_REQUEST['newname'];
		require_once('./config.inc.php');
		$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
		 $mysqli->query("UPDATE `user` SET `name` = '$newname' WHERE `user`.`id` = $id;");
		 echo "Username geändert";
}

if($_REQUEST['order']=="savesettings") {
		$row = $_REQUEST['row'];
		$value = $_REQUEST['value'];
		require_once('./config.inc.php');
		$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
		$mysqli->query("UPDATE `settings` SET `path` = '$value' WHERE `settings`.`id` = 0;");
		echo "Pfad gespeichert";
}

if($_REQUEST['order']=="cleandb") {
	$fileexistnot = 0;
	$albumexistnot = 0;
	require_once('./config.inc.php');
	$sql    = "SELECT path, id FROM title ORDER BY id";
	$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
	$result=mysqli_query($con,$sql);
	while ($row = mysqli_fetch_assoc($result)) {
		$mp3Path = utf8_encode($row['path']);
		if(!file_exists($mp3Path) || !is_file($mp3Path)) {
			$fileexist++;
		}
		else {
			if(!file_exists($row['path']) || !is_file($row['path'])) {
				$fileexist++;
			}
			else {
				$fileexistnot++;
				$result=mysqli_query($con, "DELETE FROM title WHERE id = '".$row['id']."'");
			}
		}
		
	}
	
	
	$sql    = "SELECT id FROM album ORDER BY id";
	$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
	$result=mysqli_query($con,$sql);
		while ($row = mysqli_fetch_assoc($result)) {
			$sql2    = "SELECT id, name FROM title WHERE album = '".$row['id']."'";
			$result2=mysqli_query($con,$sql2);
			if(mysqli_num_rows($result2)=="0") {
				$albumexistnot++;
				$result=mysqli_query($con, "DELETE FROM album WHERE id = '".$row['id']."'");
			}
		
	}
	
	echo $fileexistnot . " Dateien und $albumexistnot Alben aus der DB gelöscht.";

	mysqli_free_result($result);
	mysqli_close($con);
}

if($_REQUEST['order']=="userdetails") {
		$id = $_REQUEST['id'];
		$value = $_REQUEST['value'];
		$detail = $_REQUEST['detail'];
		$loggeduser = $_COOKIE['loggedIn'];
		$mail = GetUserDetails($loggeduser, "email");
		$name = GetUserDetails($loggeduser, 'fullname');
		require_once('./config.inc.php');
		$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
		if($detail=="password") {
			
			$root = $_SERVER['SERVER_NAME'];
			$headers  = "From: admin@".$root;
			mail($mail, "Dein neues OwnSound2-Passwort", "Hallo ".$name.",
Dein Passwort wurden auf '".$value."' geändert. 
Du Kannst Dich jetzt auf ".$root."".OWNURL." einloggen.", $headers);
			$value = md5($value);
			
			echo "Passwort geändert";
			
		}
		else
		{
			echo "Einstellung geändert";
		}
		$mysqli->query("UPDATE `user` SET `$detail` = '$value' WHERE `user`.`id` = $id;");
}

if($_REQUEST['order']=="renamealbum") {
		$id = $_REQUEST['id'];
		$artist = $_REQUEST['artist'];
		$newname = htmlentities($_REQUEST['newname']);
		$join = $_REQUEST['join'];
		require_once('./config.inc.php');
		
		$sql    = "SELECT * FROM album WHERE artist = '$artist' AND name = '$newname'";
		$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
		$result=mysqli_query($con,$sql);
		$row=mysqli_fetch_assoc($result);
		$checkid = $row['id'];
		if($checkid=="") {
			$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
			$mysqli->query("UPDATE `album` SET `name` = '$newname' WHERE `id` = '$id';");
			echo "gespeichert";
		}
		else
		{
			if($join=="yes") {
				$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$mysqli->query("UPDATE `title` SET `album` = '$checkid' WHERE `album` = '$id';");
				$mysqli->query("DELETE FROM album WHERE id = $id");
				echo "gespeichert";
			}
			else
			{
				echo $checkid;
			}
		}
}

if($_REQUEST['order']=="renameartist") {
		$artistID = $_REQUEST['artistID'];
		$newname = $_REQUEST['newname'];
		$join = $_REQUEST['join'];
		require_once('./config.inc.php');
		
		$sql    = "SELECT * FROM artist WHERE name = '$newname'";
		$con=mysqli_connect(DBHOST,DBUSER,DBPASS,DBDATABASE);
		$result=mysqli_query($con,$sql);
		$row=mysqli_fetch_assoc($result);
		$checkid = $row['id'];
		if($checkid=="") {
			$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
			$mysqli->query("UPDATE `artist` SET `name` = '$newname' WHERE `id` = '$artistID';");
			echo "gespeichert";
		}
		else
		{
			if($join=="yes") {
				$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
				$mysqli->query("UPDATE `title` SET `artist` = '$checkid' WHERE `artist` = '$artistID';");
				$mysqli->query("UPDATE `album` SET `artist` = '$checkid' WHERE `artist` = '$artistID';");
				$mysqli->query("DELETE FROM artist WHERE id = $artistID");
				echo "gespeichert";
			}
			else
			{
				echo $checkid;
			}
		}
}

function opennewdir ($dir) {
	$alledateien = scandir($dir);
	 foreach ($alledateien as $datei) {
		if($datei!=".") {
			if(is_dir($dir . "/" . $datei)) {
			$javadir = substr($dir . "/" . $datei, 0, -3);
			//$javadir = str_replace(" ", "%20", $javadir);
			$javadir = urlencode($javadir);
				?>
				<label class="dialogpadding">
				<input type="hidden" id="dir2scan" value="<?php echo $javadir; ?>">
				<a href='#' onclick='opendir("<?php echo urlencode($dir . "/" . $datei); ?>", "<?php echo $datei; ?>")'><span><?php echo $datei; ?></span></a><br>
				</label>
				<?php
			}
		}
	};
}

function map_dirs($path,$level) {
        if(is_dir($path)) {
                if($contents = opendir($path)) {
                        while(($node = readdir($contents)) !== false) {
                                if($node!="." && $node!="..") {
										if(substr($node, -3)=="mp3" or substr($node, -3)=="MP3") {
										$path3 = addslashes($path."/".$node);
										mysql_query("INSERT INTO scanner (path) VALUES ('$path3')");
                                        }
										map_dirs("$path/$node",$level+1);
                                }
                        }
                }
        }
}

function sec2time($time) {
	$time=explode(':',$time);
	if($time[2]=="") {
		$sec=$time[0]*60;
		$sec+=$time[1];
	}
	else
	{
		$sec=$time[0]*3600;
		$sec+=$time[1]*60;
		$sec+=$time[2];
	}
	return $sec;
}

if($_REQUEST['order']=="search") {
	require_once('./config.inc.php');
	$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
	if ($mysqli->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}
	$term = $_REQUEST['search'];
	$query = "SELECT name, id FROM artist WHERE name LIKE '%" . $term . "%' LIMIT 10";

	if ($result = $mysqli->query($query)) {
		while ($row = $result->fetch_assoc()) {
			$albumcount = getartistalbumcount($row['id']);
			if($albumcount <= "2") {
				$albumcount = "1 Album";
			}
			else
			{
				$albumcount = $albumcount. " Alben";
			}
			$rows[] =  "<a href='#' onclick='albumlist(".$row['id'].")'><font size='2px' color='#4faac6'><b>" . str_ireplace($term, "<span style='color: #ff0000;'>".$term."</span>", utf8_encode($row['name'])) . "</font></b></a> - $albumcount";
		}
		$result->free();
	}
	
	echo "<font size='2px'>Interpreten:</font><br>";
	foreach ($rows as $value) {
		// $s in $t durch $r ersetzen:
		
		// echo $bodytag;
		echo "<div id='cssmenu'>" . $value . "</div>";
	}
	echo "<br><font size='2px'><b>Alben:</font></b><br>";
	
	$query = "SELECT name, id FROM album WHERE name LIKE '%" . $term . "%' LIMIT 10";

	if ($result = $mysqli->query($query)) {
		while ($row = $result->fetch_assoc()) {
			$artist = getartist(getartistIDfromalbumID($row['id']));
			$albums[] =  "<a href='#' onclick='tracklist(".$row['id'].")'><font size='2px' color='#4faac6'><b>" . str_ireplace($term, "<span style='color: #ff0000;'>".$term."</span>", $artist) . "</font></b> - " . str_ireplace($term, "<span style='color: #ff0000;'>".$term."</span>", utf8_encode($row['name'])) . "</a>";
		}
		$result->free();
	}
	foreach ($albums as $value) {
		echo "<div id='cssmenu'>" .  $value . "</div>";
	}
	$mysqli->close();
}

if($_REQUEST['order']=="coverupload") {
$albumID = $_REQUEST['albumID'];
?>
<p>
	<label>URL: <input type='url' name='coverurl' id='coverurl'></label>&nbsp;&nbsp;
	<input type='submit' onclick='getcover("+id+", "+artistID+")'>
</p>
<?php
die();
}

if($_REQUEST['order']=="createuser") {

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
	$value = md5($newpass);
	$username = $_REQUEST['username'];
	$fullname = $_REQUEST['fullname'];
	$actlink = md5($username);
	$email = $_REQUEST['email'];
	$checkmail = GetUserDetails($username, 'name');
	if(CheckUserExist($username)==='exist') {
		 die($username.' wird bereits verwendet!');
	}
	require_once('./config.inc.php');
	$mysqli = new mysqli(DBHOST,DBUSER,DBPASS,DBDATABASE);
	$root = $_SERVER['SERVER_NAME'];
	$headers  = "From: admin@".$root;
	/* mail($email, "Dein Account bei OwnSound", "Hallo ".$fullname.",
Dein Account auf http://".$root."".OWNURL." wurde erfolgreich angelegt.
Benutzername ".$username.".
Passwort ".$newpass.". 
Um Deinen Account zu aktivieren, logge Dich jetzt über diesen Link auf <a href='".$root."".OWNURL."?activate=".$actlink."'>LINK</a> ein.", $headers);

	*/
	$mailtext = '<html>
<head>
    <title>Dein Account bei OwnSound2</title>
</head>
 
<body>
 
<h1>Dein Account bei OwnSound2</h1>
 
<p>Hallo, '.$fullname.'!</p>
<p>Dein Account auf http://'.$root.''.OWNURL.' wurde erfolgreich angelegt.<br>
Benutzername '.$username.'.<br>
Passwort '.$newpass.'.<br> 
Um Deinen Account zu aktivieren, logge Dich jetzt über diesen Link auf <a href="http://'.$root.''.OWNURL.'?activate='.$actlink.'">OwnSound2</a> ein.</p>

<font size="1">Solltest du den Link nicht anklicken können, hier im Klartext:<br>
http://'.$root.''.OWNURL.'?activate='.$actlink.'</font>
</body>
</html>
';
 
$absender   = "admin@".$root;
$betreff    = "Dein Account bei OwnSound";
$antwortan  = "admin@".$root;
 
$header  = "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html; charset=iso-8859-1\r\n";
 
$header .= "From: $absender\r\n";
$header .= "Reply-To: $antwortan\r\n";
// $header .= "Cc: $cc\r\n";  // falls an CC gesendet werden soll
$header .= "X-Mailer: PHP ". phpversion();
 
mail( $email,
      $betreff,
      $mailtext,
      $header);
	

	echo "Account $fullname wurde angelegt.";
	$mysqli->query("INSERT INTO user (name, fullname, email, role, actlink, active, password) VALUES ('$username', '$fullname', '$email', 'user', '$actlink', '0', '$value')");
}

function cut_text($string,$laenge)
{ 
  $origin=strlen($string);
    $stri_arr=explode(" ",$string);
    $anzzahl=count($stri_arr);
    $gekuerzt=0;
    $string="";
    
      while($gekuerzt<$anzzahl)
    { 
        $string_alt=$string;
        $string=$string." ".$stri_arr[$gekuerzt];
        $gekuerzt++;
          if(strlen($string)>$laenge)
        { 
            $gekuerzt=$anzzahl; 
            $string=$string_alt;
          } 
      } 
      
    if($laenge<$origin)
    { 
          $string=$string."...";
      } 
      return $string; 
}

Function ip_in_range($ip, $range) {
  if (strpos($range, '/') !== false) {
    // $range is in IP/NETMASK format
    list($range, $netmask) = explode('/', $range, 2);
    if (strpos($netmask, '.') !== false) {
      // $netmask is a 255.255.0.0 format
      $netmask = str_replace('*', '0', $netmask);
      $netmask_dec = ip2long($netmask);
      return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
    } else {
      // $netmask is a CIDR size block
      // fix the range argument
      $x = explode('.', $range);
      while(count($x)<4) $x[] = '0';
      list($a,$b,$c,$d) = $x;
      $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
      $range_dec = ip2long($range);
      $ip_dec = ip2long($ip);

      # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
      #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

      # Strategy 2 - Use math to create it
      $wildcard_dec = pow(2, (32-$netmask)) - 1;
      $netmask_dec = ~ $wildcard_dec;

      return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
    }
  } else {
    // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
    if (strpos($range, '*') !==false) { // a.b.*.* format
      // Just convert to A-B format by setting * to 0 for A and 255 for B
      $lower = str_replace('*', '0', $range);
      $upper = str_replace('*', '255', $range);
      $range = "$lower-$upper";
    }

    if (strpos($range, '-')!==false) { // A-B format
      list($lower, $upper) = explode('-', $range, 2);
      $lower_dec = (float)sprintf("%u",ip2long($lower));
      $upper_dec = (float)sprintf("%u",ip2long($upper));
      $ip_dec = (float)sprintf("%u",ip2long($ip));
      return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
    }

    echo 'Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format';
    return false;
  }

}
?>
