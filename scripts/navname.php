<?php
require_once('config.inc.php');
$db_link = mysqli_connect (DBHOST, DBUSER, DBPASS, DBDATABASE );
$sql = "SELECT * FROM artist WHERE (navname='' OR navname IS NULL)";
$db_erg = mysqli_query( $db_link, $sql );
if ( ! $db_erg )
{
	die('Ungültige Abfrage: ' . mysqli_error());
}

while ($zeile = mysqli_fetch_array( $db_erg, MYSQL_ASSOC)) {
	$artist = $zeile['name'];
	$id = $zeile['id'];
	if (strtolower(substr($artist, 0,4)) == "der ") {
		$newname = str_replace("Der ", "", $artist);
		$newname = $newname.", Der";
	}
	else 
	{
		if (strtolower(substr($artist, 0,4)) == "die ") {
			$newname = str_replace("Die ", "", $artist);
			$newname = $newname.", Die";
		}
		else
		{
			if (strtolower(substr($artist, 0,4)) == "das ") {
				$newname = str_replace("Das ", "", $artist);
				$newname = $newname.", Das";
			}
			else 
			{
				if (strtolower(substr($artist, 0,4)) == "the ") {
					$newname = str_replace("The ", "", $artist);
					$newname = $newname.", The";
				}
				else 
				{
					$newname = $artist;
				}
			}
		}
		
	}
	$sql2 = "UPDATE artist SET navname = '$newname' WHERE id='$id'";
	$db_erg2 = mysqli_query( $db_link, $sql2 );
}