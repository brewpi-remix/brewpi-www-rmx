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

$debug = true;                              // Write log file if true
$logfile = "multi-apilog.txt";              // API Debug Log
$subTarget = "brewpi-api.php";              // Target file in multi-chamber
$json = file_get_contents('php://input');   // Get incoming post

function writeLog($logLine) { // Log file writer (if $debug == true)
    global $debug;
    if ($debug) {
        // Get timestamp
        $date = date('Y-m-j H:m:s  ', time());
        //Open the File Stream
        global $logfile;
        $handle = fopen($logfile, "a");

        //Lock File, error if unable to lock
        if (flock($handle, LOCK_EX)) {
            fwrite($handle, $date);
            fwrite($handle, $logLine);
            fwrite($handle, "\n");
            flock($handle, LOCK_UN);
        }
    }
}

function getFileList($dir, $recurse = FALSE, $depth = FALSE) { // Get list of API files
    global $subTarget;
    $retval = [];

    if(substr($dir, -1) != "/") { // Add trailing slash if missing
        $dir .= "/";
    }

    // Open pointer to directory and read list of files
    $d = @dir($dir) or die("getFileList: Failed opening directory {$dir} for reading");
    while(FALSE !== ($entry = $d->read())) {
        // Skip hidden files
        if($entry{0} == ".") continue;
        if(is_dir("{$dir}{$entry}")) {
            // Skip directory;
            if($recurse && is_readable("{$dir}{$entry}/")) {
                if($depth === FALSE) {
                    $retval = array_merge($retval, getFileList("{$dir}{$entry}/", TRUE));
                } elseif($depth > 0) {
                    $retval = array_merge($retval, getFileList("{$dir}{$entry}/", TRUE, $depth-1));
                }
            }
        } elseif(is_readable("{$dir}{$entry}")) {
            if ($entry == $subTarget) {
                $retval[] = [
                    'name' => "{$dir}{$entry}",
                ];
            }
        }
    }
    $d->close();
    return $retval;
}

function postToUrl($url, $jsonString) { // Post JSON to URL and return result
    $stream = stream_context_create(
        array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'Content-Length' => strlen($jsonString),
                'content' => $jsonString,
            ),
        )
    );

    $result = file_get_contents(
        $url,
        false,
        $stream
    );

    return $result;
}

function getHeader($result) { // Determine header type
    $header = "";
    switch (intval(substr($result, 0, 3))) {
        case 200:
            $header = "HTTP/1.1 200 OK";
            break;
        case 400:
            $header = "HTTP/1.1 400 Bad Request";
            break;
        case 403:
            $header = "HTTP/1.1 403 Forbidden";
            break;
        case 500:
            $header = "HTTP/1.1 500 Internal Server Error";
            break;
        default:
            $header = "HTTP/1.1 520 Unknown Error";
            break;
    }
    return $header;
}

function postToTargets($json) {
    $jsonString = json_decode($json);
    if (json_last_error() === JSON_ERROR_NONE) { // JSON is valid
        header("HTTP/1.1 200 OK");
        echo "Ok.";
        // JSON is good, do the posts
        writeLog("Received JSON: " . $json);
        // Get file list
        $dirlist = getFileList("./", TRUE, 1);
        // Sort the array
        sort($dirlist);
        foreach($dirlist as $file => $value) { // First, count targets
            $target = $value["name"];
            if (substr_count($target, '/') >= 2) {
                // Write to target
                $url = "http://localhost" . substr($target, 1);
            
                $result = postToUrl($url, $json); // Send string not decoded (array)

                $retval = intval(substr($result, 0, 3));
                $header = getHeader($result);

                writeLog("Result from " . $url . ": " . $retval);
                echo $url . ": " . $result . "\n";                
            }
        }
        return true;
    } else {
        // Unable to decode JSON
        return false;
    }
}

if (postToTargets($json)) {
    writeLog("JSON received and processed.");
    header("HTTP/1.1 200 OK");
    echo("OK");
} else {
    writeLog("Invalid JSON received.");
    header("HTTP/1.1 400 Bad Request");
    echo "Invalid JSON received.";
}

?>
