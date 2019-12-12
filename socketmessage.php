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

/*
To make sockets work on windows, uncomment extension=php_sockets.dll
in php.ini 
*/

require_once('socket_open.php');

// Read config settings
if(file_exists('config.php')) {
	require_once('config.php');
}
else {
	die('ERROR: Unable to open required file (config.php)');
}

function startsWith($haystack, $needle){
	return !strncmp($haystack, $needle, strlen($needle));
}

function readFromSocket($sock){
	$msg = socket_read($sock, 65536);
    if($msg == false){
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
        die("Couldn't read from socket: [$errorcode] $errormsg" . "\nIs the script running?");
    }
    else{
    	return $msg;
    }
}

function writeToSocket($sock, $msg){
    $bytesWritten = socket_write($sock, $msg, 65536);
    if($bytesWritten == false){
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
        die("Couldn't write to socket: [$errorcode] $errormsg" . "\nIs the script running?");
    }
}

error_reporting(E_ALL ^ E_WARNING);
if(isset($_POST['messageType'])){
	$messageType = $_POST['messageType'];
}
else{
	die("messageType not set.");
}

if(isset($_POST['message'])){
	// message with data
	$data = $_POST['message'];
}

$sock = open_socket();
if($sock !== false){
	if(isset($data) && $data != ""){
		switch($messageType){
			case "setActiveProfile":
			case "startNewBrew":
				writeToSocket($sock, $messageType . "=" . $data);
				echo readFromSocket($sock);
				break;
			default:
				writeToSocket($sock, $messageType . "=" . $data);
				break;
		}
	}
	else {
		// message without a data argument
		switch($messageType){
		case "checkScript":
			writeToSocket($sock, "ack");
			$answer = readFromSocket($sock);
			if($answer == "ack"){
				echo 1;
			}
			else{
				echo 0;
			}
			break;
		case "lcd":
			writeToSocket($sock, "lcd");
			$lcdText = readFromSocket($sock);
			echo str_replace(chr(0xB0), "&deg;", $lcdText); // Replace degree sign with &deg;
			break;
		case "statusText":
			writeToSocket($sock, $messageType);
			echo readFromSocket($sock);
			break;
		default:
			// Pass the command to the socket and read the answer if needed
			writeToSocket($sock, $messageType);
			if(startsWith($messageType, "get") or $messageType == "stopLogging" or
				$messageType == "pauseLogging" or $messageType == "resumeLogging"){
				// Return expected data, read from socket
				echo readFromSocket($sock);
			}
		}
	}
	socket_close($sock);
}
else{
    die("Cannot open socket to script.");
}
