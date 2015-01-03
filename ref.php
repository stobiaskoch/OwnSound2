<?php
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
<script type="text/javascript" src="./scripts/jquery.jplayer.js"></script>
<script type="text/javascript" src="./scripts/jplayer.playlist.js"></script>
<script src="./scripts/script.js"></script>
<script src="./scripts/ownsound2.min.js"></script>
<script src="./scripts/toastr.min.js"></script>
<script src="./scripts/jquery.toolbar.js"></script>
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
<?php echo "<div id='cssmenu'><center>OwnSound 2 (RC" . VERSION . ")<div id='systemlog' style='display: none;'></div></center></div>"; ?>
<iframe style="display: none;" src="" width="1%" name="zip" id="zip"></iframe>