<?php
error_reporting(0);
ini_set('display_errors', 'On');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
require_once('./config.inc.php');
include('./functions.php');
$artistID = $_REQUEST['artistID'];
$artistlist = $_REQUEST['artistlist'];
$db_link = mysqli_connect (DBHOST, DBUSER, DBPASS, DBDATABASE );

if($_REQUEST['order']=="lastten") {
	$sql = "SELECT * FROM album ORDER BY id DESC LIMIT 10";
	$db_erg = mysqli_query( $db_link, $sql );
	echo "<div id='cssmenu'>Die neuesten 10 Alben</div>";
	while ($zeile = mysqli_fetch_array( $db_erg, MYSQL_ASSOC))
	{
		$albumID = $zeile['id'];
		?>
		<div class="figure">
			<a href="#" onclick="tracklist('<?php echo $zeile['id']; ?>')">
				<div class="span4 red">
						<img src="./scripts/get.php?picid=<?php echo $albumID."&".rand(5, 100000); ?>" width='120' height='120'>
						<?php if($_COOKIE["albumisplay"] == $albumID) {
						echo "<div class='overlayisplaying'>";
					}
					else
					{
						echo "<div class='overlay'>";
					}
					?>
						<a id='cssmenu' href='#' onclick="playalbum('<?php echo $albumID; ?>', 'lastten');localStorage.setItem('artistID', '<?php echo getartistIDfromalbumID($albumID); ?>');localStorage.setItem('albumname', '<?php echo getalbum($albumID); ?>');"><span><img src="./image/Play-button-blue.png" width='20' height='20'></span></a>
					</div>
				</div>	
			</a>
			<p><a href="#" onclick="tracklist('<?php echo $zeile['id']; ?>')"><?php echo substr(getalbum($albumID),0, 20); ?></a></p>
		</div>&nbsp;
		<?php
	}
	die();
}

if(!$_REQUEST['artistlist']) {
	$sql = "SELECT * FROM album WHERE artist='$artistID'";
	$db_erg = mysqli_query( $db_link, $sql );
	if($db_erg->num_rows == 0) {
			$albumcount = "Keine Alben gefunden";
			$sql = "SELECT * FROM title WHERE artist='$artistID' ORDER BY path";
		}
		else
		{
			$sql = "SELECT * FROM album WHERE artist='$artistID' ORDER BY name";
		}
	$db_erg = mysqli_query( $db_link, $sql );
	if ( ! $db_erg )
	{
		die('Ungültige Abfrage: ' . mysqli_error());
	}
	$listID = 0;
	$albumcount = getartistalbumcount($artistID);
	if($albumcount <= "2") {
		$albumcount = "1 Album";
	}
	else
	{
		$albumcount = $albumcount. " Alben";
	}
	echo "<div id='cssmenu'><font size='2px' color='#4faac6'><b>".getartist($artistID)."</font></b> - " . $albumcount . "</div>";
		?>	
			<div id='cssmenu'>
			<?php if(GetUserDetailsByID($_COOKIE['loggedInID'], 'role')=="admin") { ?>
			
				&#9998; <a id="albummenu" href='#' onclick="renameartist('<?php echo $artistID; ?>' , '<?php echo getartist($artistID); ?>')"><span>Interpret umbennen</span></a>
				&#10006; <a id="albummenu" href='#' onclick="deleteartist('<?php echo $artistID; ?>' , '<?php echo getartist($artistID); ?>')"><span>Interpret löschen</span></a><br>
			<?php } ?>
		</div>
		<?php
	while ($zeile = mysqli_fetch_array( $db_erg, MYSQL_ASSOC))
	{
		$albumID = $zeile['id'];
		?>
		<div class="figure">
			<a href="#" onclick="tracklist('<?php echo $zeile['id']; ?>')">
				<div class="span4 red">
					<img src="./scripts/get.php?picid=<?php echo $albumID."&".rand(5, 100000); ?>" width='120' height='120'>
					<?php if($_COOKIE["albumisplay"] == $albumID) {
						echo "<div class='overlayisplaying'>";
					}
					else
					{
						echo "<div class='overlay'>";
					}
					?>
					<a id='cssmenu' href='#' onclick="playalbum('<?php echo $albumID; ?>', '<?php echo $artistID; ?>')"><span><img src="./image/Play-button-blue.png" width='20' height='20'></span></a>
					</div>
				</div>	

			</a>

			<p><a style="padding-top: 10px;" href="#" onclick="tracklist('<?php echo $zeile['id']; ?>')"><?php echo substr(getalbum($albumID),0, 19); ?></a></p>
		</div>&nbsp;
		<?php
	}
}
else
{
	$sql = "SELECT * FROM artist  WHERE navname like '".$artistlist."%' ORDER BY navname";
	$db_erg = mysqli_query( $db_link, $sql );
	if($db_erg->num_rows == 0) {
		echo "<div id='cssmenu'>Keine Künstler gefunden</div>";
	}
	else
	{
		$sql = "SELECT * FROM artist  WHERE navname like '".$artistlist."%' ORDER BY navname";
	}
	$db_erg = mysqli_query( $db_link, $sql );
	if ( ! $db_erg )
	{
		die('Ungültige Abfrage: ' . mysqli_error());
	}
	$listID = 0;
	while ($zeile = mysqli_fetch_array( $db_erg, MYSQL_ASSOC))
	{
		$albumcount = getartistalbumcount($zeile['id']);
		if($albumcount <= "2") {
			$albumcount = "1 Album";
		}
		else
		{
			$albumcount = $albumcount. " Alben";
		}
		$artistname = $zeile['name'];
		?>
		<div id='cssmenu' class="navi">
			<a href='#' onclick="albumlist('<?php echo $zeile['id']; ?>')"><span><font color="#4faac6" size="2px"><b><?php echo utf8_encode($zeile['name']); ?></font></b> - <?php echo $albumcount; ?></span></a>
		</div>
		<?php
	}
}
?>
<div id="renameartistdialog" title="<?php echo getartist($artistID); ?>"></div>

<script>
$( "#renameartistdialog" ).dialog({
      resizable: false,
	  buttons: [
        {
		    id: "button-ok",
            text: "Schließen",
            click: function() {
			var value = document.getElementById('grunddir').value;
			$("#systemdialog").html("<p><label>Path: <input type='text' value='"+value+"' name='path' id='path' onchange='savesettings(this.id, this.value);'></label></p>");
			$( this ).dialog( "close" );
            }
        }
    ],
      height:  250,
	  width: 350,
      modal: true,
	  autoOpen: false
});
</script>