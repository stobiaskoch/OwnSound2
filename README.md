OwnSound2
=========

First Release
OwnSound2 ist eine MP3-Verwaltung und Player auf HTML5/PHP/MySQL-Basis.

Features:
- Zugriff auf MP3-Dateien ausserhalb des www-roots
- Alle Daten werden in MySQL gespeichert
- Cover werden in der MySQL-Datenbank abgelegt, keine tausende jpg-Files auf der Platte
- Interpreten- und Albumdetails bearbeitbar, keine Ändererungen an den Dateien selbst
- Playlists
- Alben als zip downloaden

Install:
- Zip im gewünschten Ordner entpacken
- Öffne mit einem Editor die config.inc.php im Ordner scripts
- Host, User und Passwort der MySQL-Installation eintragen, unter "OWNURL" den Ordner angeben und speichern.
- Importieren der beigefügten SQL-Datei "musikdatenbank.sql" um die Datenbankstruktur zu erzeugen
- Einloggen mit "admin" und "password"
- Ordner mit den MP3-Dateien unter "Admin" eintragen und den Scanner starten

OwnSound2 benutzt folgenden Code:
- jplayer (http://jplayer.org/)
- Toolbar.Js (http://paulkinzett.github.io/toolbar/)
- toastr (https://github.com/CodeSeven/toastr)
- http://cssmenumaker.com/
- getid3 (http://getid3.sourceforge.net/)
- phpthumb (http://phpthumb.sourceforge.net/)


2014 - s.t.koch77@gmail.com
Licence : http://www.wtfpl.net/