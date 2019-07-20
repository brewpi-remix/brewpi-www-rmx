<?php
/* Copyright (C) 2018, 2019 Lee C. Bussy (@LBussy)
 *
 * This file is part of LBussy's BrewPi WWW Remix (BrewPi-WWW-RMX).
 *
 * BrewPi WWW RMX is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * BrewPi WWW RMX is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BrewPi WWW RMX. If not, see <https://www.gnu.org/licenses/>.
 *
 * These scripts were originally a part of brewpi-www, a part of
 * the BrewPi project. Legacy support (for the very popular Arduino
 * controller) seems to have been discontinued in favor of new hardware.
 *
 * All credit for the original brewpi-www goes to @elcojacobs,
 * @lukepower, @m-mcgowan, @vanosg, @GregAtkinson and I'm sure
 * many more contributors around the world. My apologies if I have
 * missed anyone; those were the names listed as contributors on the
 * Legacy branch.
 *
 * See: 'original-license.md' for notes about the original project's
 * license and credits. */

// This file is loaded into a hidden iFrame. the javascript functions are defined in maintenance-panel.js
require_once('socket_open.php');

// Set instance root
$instanceRoot = str_replace("\\", "/", getcwd());

// Read config settings
if(file_exists('config.php')) {
	require_once('config.php');
}
else {
	$error = "Unable to open required file (config.php)";
	?>
	<script type="text/javascript">window.top.window.programmingError(<?php echo "\"$error\"" ?>)</script>
	<?php
	die($error);
}

error_reporting(E_ALL);

if(isset($_POST['boardType'])){
	$boardType = $_POST['boardType'];
}
else{
	$error = "boardType not specified!";
	?>
	<script type="text/javascript">window.top.window.programmingError(<?php echo "\"$error\"" ?>)</script>
	<?php
	die($error);
}

if(isset($_POST['restoreSettings'])){
	$restoreSettings = $_POST['restoreSettings'];
}
else{
	$error = "restoreSettings not specified!";
	?>
	<script type="text/javascript">window.top.window.programmingError(<?php echo "\"$error\"" ?>)</script>
	<?php
	die($error);
}

if(isset($_POST['restoreDevices'])){
	$restoreDevices = $_POST['restoreDevices'];
}
else{
	$error = "restoreDevices not specified!";
	?>
	<script type="text/javascript">window.top.window.programmingError(<?php echo "\"$error\"" ?>)</script>
	<?php
	die($error);
}

if ($_FILES["file"]["error"] > 0){
	$error = "Hex file error: " . $_FILES["file"]["error"];
	?>
	<script type="text/javascript">window.top.window.programmingError(<?php echo "\"$error\"" ?>)</script>
	<?php
	die($error);
}

$fileName = $_FILES["file"]["name"];
$tempFileName = $_FILES["file"]["tmp_name"];
$newFileName = "$instanceRoot/uploads/" . $fileName;
if(move_uploaded_file($tempFileName, $newFileName)){
	// Success
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		 // Set permissions to allow reading by anyone. When apache is running
		 // as system service, file will not be readable by script.
		 chmod($newFileName, '0666');
	}	
}
else{
	$error = "Cannot move uploaded file.";
	?>
	<script type="text/javascript">window.top.window.programmingError(<?php echo "\"$error\"" ?>)</script>
	<?php
	die($error);
}

$sock = open_socket();
if($sock !== false){
    $cmd = "programArduino={\"boardType\":\"$boardType\",\"fileName\":\"$instanceRoot/uploads/$fileName\",\"restoreSettings\":$restoreSettings, \"restoreDevices\":$restoreDevices}";
	socket_set_timeout($sock, 120, 0); // set timeout to 2 minutes in case programming takes a while
	socket_write($sock, $cmd, 1024);
	// script will return 1 on success and 0 on failure. This blocks the post request until done
	$programmingResult = socket_read($sock, 1024);
	if(strlen($programmingResult)<1){
		$programmingResult = 0;
	}
	socket_close($sock);
}
?>
<script type="text/javascript">
	if(<?php echo $programmingResult?>){
		window.top.window.programmingDone()
	}
	else{
		// window.top.window.programmingFailed() // commented out because it reported errors when there were none
	}
</script>
