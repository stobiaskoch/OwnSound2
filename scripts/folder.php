<html>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<head>
	<link rel="stylesheet" href="./css/ownsound.css">
	<script>
		var source = 'THE SOURCE';
        function start_task()
        {
            source = new EventSource('./scripts/scanner.php');
             
            //a message is received
            source.addEventListener('message' , function(e) 
            {
                var result = JSON.parse( e.data );
                 
                add_log(result.message);
                 
                document.getElementById('progressor').style.width = result.progress + "%";
                 
                if(e.data.search('TERMINATE') != -1)
                {
                    add_log('Scan fertig');
                    source.close();
					

					$( "#leiste_oben" ).load( "scripts/leiste_oben.php",
						function() {
							sysscan();
						}
					);
					setTimeout(function(){
						var dir = $("#grunddir").val();
						$( "#scandialog" ).load( "scripts/functions.php?order=opendir&dir="+dir );
						$("#scandialog").dialog( "close" );
						$("#button-ok").button("enable");
						$( "#mitte" ).load( "scripts/mitte.php?order=lastten" );
					}, 2000);
					toastr.info("Scan beendet");
                }
            });
             
            source.addEventListener('error' , function(e)
            {
                add_log(e);
                 console.log(e);
            });
        }
         
        function stop_task()
        {
            source.close();
            add_log('Interrupted');
        }
         
        function add_log(message)
        {
            var r = document.getElementById('results');
            r.innerHTML += message + '<br>';
            r.scrollTop = r.scrollHeight;
        }
	</script>

</head>	
<?php
error_reporting(0);
ini_set('display_errors', 'On');
set_time_limit(3000);
require_once('./config.inc.php');
include('./functions.php');
$DirectoryToScan = $_REQUEST['scandir'];
$bla = $_REQUEST['scandir'];
echo "<p style='font-size: 9px;'>Durchsuche ".$DirectoryToScan." .... Bitte warten</p>";
mysql_connect(DBHOST, DBUSER,DBPASS) OR DIE ("NICHT Erlaubt");
mysql_select_db(DBDATABASE) or die ("Die Datenbank existiert nicht.");
mysql_query("SET NAMES 'utf8'");
if($_REQUEST['truncate']=="yes") {
mysql_query("TRUNCATE `album`");
mysql_query("TRUNCATE `artist`");
mysql_query("TRUNCATE `title`");
}
mysql_query("TRUNCATE `scanner`");
map_dirs($bla ,0);
?>
<div id='statnr'></div>
<div id="progressleiste"></div>
	<script language="JavaScript">
	start_task();
	</script>
<div id="results" style="font-size: 9px; border:1px solid #000; padding:10px; width:300px; height:15px; overflow:auto; background:#eee; z-index:10000;"></div>
        <br />
        <div style="border:1px solid #ccc; width:300px; height:20px; overflow:auto; background:#eee;">
            <div id="progressor" style="background:#4faac6; width:0%; height:100%;"></div>
        </div>
</body></html>
 
