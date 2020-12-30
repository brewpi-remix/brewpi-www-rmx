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

// See if we are using an IP to access page, and/or if user is on Windows
$ipurl = (filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_IP) ? true : false);
$windows = (preg_match('/windows|win32/i', $_SERVER['HTTP_USER_AGENT']) ? true : false);
// Form URL with host name
$named_url = 'http://' . gethostname() . '.local' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$bjlogo = "images/bonjour.png";

if (isset($chamberLoc)) {
    // If we are running as multi-index.php, be sure to prepend a chamber path
    // It will not read as a multi-chamber otherwise
    $bjlogo = $chamberLoc . "/" . $bjlogo;
}

// Bonjour prompt
$bjprompt = '';
if ($ipurl && $windows) {
    $bjprompt .= "<div id=\"bonjour-panel\" class=\"ui-widget ui-widget-content ui-corner-all\">\r\n";
    $bjprompt .= "<div id=\"bonjour-bar\" class=\"ui-widget ui-widget-header ui-corner-all\">\r\n";
    $bjprompt .= "<a href=\"https://support.apple.com/kb/DL999\">\r\n";
    $bjprompt .= "<img style=\"float: left;\" src=\"" . $bjlogo . "\" alt=\"Bonjour icon\" width=\"43\" /></a>\r\n";
    $bjprompt .= "<p>&nbsp;You are using an IP to access your BrewPi.\r\n";
    $bjprompt .= "You can use <a href=\"" . $named_url . "\">" . $named_url . "</a> instead\r\n";
    $bjprompt .= "if you install <a href=\"https://support.apple.com/kb/DL999\">Bonjour from Apple</a>.\r\n";
    $bjprompt .= "</div>\r\n</div>";
}

echo $bjprompt;

?>
