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

// See if we are using an IP to access page, and/or if user is on Windows
$ipurl = false;
$windows = false;
$named_url = '';
$ipurl = (filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_IP) ? true : false);
$windows = (preg_match('/windows|win32/i', $_SERVER['HTTP_USER_AGENT']) ? true : false);
// Form URL with host name
$named_url = 'http://' . gethostname() . '.local' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Bonjour prompt
$bjprompt = '';
if ($ipurl && $windows) {
    $bjprompt .= '<div id="bonjour-panel" class="ui-widget ui-widget-content ui-corner-all">';
    $bjprompt .= '<div id="top-bar" class="ui-widget ui-widget-header ui-corner-all">';
    $bjprompt .= '<a href="https://support.apple.com/kb/DL999">';
    $bjprompt .= '<img style="float: left;" src="images/bonjour.png" alt="Bonjour icon" width="43" /></a>';
    $bjprompt .= '<p>&nbsp;I see you are using an IP address to access your BrewPi.';
    $bjprompt .= 'Did you know you can use <a href="' . $named_url . '">its name</a> instead? ';
    $bjprompt .= 'Look into <a href="https://support.apple.com/kb/DL999">Bonjour from Apple</a>.';
    $bjprompt .= '</div></div>';
}

// Git information for footer
$version = '';
$branch = '';
$commit = '';
$arr = '';
$word = '';
$gitinfo = '';
$division = '';
$version = shell_exec('git describe --tags $(git rev-list --tags --max-count=1)');
$branch = shell_exec('git branch | grep \* | cut -d " " -f2');
$commit = shell_exec('git -C . log --oneline -n1');
$arr = explode(' ',trim($commit));
$commit='';
foreach ($arr as $key => $word) {
    if ($key == 0) {
        $commit .= '<span class="version-text-monoylw">' . $word . '</span> ';
    } else {
        $commit .= $word . ' ';
    }
}
$commit = rtrim($commit) . '</span>';
$gitinfo = $version . ' (<span class="version-text-mono">' . rtrim($branch) . '</span>) ';
$gitinfo .= '[<span class="version-text-mono">' . rtrim($commit) . '</span>]';
$division = '<div id="version-panel" class="ui-widget ui-widget-content ui-corner-all">';
$division .= '<div id="bottom-bar" class="ui-widget ui-widget-header ui-corner-all">';
$division .= '<div id="version-text">';
$division .= '<span>BrewPi Remix version: ' . $gitinfo . '</span>';
$division .= '</div></div></div>';
$gitinfo = $division;
