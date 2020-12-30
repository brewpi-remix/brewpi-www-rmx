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

// Read configuration name for multi-chamber
if (file_exists('config.php')) {
    require_once('config.php');
    if(file_exists($scriptPath . "/settings/config.cfg")) {
        $ini_array = parse_ini_file($scriptPath . "/settings/config.cfg");
    }
} else {
    die('ERROR: Unable to open required file (config.php).');
}

$beerName = $settingsArray["beerName"];
$tempFormat = $settingsArray["tempFormat"];
$profileName = $settingsArray["profileName"];
$dateTimeFormat = $settingsArray["dateTimeFormat"];
$dateTimeFormatDisplay = $settingsArray["dateTimeFormatDisplay"];
$chamberName = $ini_array['chamber'];

function prepareJSON($input) {
    // This will convert ASCII/ISO-8859-1 to UTF-8.
    // Be careful with the third parameter (encoding detect list), because
    // if set wrong, some input encodings will get garbled (including UTF-8!)
    $input = mb_convert_encoding($input, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');
    //Remove UTF-8 BOM if present, json_decode() does not like it.
    if(substr($input, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) $input = substr($input, 3);
    return $input;
}

// Get link to root of chamber
$rooturl = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']));
$title = ($chamberName == '' ? 'BrewPi Remix' : 'LCD: ' . $chamberName)

?>

<!DOCTYPE html >
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $title;?></title>
<link type="text/css" href="css/redmond/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
<link type="text/css" href="css/style.css" rel="stylesheet"/>
<link rel="apple-touch-icon" href="images/touch-icon-iphone.png">
<link rel="apple-touch-icon" sizes="76x76" href="images/touch-icon-ipad.png">
<link rel="apple-touch-icon" sizes="120x120" href="images/touch-icon-iphone-retina.png">
<link rel="apple-touch-icon" sizes="152x152" href="images/touch-icon-ipad-retina.png">
<link rel="icon" type="image/png" href="images/favicon.ico">
<meta name="apple-mobile-web-app-title" content="<?php echo $title;?>">
<base target="_parent">
</head>
<body>
<div id="lcd-portal" class="ui-widget-header ui-widget ui-widget-content ui-corner-all">
    <div id="lcd" class="portallcddisplay">
        <div class="lcd-text">
            <div class="lcd0 lcd-line" id="lcd-line-0">Waiting for</div>
            <div class="lcd1 lcd-line" id="lcd-line-1">update from</div>
            <div class="lcd2 lcd-line" id="lcd-line-2">script.</div>
            <div class="lcd3 lcd-line" id="lcd-line-3">&nbsp;</div>
        </div>
    </div>
    <div><a href="<?php echo $rooturl; ?>">Open <?php echo $chamberName; ?>'s Main Page</a></div>
</div>
<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="js/main.js"></script>

<script type="text/javascript">
    // Pass parameters to JavaScript
    window.tempFormat = <?php echo "'$tempFormat'" ?>;
    window.beerName = <?php echo "\"$beerName\""?>;
    window.profileName = <?php echo "\"$profileName\""?>;
    window.dateTimeFormat = <?php echo "\"$dateTimeFormat\""?>;
    window.dateTimeFormatDisplay = <?php echo "\"$dateTimeFormatDisplay\""?>;
</script>

</body>
</html>
