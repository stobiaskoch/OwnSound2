<?php
require_once('./config.inc.php');
include('./functions.php');
$tracklist = $_REQUEST['tracklist'];
$loggeduser = $_COOKIE['loggedIn'];
if(!$_REQUEST['tracklist']) die();
$db_link = mysqli_connect (DBHOST, DBUSER, DBPASS, DBDATABASE );
$sql = "SELECT * FROM title WHERE album='$tracklist' ORDER BY track";
$db_erg = mysqli_query( $db_link, $sql );

echo "<div id='cssmenu' style='position:relative;'><a href='#' onclick='albumlist(".getartistIDfromalbumID($tracklist).")'><font size='2px' color='#4faac6'><b>[" . getartist(getartistIDfromalbumID($tracklist)) . "]</font></b></a> - " . getalbum($tracklist) . " <div id='button-on-hover' style='width: 28px; position:absolute; top:0; right:0; ' class='settings-button'><img src='./image/icon-cog-small.png'></div>";
		?>
		
		<div id='cssmenu_unten' class="toolbar-icons" style="display: none;">
			<a href='#' title="Album abspielen" onclick="playalbum('<?php echo $_REQUEST['tracklist']; ?>', '<?php echo getartistIDfromalbumID($tracklist); ?>');localStorage.setItem('artistID', '<?php echo getartistIDfromalbumID($tracklist); ?>');localStorage.setItem('albumname', '<?php echo getalbum($tracklist); ?>');"><i class="icon-play"></i></a>
			<?php if(GetUserDetails($loggeduser, 'role')=="admin") { ?>
			<a href='#' title="Cover ändern" onclick="coverdialog('<?php echo $_REQUEST['tracklist']; ?>', '<?php echo addslashes(getalbum($tracklist)); ?>', '<?php echo getartistIDfromalbumID($tracklist); ?>')"><i class="icon-picture"></i></a>
			<a href='#' title="Album umbennen" onclick="renamealbum('<?php echo $_REQUEST['tracklist']; ?>' , '<?php echo getalbum($tracklist); ?>', '<?php echo getartistIDfromalbumID($tracklist); ?>', 'no')"><i class="icon-edit"></i></a>
			<a href='#' title="Album löschen" onclick="deletealbum('<?php echo $_REQUEST['tracklist']; ?>' , '<?php echo getalbum($tracklist); ?>' , '<?php echo getartistIDfromalbumID($tracklist); ?>')"><i class="icon-trash"></i></a>
			<?php } ?>
			<a href='#' title="Album herunterladen" onclick="downloadalbum('<?php echo $_REQUEST['tracklist']; ?>')"><i class="icon-download"></i></a>
		</div><br><br>
		<?php
	while ($zeile = mysqli_fetch_array( $db_erg, MYSQL_ASSOC))
	{
		if($zeile['track']<="9") {
			$tracknumber = "0" . $zeile['track'];
		}
		else
		{
			$tracknumber = $zeile['track'];
		}
		$track = $tracknumber . " - " .utf8_encode($zeile['name']) . " -  [" . $zeile['duration'] . "]<br>";
		?>	
		<a href='#' title="Titel abspielen" class="icon-play" style="margin-bottom: 4px; margin-top: 0px;" onclick="playtrackonly('<?php echo substr(utf8_encode($zeile['name']),0, 38); ?>', '<?php echo $zeile['id']; ?>', '<?php echo $_REQUEST['tracklist']; ?>', '<?php echo getartist(getartistIDfromalbumID($tracklist)); ?>');localStorage.setItem('artistID', '<?php echo getartistIDfromalbumID($tracklist); ?>');localStorage.setItem('albumname', '<?php echo getalbum($tracklist); ?>');"></a>
		<a href='#' title="Hinzufügen" class="icon-plus" style="margin-bottom: 4px; margin-top: 0px;" onclick="addtrack('<?php echo substr(utf8_encode($zeile['name']),0, 38); ?>', '<?php echo $zeile['id']; ?>');"></a>
		<a style="margin-bottom: 4px;"><?php echo $track; ?></a>

		<?php
	}
	?>
</div>
<div id="dialog" title="<?php echo getalbum($tracklist); ?>">
</div>

<div id="albumrenamedialog" title="<?php echo getalbum($tracklist); ?>">
  <p>
	<input type="hidden" id="renamealbumartist" value="<?php echo getartistIDfromalbumID($tracklist); ?>">
    <label>Albumname: <input type="text" name="albumnameranme" id="albumnameranme" value="<?php echo getalbum($tracklist); ?>"></label>
    <input type="submit" onclick="albumnameranme('<?php echo $_REQUEST['tracklist']; ?>', <?php echo getartistIDfromalbumID($tracklist); ?>')">
  </p>
</div>

<script type="text/javascript">
	$(document).ready(function($) {
		$('#button-on-hover').toolbar({
			content: '#cssmenu_unten',
			position: 'right',
			hideOnClick: true,
			hover: true
		});
	});
</script>
