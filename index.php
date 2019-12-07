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

// load default settings from file
$defaultSettings = file_get_contents('defaultSettings.json');
if ($defaultSettings == false) die("Cannot open: defaultSettings.json");

$settingsArray = json_decode(prepareJSON($defaultSettings), true);
if (is_null($settingsArray)) die("Cannot decode: defaultSettings.json");

// overwrite default settings with user settings
if (file_exists('userSettings.json')) {
    $userSettings = file_get_contents('userSettings.json');
    if ($userSettings == false) die("Cannot open: userSettings.json");

    $userSettingsArray = json_decode(prepareJSON($userSettings), true);
    if (is_null($settingsArray)) die("Cannot decode: userSettings.json");

    foreach ($userSettingsArray as $key => $value) {
        $settingsArray[$key] = $userSettingsArray[$key];
    }
}

$beerName = $settingsArray["beerName"];
$tempFormat = $settingsArray["tempFormat"];
$profileName = $settingsArray["profileName"];
$dateTimeFormat = $settingsArray["dateTimeFormat"];
$dateTimeFormatDisplay = $settingsArray["dateTimeFormatDisplay"];

function prepareJSON($input)
{
    //This will convert ASCII/ISO-8859-1 to UTF-8.
    //Be careful with the third parameter (encoding detect list), because
    //if set wrong, some input encodings will get garbled (including UTF-8!)
    $input = mb_convert_encoding($input, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');
    //Remove UTF-8 BOM if present, json_decode() does not like it.
    if (substr($input, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) $input = substr($input, 3);
    return $input;
}

// Read configuration name for multi-chamber
$chamber = '';
if (file_exists('config.php')) {
    require_once('config.php');
    if (file_exists($scriptPath . "/settings/config.cfg")) {
        $ini_array = parse_ini_file($scriptPath . "/settings/config.cfg");
        $chamber = $ini_array['chamber'];
    }
} else {
    die('ERROR: Unable to open required file (config.php).');
}

// Git information for footer
$docloc = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($scriptPath));
$tbwd = getcwd();
if (is_dir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['docloc'])) {
    chdir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['docloc']);
}
$version = trim(shell_exec('git describe --tags $(git rev-list --tags --max-count=1)'));
$branch = trim(shell_exec('git branch | grep \* | cut -d " " -f2'));
$commit = trim(shell_exec('git -C . log --oneline -n1'));
chdir($tbwd);
$arr = explode(' ', trim($commit));
$commit = "[ <span class=\"version-text-mono\">";
$loop = '';
foreach ($arr as $key => $word) {
    if ($key == 0) { // Make commit hash yellow
        $loop = "<span class=\"version-text-monoylw\">" . $word . "</span> - ";
    } else {
        $loop .= $word . " ";
    }
}
$commit .= trim($loop) . "</span> ]";
$division = "<div id=\"version-panel\" class=\"ui-widget ui-widget-content ui-corner-all\" style=\"display:none\">\r\n";
$division .= "<div id=\"bottom-bar\" class=\"ui-widget ui-widget-header ui-corner-all\">\r\n";
$division .= "<div id=\"version-text\">\r\n";
$division .= "<span>BrewPi Remix version: " . trim($version) . " (" . trim($branch) . ")</span>\r\n";
$division .= trim($commit) . "\r\n";
$division .= "</div>\r\n</div>\r\n</div>";
$gitinfo = $division;

// See if we are using an IP to access page, and/or if user is on Windows
$ipurl = (filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_IP) ? true : false);
$windows = (preg_match('/windows|win32/i', $_SERVER['HTTP_USER_AGENT']) ? true : false);
// Form URL with host name
$named_url = 'http://' . gethostname() . '.local' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Bonjour prompt
$bjprompt = '';
if ($ipurl && $windows) {
    $bjprompt .= "<div id=\"bonjour-panel\" class=\"ui-widget ui-widget-content ui-corner-all\">\r\n";
    $bjprompt .= "<div id=\"top-bar\" class=\"ui-widget ui-widget-header ui-corner-all\">\r\n";
    $bjprompt .= "<a href=\"https://support.apple.com/kb/DL999\">\r\n";
    $bjprompt .= "<img style=\"float: left;\" src=\"images/bonjour.png\" alt=\"Bonjour icon\" width=\"43\" /></a>\r\n";
    $bjprompt .= "<p>&nbsp;You are using an IP to access your BrewPi.\r\n";
    $bjprompt .= "You can use <a href=\"" . $named_url . "\">" . $named_url . "</a> instead\r\n";
    $bjprompt .= "if you install <a href=\"https://support.apple.com/kb/DL999\">Bonjour from Apple</a>.\r\n";
    $bjprompt .= "</div>\r\n</div>";
}

$title = ($chamber == '' ? 'BrewPi Remix' : 'BLR: ' . $chamber)

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo $title; ?></title>
    <link type="text/css" href="css/redmond/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
    <link type="text/css" href="css/style.css" rel="stylesheet" />
    <link type="text/css" href="css/tilt.css" rel="stylesheet" />
    <link rel="apple-touch-icon" href="images/touch-icon-iphone.png">
    <link rel="apple-touch-icon" sizes="76x76" href="images/touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="images/touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="images/touch-icon-ipad-retina.png">
    <meta name="apple-mobile-web-app-title" content="<?php echo $title; ?>">
    <link rel="icon" type="image/png" href="images/favicon.ico">
</head>

<body>

    <!-- Bonjour prompt bar start -->
    <?php echo $bjprompt; ?>
    <!-- Bonjour prompt bar end -->

    <div id="beer-panel" class="ui-widget ui-widget-content ui-corner-all" style="display:none">
        <?php include 'top-bar.php'; ?>
        <?php include 'beer-panel.php'; ?>
    </div>
    <div id="control-panel" style="display:none">
        <?php include 'control-panel.php'; ?>
    </div>
    <div id="maintenance-panel" style="display:none">
        <?php include 'maintenance-panel.php'; ?>
    </div>

    <!-- Git version bar start -->
    <?php echo $gitinfo; ?>
    <!-- Git version bar end -->

    <!-- Load scripts after the body, so they don't block rendering of the page -->
    <!-- <script type="text/javascript" src="js/jquery-1.11.0.js"></script> -->
    <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
    <script type="text/javascript" src="js/spin.js"></script>

    <script type="text/javascript" src="js/dygraph-combined.js"></script>
    <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.js"></script> -->
    <!-- <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.css" /> -->

    <script type="text/javascript">
        // pass parameters to JavaScript
        window.tempFormat = <?php echo "'$tempFormat'" ?>;
        window.beerName = <?php echo "\"$beerName\"" ?>;
        window.profileName = <?php echo "\"$profileName\"" ?>;
        window.dateTimeFormat = <?php echo "\"$dateTimeFormat\"" ?>;
        window.dateTimeFormatDisplay = <?php echo "\"$dateTimeFormatDisplay\"" ?>;
    </script>
    <script type="text/javascript" src="js/main.js"></script>
    <script type="text/javascript" src="js/device-config.js"></script>
    <script type="text/javascript" src="js/control-panel.js"></script>
    <script type="text/javascript" src="js/maintenance-panel.js"></script>
    <script type="text/javascript" src="js/beer-chart.js"></script>
    <script type="text/javascript" src="js/profile-table.js"></script>

</body>

</html>