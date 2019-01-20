<?php
/* Copyright (C) 2018  Lee C. Bussy (@LBussy)
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

// Read config settings
if(file_exists('config.php')) {
        require_once('config.php');
}
else {
        die('ERROR: Unable to open required file (config.php).');
}

$response = array();
if(isset($_GET['stdout'])){
        if($_GET['stdout']== '1'){
                $stdout = getEndOfFile("$scriptPath/logs/stdout.txt");
                $response['stdout'] = utf8_decode($stdout);
        }
}
if(isset($_GET['stderr'])){
        if($_GET['stderr']== '1'){
                $stderr = getEndOfFile("$scriptPath/logs/stderr.txt");
                $response['stderr'] = utf8_decode($stderr);
        }
}

header('Content-Type: application/json');
echo json_encode($response);

function getEndOfFile($filename){
        $output = "";
        $fp = fopen($filename,'rb');
        if($fp === false){
                $output = "Cannot open log file $filename.";
        }
        else{
                $stat = fstat($fp);
                $size = $stat['size'];
                if($size > 16384){
                        fseek($fp, -16384, SEEK_END);
                }
                $output = fread($fp, 16384);
        }
        return str_replace(array("\r\n", "\n", "\r"), '<br />', $output);
}

