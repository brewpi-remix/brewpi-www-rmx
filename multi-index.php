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

$columns = 3; // Number of columns in display table
$chamberLoc = "";

function getFileList($dir, $recurse = FALSE, $depth = FALSE)
{
    $retval = [];

    // Add trailing slash if missing
    if (substr($dir, -1) != "/") {
        $dir .= "/";
    }

    // Open pointer to directory and read list of files
    $d = @dir($dir) or die("getFileList: Failed opening directory {$dir} for reading");
    while (FALSE !== ($entry = $d->read())) {
        // Skip hidden files
        if ($entry{0} == ".") continue;
        if (is_dir("{$dir}{$entry}")) {
            // Skip directory;
            if ($recurse && is_readable("{$dir}{$entry}/")) {
                if ($depth === FALSE) {
                    $retval = array_merge($retval, getFileList("{$dir}{$entry}/", TRUE));
                } elseif($depth > 0) {
                    $retval = array_merge($retval, getFileList("{$dir}{$entry}/", TRUE, $depth-1));
                }
            }
        } elseif(is_readable("{$dir}{$entry}")) {
            if ($entry == 'lcd.php') {
                $retval[] = [
                    'name' => "{$dir}{$entry}",
                ];
            }
        }
    }
    $d->close();
    return $retval;
}

function htmlpath($relative_path) {
    $realpath=realpath($relative_path);
    $htmlpath=str_replace($_SERVER['DOCUMENT_ROOT'],'',$realpath);
    return $htmlpath;
}

// Create table of frames
$i = 0;
// Establish the output variable
$dyn_table = '<table summary="Multi-Chamber Display" cellpadding="5px">';
// Get file list
$dirlist = getFileList("./", TRUE, 1);
// Sort the array
sort($dirlist);
// Get a chamber location
$chamberLoc = htmlpath(pathinfo($dirlist[0]['name'])['dirname']);
$chamberLoc = ltrim($chamberLoc,'/');

foreach($dirlist as $file) {
    $dyn_table .= ($i % $columns == 0 ? '<tr>' . "\n" : ''); // Make a table up to $columns columns wide
    $dyn_table .= '<td>' . '<iframe src="' . $file['name'] . '" scrolling="no" width="312">' . "\n";
    $dyn_table .= '<p>Your browser does not support iframes.</p></iframe>' . "\n";
    $dyn_table .= '</td>' . "\n";
    $i++;
    $dyn_table .= ($i % $columns == 0 ? '</tr>' . "\n" : ''); // Close row after $columns
}
$dyn_table .= ($i % $columns == 0 ? '' : '</tr>' . "\n"); // Close row if it has not been closed
$dyn_table .= '<tr><td></td></tr>' . "\n"; // Hack to pad the bottom of lcd-panel to allow dynamic height
$dyn_table .= '</table>';

$title = 'BLR: Chamber Dashboard';

?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title><?php echo $title; ?></title>
    <link type="text/css" href="<?php echo $chamberLoc . '/'; ?>css/redmond/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo $chamberLoc . '/'; ?>css/style.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo $chamberLoc . '/'; ?>css/multi-index.css" rel="stylesheet" />
    <link rel="apple-touch-icon" href="<?php echo $chamberLoc . '/'; ?>images/touch-icon-iphone.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $chamberLoc . '/'; ?>images/touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $chamberLoc . '/'; ?>images/touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $chamberLoc . '/'; ?>images/touch-icon-ipad-retina.png">
    <meta name="apple-mobile-web-app-title" content="<?php echo $title; ?>">
    <link rel="icon" type="image/png" href="<?php echo $chamberLoc . '/'; ?>images/favicon.ico">
</head>

<body>

<div id="not-multi-index" style='display:none'>This page is intended to be viewed as part of a multi-chamber index.</div>

<div id="multi-index" style='display:none'>

    <?php include 'get-bjprompt.php'; ?>

    <div id="beer-panel" class="ui-widget ui-widget-content ui-corner-all">
            <div id="top-bar" class="grid-container ui-widget ui-widget-header ui-corner-all">
                <div class="m-logo"><?php include 'get-logo.php'; ?></div>
                <div class="m-title">Multi-Chamber Dashboard</div>
            </div>

            <?php echo $dyn_table; ?>
    </div>

    <?php include 'get-gitinfo.php'; ?>

</div>

<script type="text/javascript">
    if ( window.location.pathname.includes("multi-index.php") ) {
        document.getElementById("not-multi-index").style.display="block";
    } else {
        document.getElementById("multi-index").style.display="block";
    }
</script>

</body>
</html>
