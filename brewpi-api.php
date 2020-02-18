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

$debug = false;                                 // Write log file if true
$file = "apilog.txt";                           // API Log
$args = "LOCK_EX | FILE_APPEND";                // File lock mode
$json = file_get_contents('php://input');       // Get incoming post
$url = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/newsocketmessage.php'; // Destination

function writeLog($logLine) { // Log file writer (if $debug == true)
    global $debug;
    if ($debug) {
        // Get timestamp
        $date = date('Y-m-j H:m:s  ', time());
        //Open the File Stream
        global $file;
        $handle = fopen($file, "a");

        //Lock File, error if unable to lock
        if (flock($handle, LOCK_EX)) {
            fwrite($handle, $date);
            fwrite($handle, $logLine);
            fwrite($handle, "\n");
            flock($handle, LOCK_UN);
        }
    }
}

$result = json_decode($json);

if (json_last_error() === JSON_ERROR_NONE) { // JSON is valid
    writeLog("Received JSON: " . $json);

    $postdata = array(
        'messageType' => 'api',
        'message' => $json
    );

    $opts = array('http' => array(
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($postdata)
    ));

    $context  = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);

    writeLog("Result Body: " . $result);

    switch (intval(substr($result, 0, 3))) {
        case 200:
            header("HTTP/1.1 200 OK");
            break;
        case 400:
            header("HTTP/1.1 400 Bad Request");
            break;
        case 403:
            header("HTTP/1.1 403 Forbidden");
            break;
        case 500:
            header("HTTP/1.1 500 Internal Server Error");
            break;
        default:
            header("HTTP/1.1 520 Unknown Error");
            break;
    }
    var_dump($result);
} else {
    // Unable to decode JSON
    writeLog("Invalid JSON received.");
    header("HTTP/1.1 400 Bad Request");
    echo "Invalid JSON received.";
}
