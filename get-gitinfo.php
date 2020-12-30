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
*/

// Git information for footer
$version = trim(shell_exec('git -C ' . __DIR__ . ' describe --tags $(git -C ' . __DIR__ . ' rev-list --tags --max-count=1)'));
$branch = trim(shell_exec('git -C ' . __DIR__ . ' branch | grep \* | cut -d " " -f2'));
$commit = trim(shell_exec('git -C ' . __DIR__ . ' log --oneline -n1'));
$cmtarr = explode(' ', trim($commit));

$commit = "[ <span class=\"version-text-mono\">";
foreach ($cmtarr as $key => $word) {
    if ($key == 0) { // Make commit hash yellow
        $loop = "<span class=\"version-text-monoylw\">" . $word . "</span> - ";
    } else {
        $loop .= $word . " ";
    }
}
$commit .= trim($loop) . "</span> ]";

$division = "";
$division = "<div id=\"version-panel\" class=\"ui-widget ui-widget-content ui-corner-all\">\r\n";
$division .= "<div id=\"bottom-bar\" class=\"ui-widget ui-widget-header ui-corner-all\">\r\n";
$division .= "<div id=\"version-text\">\r\n";
$division .= "<span>BrewPi Remix version: " . trim($version) . " (" . trim($branch) . ")</span>\r\n";
$division .= trim($commit) . "\r\n";
$division .= "</div>\r\n</div>\r\n</div>";

echo $division;

?>
