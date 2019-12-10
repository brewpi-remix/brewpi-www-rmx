<?php
/* Copyright (C) 2019 Lee C. Bussy (@LBussy)

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
*/

 // Set error reporting level
error_reporting(E_ALL ^ E_WARNING);

// Define variables
$code = 200;
$codedesc = "Ok";
$response = "";
$messageType = "";
$message = "";
 
function startsWith($haystack, $needle) { // TODO: Was here
	return !strncmp($haystack, $needle, strlen($needle));
}

function readFromSocket($sock) { // TODO: Was here
	$msg = socket_read($sock, 65536);
    if ($msg == false) {
        global $code;
        global $codedesc;
        $code = 500;
        $codedesc = "Internal server error";
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
        $msg = "Could not read from socket: [$errorcode] $errormsg";
    }
    return $msg;
}

function writeToSocket($sock, $msg) {  // TODO: Was here
    $bytesWritten = socket_write($sock, $msg, 65536);
    $msg = "";
    if ($bytesWritten == false) {
        global $code;
        global $codedesc;
        $code = 500;
        $codedesc = "Internal server error";
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
        $msg = "Could not write to socket: [$errorcode] $errormsg";
    }
    return $msg;
}

function open_socket() { // TODO: Also in ./program_arduino.php
    $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');
    $useInetSocket = getConfig("useInetSocket", $isWindows);
    if ($useInetSocket)
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    else
        $sock = socket_create(AF_UNIX, SOCK_STREAM, 0);

    if (!($sock === false)) {
        if (
            ((!$useInetSocket) && socket_connect($sock, "$GLOBALS[scriptPath]/BEERSOCKET"))
            || (($useInetSocket) && socket_connect($sock, getConfig("scriptAddress", "localhost"),
            getConfig("scriptPort",6332)))
            )
        {
            socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 15, 'usec' => 0));
        } else {
            socket_close($sock);
		}
	}
	return $sock;
}

function getConfig($key, $defaultValue) { // Was in ./socket_open.php
    return isset($GLOBALS[$key]) ? $GLOBALS[$key] : $defaultValue;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $code = 403;
    $codedesc = "Forbidden";
    $response = "There was a problem with your submission, please try again.";

} elseif (!file_exists('config.php')) {
    $code = 500;
    $codedesc = "Internal server error";
    $response = "Unable to read BrewPi configuration.";

} elseif (empty(strip_tags(trim($_POST["messageType"])))) {
    $code = 400;
    $codedesc = "Bad request";
    $response = "Missing message type. Please complete the form and try again.";

} else {
    require_once('config.php'); // Read config settings

    $sock = open_socket();
    if ($sock !== false){
        // Get the form fields and remove whitespace
        $messageType = strip_tags(trim($_POST["messageType"]));
        $message = strip_tags(trim($_POST["message"]));

        error_log("messageType = " + $messageType);
        error_log("message = " + $message);

        if (!empty($message)) {

            switch ($messageType){ // Message with a message argument
                case "api":
                    writeToSocket($sock, $messageType . "=" . $message);
                    break;
                case "setActiveProfile":
                case "startNewBrew":
                default:
                    writeToSocket($sock, $messageType . "=" . $message);
                    $response = readFromSocket($sock);
                    break;
            } // Switch with message

        } else {

            switch ($messageType) { // Message without a message argument
                case "quit":
                    writeToSocket($sock, $messageType);
                    break;
                case "checkScript":
                    writeToSocket($sock, $messageType);
                    $response = readFromSocket($sock);
                    break;
                case "lcd":
                    writeToSocket($sock, $messageType);
                    $response = readFromSocket($sock);
                    break;
                case "status":
                    writeToSocket($sock, $messageType);
                    $response = readFromSocket($sock);
                    break;
                default:
                    // Pass the command to the socket and read the answer if needed
                    writeToSocket($sock, $messageType);
                    if (startsWith($messageType, "get") or $messageType == "stopLogging" or
                        $messageType == "pauseLogging" or $messageType == "resumeLogging") {
                        // Return expected data, read from socket
                        $response = readFromSocket($sock);
                    }
            } // Switch without message

        }
        socket_close($sock);
    } else {
        // Could not open socket
        $code = 500;
        $codedesc = "Internal server error";
        $response = "Could not open socket.";
    }
}

// Replace degree sign with &deg;
$response = str_replace(chr(0xB0), "&deg;", $response);
// Raise HTTP code and return text
http_response_code($code); // Send response code
$sendText;
$sendText = $code . " (" . $codedesc . ")";
if (!empty($response)) {
    $sendText .= ": " . $response;
}
echo $sendText; // Send response text
