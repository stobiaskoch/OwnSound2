function read_cookie(my_cookie) {
 var my_cookie_eq = my_cookie + "=";
 var ca = document.cookie.split(';');
 for (var i=0;i< ca.length;i++) {
  var c = ca[i];
  while (c.charAt(0)==' ') {
   c = c.substring(1,c.length);
  }
  if (c.indexOf(my_cookie_eq) === 0) {
   return c.substring(my_cookie_eq.length,c.length);
  }
 }
 return null;
}

function set_cookie(my_cookie,value,days) {
 if (days) {
  var date = new Date();
  date.setTime(date.getTime()+(days*24*60*60*1000));
  var expires = "; expires="+date.toGMTString();
 }
 else expires = ""
 document.cookie = my_cookie+"="+value+expires+"; path=/";
}

$("#leiste_unten").load("scripts/leiste_unten.php?tracklist=" + read_cookie('artistdisplay') ,
		function() {
			$( "#dialog" ).dialog({
				resizable: false,
				height:  250,
				width: 350,
				modal: true,
				autoOpen: false
			});
			$("#albumrenamedialog").dialog({
				resizable: false,
				buttons: [
				{
					id: "button-ok",
					text: "Schließen",
					click: function() {
						$( this ).dialog( "close" );
					}
				}
				],
				height:  250,
				width: 350,
				modal: true,
				autoOpen: false
			});
		});

$("#leiste_oben").load( "scripts/leiste_oben.php",
	function() {
		sysscan();
		var newusercheck = read_cookie('newuser');
		if(newusercheck!=null) {
			$("span.ui-dialog-title").text('Bitte neues Passwort vergeben!');
			system();
			console.log(newusercheck+'hsjd');
		}
	}
	
	
);

function sysscan() {
	$("#scandialog").dialog({
		resizable: false,
		buttons: [
			{
				id: "button-ok",
				text: "Diesen Ordner scannen",
				click: function() {
					var dir = $("#dir2scan").val();
					$("#scandialog").html("Bereite vor. Bitte warten...");
					$("#button-ok").button("disable");
					$("#scandialog").load( "scripts/folder.php?scandir="+dir+"&truncate=no" );
					toastr.success("Scan gestartet");
				}
			},
			{
				id: "button-cancel",
				text: "Abbrechen",
				click: function() {
					var dir = $("#grunddir").val();
					$("#scandialog").load( "scripts/functions.php?order=opendir&dir="+dir );
					$(this).dialog( "close" );
				}
			}
		],
		height:  250,
		width: 400,
		modal: true,
		autoOpen: false
    });

	$("#systemdialog").dialog({
		resizable: false,
		buttons: [
			{
				id: "button-ok",
				text: "Schließen",
				click: function() {
					var value = document.getElementById('grunddir').value;
					$( this ).dialog( "close" );
				}
			}
		],
		height:  250,
		width: 400,
		modal: true,
		autoOpen: false
    });
	$("#admin").dialog({
		resizable: false,
		buttons: [
			{
				id: "button-ok",
				text: "Schließen",
				click: function() {
					$('#userlist').val('0');
					$(this).dialog( "close" );
				}
			}
		],
		height:  250,
		width: 400,
		modal: true,
		autoOpen: false
    });
	
	$("#changelog").dialog({
		resizable: false,
		buttons: [
			{
				id: "button-ok",
				text: "Schließen",
				click: function() {
					$('#userlist').val('0');
					$(this).dialog( "close" );
				}
			}
		],
		height:  'auto',
		width: 'auto',
		modal: true,
		autoOpen: false
    });
}

$("#mitte").load( "scripts/mitte.php?order=lastten" );

var albuminit = read_cookie('albumisplay');


$("#player").load( "scripts/player.php");

function albumlist(id) {
	$("#mitte").load( "scripts/mitte.php?artistID="+id );
}

function artistlist(id) {
	$("#mitte").load( "scripts/mitte.php?artistlist="+id );
}

function tracklist(id) {
	document.cookie="artistdisplay="+id;
	$("#leiste_unten").load( "scripts/leiste_unten.php?tracklist="+id ,
		function() {
			$("#dialog").dialog({
				resizable: false,
				height:  250,
				width: 350,
				modal: true,
				autoOpen: false
			});
			$("#albumrenamedialog").dialog({
				resizable: false,
				buttons: [
				{
					id: "button-ok",
					text: "Schließen",
					click: function() {
						$( this ).dialog( "close" );
					}
				}
				],
				height:  250,
				width: 350,
				modal: true,
				autoOpen: false
			});
		}
	);
}



function playalbum(id, artistID) {
	localStorage.setItem("album", id);
	localStorage.setItem("artist", artistID);
	localStorage.setItem("playalbum", "yes");
	$("#player").load( "scripts/player.php?order=playalbum&albumID="+id ,
		function() {
			document.cookie="albumisplay="+id;
			if(artistID=="lastten") {
				$("#mitte").load( "scripts/mitte.php?order=lastten" );
			}
			else
			{
				$("#mitte").load( "scripts/mitte.php?artistID="+artistID );
				
			}
		}
	);
}
	
function deletealbum(id, albumname, artistID) {
	var r = confirm("Album "+albumname+" wirklich löschen?");
	if (r === true) {
		$( "#leiste_unten" ).load( "scripts/functions.php?order=deletealbum&id="+id ,
		function() {
			toastr.info(albumname+" gelöscht");
			$( "#mitte" ).load( "scripts/mitte.php?artistID="+artistID ); 
		});
	}
}

function deleteartist(artistID, artistname) {
	var r = confirm("Interpret "+artistname+" wirklich löschen?");
	if (r === true) {
		$( "#leiste_unten" ).load( "scripts/functions.php?order=deleteartist&id="+artistID ,
		function() {
			toastr.info(artistname+" gelöscht");
			$( "#mitte" ).load( "scripts/mitte.php?order=lastten" );
		});
	}
}	

 function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $("#img").change(function(){
        readURL(this);
    });
	
function coverdialog(id, albumname, artistID) {
	$("#dialog").html("<p><label>URL: <input type='url' name='coverurl' id='coverurl'></label>&nbsp;&nbsp;<input type='submit' onclick='getcover("+id+", "+artistID+")'></p>");
	$("span.ui-dialog-title").text(albumname); 
	$( "#dialog" ).dialog( "open" );
}

function admin() {
	$( "#admin" ).dialog( "open" );
}

function changelog() {
	$.get( "http://ownsound.no-ip.info/change.log", function( data ) {
		$( "#changelog" ).html("<ul>"+data+"</ul>");
	});
	$( "#changelog" ).dialog( "open" );
}

function scan() {
	var value = document.getElementById('grunddir').value;
	$( "#scandialog" ).dialog( "open" );
	$( "#scandialog" ).load( "scripts/functions.php?order=opendir&dir="+value );
}

function system() {
	$( "#systemdialog" ).dialog( "open" );
}

function renamealbum(id, albumname, artistID) {
	$("#albumrenamedialog").html("<p><input type='hidden' id='renamealbumartist' value="+artistID+"><label>Albumname: <input type='text' name='albumnameranme' id='albumnameranme' value='"+albumname+"'></label><input type='submit' onclick='albumnameranme("+id+", this.value, \"no\")'></p>");
	$("span.ui-dialog-title").text(albumname); 
	$( "#albumrenamedialog" ).dialog( "open" );
}

function renameartist(artistID, oldartistname) {
	$("#renameartistdialog").html("<p><label>Name: <input type='text' name='newartistname' id='newartistname' value='"+oldartistname+"'></input>&nbsp;&nbsp;&nbsp;&nbsp;</label>&nbsp;&nbsp;<input type='submit' onclick='artistrename("+artistID+", \"no\")'></input></p>");
	$("span.ui-dialog-title").text(oldartistname); 
	$( "#renameartistdialog" ).dialog( "open" );
}

function opendir(dir, dir2) {
	$("span.ui-dialog-title").text(dir2);
	$( "#scandialog" ).load( "scripts/functions.php?order=opendir&dir="+dir );
}

function savesettings(row, value) {
	$("#systemlog").load("scripts/functions.php?order=savesettings&row="+row+"&value="+value ,function(responseTxt,statusTxt,xhr){
		if(statusTxt=="success")
			toastr.info(responseTxt);
			$("#path").val(value);
			$('#grunddir').val(value);
		if(statusTxt=="error")
			toastr.error(responseTxt);
	});
	$("#scandialog").load( "scripts/functions.php?order=opendir&dir="+value);
}

function renameuser(id, value) {
  $("#systemlog").load("scripts/functions.php?order=userdetails&detail=name&id="+id+"&value="+value ,function(responseTxt,statusTxt,xhr){
		if(statusTxt=="success")
			toastr.info(responseTxt);
			$("#user").value(value);
		if(statusTxt=="error")
			toastr.error(responseTxt);
	});
}

function renameusermail(id, value) {
	$("#systemlog").load("scripts/functions.php?order=userdetails&detail=email&id="+id+"&value="+value ,function(responseTxt,statusTxt,xhr){
		if(statusTxt=="success")
			toastr.info(responseTxt);
		if(statusTxt=="error")
			toastr.error(responseTxt);
	});
}

function changepassword(id, value) {
	$("#systemlog").load("index.php?order=newuser");
	var value2 = document.getElementById('passwordcheck').value;
	if(value==value2) {
		$("#systemlog").load("scripts/functions.php?order=userdetails&detail=password&id="+id+"&value="+value ,function(responseTxt,statusTxt,xhr){
			if(statusTxt=="success")
				toastr.info(responseTxt);
			if(statusTxt=="error")
				toastr.error(responseTxt);
		});
	}
	else
	{
		toastr.error('Passwörter stimmen nicht überein');
	}
}

function syslogfade() {
	setTimeout(function(){
		$( "#systemlog" ).fadeOut( "slow", function() {
		$( "#systemlog" ).html( "" );
		$( "#systemlog" ).fadeIn( "fast" );
		});
	}, 2000);
}

function playerback(url) {
	$('#player').css("background-image", "url(/script/get.php?id="+url+")"); 
}

function logout() {
	var r = confirm("Wirklich abmelden?");
	if (r === true) {
		$.post( "index.php", {	
			order:"logout"
		}, function( data ) {
			window.location.href = "index.php";
		});
	}
}

function getcover(id, artistID) {
	var url = document.getElementById('coverurl').value;
	console.log(url + " - " + id);
		$.ajax({
			url: "./scripts/functions.php?order=coverdown&url=" + url + "&id="+id,
			type: "get",
			success: function(data) {
				toastr.info('Cover geändert');
				$( "#mitte" ).load( "scripts/mitte.php?artistID="+artistID );
				$( "#dialog" ).dialog( "close" );
			},
			error: function(){
				toastr.error(data);
			}
		});
}

function albumnameranme(id, join) {
	var newname = document.getElementById('albumnameranme').value;
	var artistID2 = document.getElementById('renamealbumartist').value;
	$.ajax({
		url: "./scripts/functions.php?order=renamealbum&join="+join+"&artist="+artistID2+"&newname=" + newname + "&id="+id,
		type: "get",
		success: function(data) {
			if(data!="gespeichert") {
				$("#albumrenamedialog").html("<input type='hidden' id='albumnameranme' value='"+newname+"'><input type='hidden' id='renamealbumartist' value='"+artistID2+"'>Album dieses Namens bereits vorhanden.<br>Sollen diese Titel dem Album zugeordnet werden?<br><br><a href='#' onclick='albumnameranme("+id+", \"yes\")()'><span>Zusammenführen</span></a><a href='#' onclick='$(this).dialog( 'close' );)()'><span>Abbrechen</span></a>");
			}
			else
			{
				tracklist(id);
				albumlist(artistID2);
				toastr.info(newname+" "+data);
				$( "#albumrenamedialog" ).dialog( "close" );
			}
		},
		error: function(){
			toastr.error(data);
		}
	});
}

function artistrename(artistID, join) {
	var newname = document.getElementById('newartistname').value;
	$.ajax({
		url: "./scripts/functions.php?order=renameartist&join="+join+"&artistID="+artistID+"&newname=" + newname,
		type: "get",
		success: function(data) {
			if(data!="gespeichert") {
				$("#renameartistdialog").html("<input type='hidden' id='newartistname' value='"+newname+"'></input>Interpret dieses Namens bereits vorhanden.<br>Sollen diese Titel dem Album zugeordnet werden?<br><br><a href='#' onclick='artistrename("+artistID+", \"yes\")'><span>Zusammenführen</span></a><a href='#' onclick='$( '#renameartistdialog' ).dialog( 'close' );)()'><span>Abbrechen</span></a>");
			}
			else
			{
				$( "#renameartistdialog" ).dialog( "close" );
				$( "#mitte" ).load( "scripts/mitte.php?order=lastten" );
				$( "#leiste_oben" ).load( "scripts/leiste_oben.php",
					function() {
						sysscan();
						toastr.info(newname+" umbenannt!");
					}
				);
			}
		},
		error: function(){
			toastr.error(data);
		 }
	});
}

function deleteuser() {
	var id = $('#userIDAdmin').val();
	var username = $('#userlist option:selected').data('name');
		$("#systemlog").load("./scripts/functions.php?order=deleteuser&id="+id, function(responseTxt,statusTxt,xhr){
		if(statusTxt=="success")
			toastr.info(responseTxt);
		if(statusTxt=="error")
			toastr.error(responseTxt);
		});
}

function renameuserAdmin() {
	var id = $('#userIDAdmin').val();
	var username = $('#userlist option:selected').data('username');
	var newname = prompt("Bitte neuen Benutzernamen eingeben", ""+username+"");
	if (newname !== null) {
		$("#systemlog").load("./scripts/functions.php?order=renameuser&id="+id+"&newname="+newname ,function(responseTxt,statusTxt,xhr){
		if(statusTxt=="success")
			toastr.info(responseTxt);
		if(statusTxt=="error")
			toastr.error(responseTxt);
		});
	}
}

function changepasswordAdmin() {
	var id = $('#userIDAdmin').val();
	var username = $('#userlist option:selected').data('username');
	var newname = prompt( "Bitte neues Passwort eingeben" );
	if (newname !== null) {
		$("#systemlog").load("scripts/functions.php?order=userdetails&detail=password&id="+id+"&value="+newname ,function(responseTxt,statusTxt,xhr){
		if(statusTxt=="success")
			toastr.info(responseTxt);
		if(statusTxt=="error")
			toastr.error(responseTxt);
		});
	}
}

function downloadalbum(id) {
	toastr.info('Download wird vorbereitet');
	var loc = "./scripts/functions.php?order=download&albumID="+id;
	$('#zip').attr('src', loc);
}

function CreateUser() {
	var username = prompt( "Bitte Benutzernamen eingeben" );
	if (username == null) {
		return false;
	}
	var fullname = prompt( "Bitte Vor-, und Zunamen eingeben" );
	if (fullname == null) {
		return false;
	}
	var email = prompt( "Bitte E-Mailadresse eingeben" );
	if (email == null) {
		return false;
	}
		$("#systemlog").load("scripts/functions.php?order=createuser&username="+username+"&fullname="+encodeURIComponent(fullname)+"&email="+email ,function(responseTxt,statusTxt,xhr){
		if(statusTxt=="success")
			toastr.info(responseTxt);
		if(statusTxt=="error")
			toastr.error(responseTxt);
		});


	}

