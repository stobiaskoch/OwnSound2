<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript">
	var cssSelector = { jPlayer: "#jquery_jplayer_2", cssSelectorAncestor: "#jp_container_2" };
	var playlist = [];
	var options = { swfPath: "js", supplied: "ogv, m4v, oga, mp3", preload: "true", size: {
			  width: "0px",
			  height: "0px"}, smoothPlayBar: "true", errorAlerts: "false", warningAlerts: "false"};
	var ploptions = {displayTime: 0, enableRemoveControls: "true", autoPlay: "false" };

	var myPlaylist = new jPlayerPlaylist(cssSelector, playlist, options, ploptions);

	function playtrack(title, mp3) { 
		myPlaylist.add({
			title: title,
			mp3:"./scripts/mp3.php?id="+mp3
		});
	}
	
	function addtrack(title, mp3) { 
		var titles = JSON.parse(localStorage["titles"]);
		var mp3s = JSON.parse(localStorage["mp3s"]);
		titles.push(title);
		mp3s.push(mp3);
		localStorage['titles'] = JSON.stringify(titles);
		localStorage['mp3s'] = JSON.stringify(mp3s);
		myPlaylist.add({
			title: title,
			mp3:"./scripts/mp3.php?id="+mp3
		});
	}
	
	function playtrackonly(title, mp3, albumID, artistname) {
		localStorage.setItem("album", albumID);
		var titles = new Array();
		var mp3s = new Array();
		titles.push(title);
		mp3s.push(mp3);
		localStorage['titles'] = JSON.stringify(titles);
		localStorage['mp3s'] = JSON.stringify(mp3s);
		localStorage['artistname'] = artistname;
		$("#player").load("scripts/player.php");
	}
	
	$(document).ready(function() {
		$("#jquery_jplayer_2").bind($.jPlayer.event.play, function(event) { // Add a listener to report the time play began
			localStorage.setItem("act_title", myPlaylist.current);
		});
		$("#jquery_jplayer_2").bind($.jPlayer.event.pause, function(event) { // Add a listener to report the time play began
			var time = $("#jquery_jplayer_2").data("jPlayer").status.currentTime
			localStorage['time'] = time;
		});
		$("#jquery_jplayer_2").bind($.jPlayer.event.stop, function(event) { // Add a listener to report the time play began
			var time = $("#jquery_jplayer_2").data("jPlayer").status.currentTime
			localStorage['time'] = "";
		});
		
	$("#jquery_jplayer_2").bind($.jPlayer.event.ready, function (event) {
		myPlaylist.play();
	});
	});

</script>
</head>
<?php
include('./functions.php');
error_reporting(1);
$albumpic = $_REQUEST['albumID'];
ini_set('display_errors', 'On');
if($_REQUEST['order']=="playalbum") {
	?>
		<script type="text/javascript">
			myPlaylist.remove();
			var titles = new Array();
			var mp3s = new Array();
			localStorage.setItem("act_title", '0');
		</script>
	<?php
		$artistname = getartist(getartistIDfromalbumID($_REQUEST['albumID']));
		$albumname = getalbum($_REQUEST['albumID']);
		$albumid = $_REQUEST['albumID'];
		$artistname = getartist(getartistIDfromalbumID($albumid));
		$titles = GetTitlesfromAlbumID($albumid);
		foreach ($titles as $value) {
	?>
		<script type="text/javascript">
			myPlaylist.add({
				title: "<?php echo substr(getTrackTitle($value),0, 38); ?>",
				mp3:"./scripts/mp3.php?id=<?php echo $value; ?>"
			});
			titles.push("<?php echo substr(getTrackTitle($value),0, 38); ?>");
			mp3s.push('<?php echo $value; ?>');
			localStorage["titles"] = JSON.stringify(titles);
			localStorage["mp3s"] = JSON.stringify(mp3s);
			myPlaylist.select(localStorage.getItem("act_title"));
		</script>
	<?php
		}
	?>
		<script>
			localStorage['artistname'] = '<?php echo $artistname; ?>';
			localStorage['albumname'] = '<?php echo $albumname; ?>';
			$("#titletext").html("<div id='cssmenu' style='padding-left: 132px; width:180px; height: 50px;'><a href='#' onclick='albumlist("+localStorage.getItem('artistID')+")'><font size='2px' color='#4faac6'><b>["+localStorage.getItem('artistname')+"]</font></b></a><br>"+localStorage.getItem('albumname')+"</div>");
		</script>
	<?php
}
else
{
	?>
		<script type="text/javascript">
			var titles = JSON.parse(localStorage["titles"]);
			var mp3s = JSON.parse(localStorage["mp3s"]);
			for (i = 0; i < mp3s.length; i++) {
				myPlaylist.add({
					title: titles[i],
					mp3:"./scripts/mp3.php?id="+mp3s[i]
				});
			}
			var actual = localStorage.getItem("act_title");
			var actualtime = localStorage.getItem("time");
			actual -= 1;
			myPlaylist.play(actual + 1);
			document.getElementById("imageid").src="./scripts/get.php?picid="+localStorage.getItem("album");
			$("#titletext").html("<div id='cssmenu' style='padding-left: 132px; width:180px; height: 50px;'><a href='#' onclick='albumlist("+localStorage.getItem('artistID')+")'><font size='2px' color='#4faac6'><b>["+localStorage.getItem('artistname')+"]</font></b></a><br>"+localStorage.getItem('albumname')+"</div>");
		</script>
	<?php
	}
?>
<img id="imageid" style="float:left; padding-top:5px;padding-left:22px;" src="./scripts/get.php?picid=<?php echo $albumpic."&".rand(5, 100000); ?>" width='120' height='120'>
<div style="float:left; top:50px; left: 25px; position: absolute;" id="titletext"></div>
<body>
<div id="jquery_jplayer_2" class="jp-jplayer" style="padding-top: 125px"></div>
<div id="jp_container_2" class="jp-audio">
	<div class="jp-type-playlist" id="items">
		<div class="jp-gui jp-interface">
			<ul class="jp-controls">
				<li><a href="javascript:;" class="jp-previous" tabindex="1">previous</a></li>
				<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
				<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
				<li><a href="javascript:;" class="jp-next" tabindex="1">next</a></li>
				<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
				<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
				<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
				<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
			</ul>
			<div class="jp-progress">
				<div class="jp-seek-bar">
					<div class="jp-play-bar"></div>
				</div>
			</div>
			<div class="jp-volume-bar">
				<div class="jp-volume-bar-value"></div>
			</div>
			<div class="jp-current-time"></div>
			<div class="jp-duration"></div>
		</div>
		<div class="jp-playlist" id="paging_container6">
			<ul class="content">
				<li></li>
			</ul>
		</div>
		<div class="jp-no-solution">
			<span>Update nötig</span> Um die Audiodatein anzuhören ist ein aktuelle Flashplayer von Nöten: <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
		</div>
	</div>
</div>
</body>
</html>