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

$file = "test.txt";
$args = "LOCK_EX | FILE_APPEND"; // Separate multiples with pipe
$json = file_get_contents('php://input');
$json .= "\n"; // Add a line return for easy reviewing

//Open the File Stream
$handle = fopen($file, "a");

//Lock File, error if unable to lock
if(flock($handle, LOCK_EX)) {
    fwrite($handle, $json);     //Write the $data into file
    flock($handle, LOCK_UN);    //Unlock File
    // 200 = Ok
    header('X-PHP-Response-Code: 200', true, 200);
} else {
    // 500 = Internal Server Error
    header('X-PHP-Response-Code: 500', true, 500);
}
//Close Stream
?>
