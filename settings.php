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
 
 // Load default settings from file
$defaultSettings = file_get_contents('defaultSettings.json');
if ($defaultSettings == false) die("Cannot open: defaultSettings.json");
$settingsArray = json_decode(prepareJSON($defaultSettings), true);
if (is_null($settingsArray)) die("Cannot decode: defaultSettings.json");

// Overwrite default settings with user settings
if(file_exists('userSettings.json')){
    $userSettings = file_get_contents('userSettings.json');
    if($userSettings == false) die("Cannot open: userSettings.json");

    $userSettingsArray = json_decode(prepareJSON($userSettings), true);
    if(is_null($settingsArray)) die("Cannot decode: userSettings.json");

    foreach ($userSettingsArray as $key => $value) {
        $settingsArray[$key] = $userSettingsArray[$key];
    }
}

$beerName = $settingsArray["beerName"];
$tempFormat = $settingsArray["tempFormat"];
$profileName = $settingsArray["profileName"];
$dateTimeFormat = $settingsArray["dateTimeFormat"];
$dateTimeFormatDisplay = $settingsArray["dateTimeFormatDisplay"];
$chamber = $settingsArray["chamber"];
$scriptPath = $settingsArray["scriptPath"];

function prepareJSON($input) {
    // This will convert ASCII/ISO-8859-1 to UTF-8.
    // Be careful with the third parameter (encoding detect list), because
    // if set wrong, some input encodings will get garbled (including UTF-8!)
    $input = mb_convert_encoding($input, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');
    // Remove UTF-8 BOM if present, json_decode() does not like it.
    if(substr($input, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) $input = substr($input, 3);
    return $input;
}
