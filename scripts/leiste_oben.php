<?
error_reporting(-1);
ini_set('display_errors', 'On');
require_once('./config.inc.php');
include('./functions.php');
$db_link = mysqli_connect (DBHOST, DBUSER, DBPASS, DBDATABASE );
$alphabet = range('A', 'Z');
$zahlen = range('0', '9');
$useridlist = GetUserList();
?>
<div id='cssmenu' class="navi">
	<ul style="z-index:20;">
		<?php
		foreach($alphabet as $alpha) {
			$sql = "SELECT * FROM artist  WHERE navname like '".$alpha."%' ORDER BY navname";
			$db_erg = mysqli_query( $db_link, $sql );
			$sql2 = "SELECT * FROM artist  WHERE navname like '".$alpha."%' ORDER BY navname";
			$db_erg2 = mysqli_query( $db_link, $sql2 );
			
			if($db_erg2->num_rows == 0) {
				?>
					<li class='has-sub'><a href='#' onclick="artistlist('<?php echo $alpha; ?>')"><span><?php echo $alpha; ?></span></a>
					<ul>
				<?php
			}
			else
			{
				?>	
					<li class='has-sub'><a href='#' onclick="artistlist('<?php echo $alpha; ?>')"><span><?php echo $alpha; ?></span></a>
					<ul>
				<?php
			}
			while ($zeile = mysqli_fetch_array( $db_erg, MYSQL_ASSOC)) {
			?>				
				<li class='last'><a href='#' onclick="albumlist('<?php echo $zeile['id']; ?>')"><span><?php echo utf8_encode($zeile['name']); ?></span></a></li>
			<?php
			}
			?>
				</ul>
				</li>
			<?php
		}
		?>
		<li class='has-sub2' id="system"><a href='#'><span>Settings</span></a>
			<ul>
				<li class='last'><a href='#' onclick="system('2')"><span>Profil</span></a></li>
				<?php if(GetUserDetailsByID($_COOKIE['loggedInID'], 'role')=="admin") { ?>
				<li class='last'><a href='#' onclick="scan('2')"><span>Scanner</span></a></li>
				<li class='last'><a href='#' onclick="admin('2')"><span>Admin</span></a></li>
				<?php } ?>
				<li class='last'><a href='#' onclick="changelog('2')"><span>Changelog</span></a></li>
				<li class='last'><a href='#' onclick="logout()"><span>Logout</span></a></li> 
			</ul>

		</li>
		<p class="ui-widget" style="float:right; margin-right: 12px;">
			<input id="birds" style="width: 130px;">
		</p>
	</ul>

</div>

<div id="systemdialog" title="Systemeinstellungen">
  <p class="dialogpadding">
	<label>Benutzername: <input style="float:right;" type="text" value="<?php echo GetUserDetailsByID($_COOKIE['loggedInID'], 'name'); ?>" id="<?php echo $_COOKIE['loggedInID']; ?>" onchange="renameuser(this.id, this.value);"></label><br>
	<br>
	<label>E-Mail: <input style="float:right;" type="text" value="<?php echo GetUserDetailsByID($_COOKIE['loggedInID'], 'email'); ?>" id="<?php echo $_COOKIE['loggedInID']; ?>" onchange="renameusermail(this.id, this.value);"></label><br>
	<br>
	<label>Passwort: <input style="float:right;" name="firstpw1" type="password" id="passwordcheck"></label><br>
		<br>
	<label>Wiederholung: <input style="float:right;" name="firstpw2" type="password" id="<?php echo $_COOKIE['loggedInID']; ?>" onchange="changepassword(this.id, this.value);"></label>
 </p>
</div>

<div id="scandialog" title="Auf neue Dateien prüfen"></div>
<div id="changelog" class="dialogpadding" title="Changelog"></div>

<div id="admin" title="Administration">
<p class="dialogpadding">
	<label>Pfad: <input type="text" value="<?php echo MUSICDIR; ?>" id="path" onchange="savesettings(this.id, this.value);"></label><br>
	<br>
	<select id="userlist">
		<option value="0">User wählen</option>
		<?php
			foreach($useridlist as $value) {
				$status = "";
				if(GetUserDetails($value, 'active')=="0") {
					$status = " - (gelöscht)";
				}
				if(GetUserDetails($value, 'id')!=$_COOKIE['loggedInID']) {
					echo "<option id='usernameAdmin' data-username='".GetUserDetails($value, 'name')."' data-role='".GetUserDetails($value, 'role')."' data-name='".GetUserDetails($value, 'fullname')."' value=".GetUserDetails($value, 'id').">".GetUserDetails($value, 'fullname')." $status</option>";
				}
			}
			?>
	</select>
	<select id="userrole" style="display: none;">
		<option value="admin">admin</option>
		<option value="user">user</option>
	</select>
		<div id='cssmenu' class="dialogpadding">
		<input type="hidden" value="" id="userIDAdmin">
		&#9658; <a href='#' style="pointer-events: none;" class="useradmin" onclick="deleteuser()"><span>Benutzer de-/aktiveren</span></a><br>
		&#9998; <a href='#' style="pointer-events: none;" class="useradmin" onclick="renameuserAdmin()"><span>Benutzername ändern</span></a><br>
		&#9998; <a href='#' style="pointer-events: none;" class="useradmin" onclick="changepasswordAdmin()"><span>Passwort setzen</span></a><br>
		&#9998; <a href='#' style="pointer-events: all; color: #4faac6;" onclick="CreateUser()"><span>User anlegen</span></a>
		<br><br>
		</div>
</p>
</div>

</html>
  <script>
$(document).ready(function() {
	$('#birds').keyup(function() {
		if($(this).val().length >= 1) {
			$.get("./scripts/functions.php?order=search", {search: $(this).val()}, function(data) {
				$("#mitte").html(data);
			});
		}
	});
	
	
	$('#userlist').on('change', function() {
		var role = $('#userlist option:selected').data('role');
		$('#userrole').val(role);
		var value = $('#userlist').val();
		$('#userIDAdmin').val(value);
		if(value!="0") {
			$(".useradmin").css({"pointer-events": "all", "color": "#4faac6"});
			$("#userrole").show();
		}
		else
		{
			$(".useradmin").css({"pointer-events": "none", "color": "#000"});
			$("#userrole").hide();
		}
	});
	
	$('#userrole').on('change', function() {
		var newrole = $('#userrole').val();
		var id = $('#userlist option:selected').val();
		$('#userlist option:selected').attr('data-role', newrole)
		$("#systemlog").load("./scripts/functions.php?order=changerole&newrole="+newrole+"&id="+id, function(responseTxt,statusTxt,xhr){
		if(statusTxt=="success")
			toastr.info(responseTxt);
		if(statusTxt=="error")
			toastr.error(responseTxt);
		});
	});
	

});

</script>