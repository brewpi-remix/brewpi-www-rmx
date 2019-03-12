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

$columns = 3; // Number of columns in display table

function getFileList($dir, $recurse = FALSE, $depth = FALSE)
{
  $retval = [];

  // add trailing slash if missing
  if(substr($dir, -1) != "/") {
    $dir .= "/";
  }

  // open pointer to directory and read list of files
  $d = @dir($dir) or die("getFileList: Failed opening directory {$dir} for reading");
  while(FALSE !== ($entry = $d->read())) {
    // skip hidden files
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

// Create table of frames
$i = 0;
// Establish the output variable
$dyn_table = '<table summary="Multi-Chamber Display" class="lcd-table">';
// Get file list
$dirlist = getFileList("./", TRUE, 1);
// Sort the array
sort($dirlist);
foreach($dirlist as $file) {
    $dyn_table .= ($i % $columns == 0 ? '<tr>' . "\n" : ''); // Make a table up to $columns columns wide
    $dyn_table .= '<td>' . '<iframe src="' . $file['name'] . '" scrolling="no" width="312" height="135">' . "\n";
    $dyn_table .= '<p>Your browser does not support iframes.</p></iframe>' . "\n";
    $dyn_table .= '</td>' . "\n";
    $i++;
    $dyn_table .= ($i % $columns == 0 ? '</tr>' . "\n" : ''); // Close row after $columns
}
$dyn_table .= ($i % $columns == 0 ? '' : '</tr>' . "\n"); // Close row if it's not been closed
$dyn_table .= '<tr><td></td></tr>' . "\n"; // Hack to pad the bottom of lcd-panel to allow dynamic height
$dyn_table .= '</table>' . "\n";

// Get correct logo
list($scriptPath) = get_included_files();
$docloc = str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname($scriptPath));
$imgloc = $docloc . '/images';
$custom = $imgloc . '/custom_logo.png';
$stock = $imgloc . '/brewpi_logo.png';
$css = $docloc . '/css'; // Get css location
$logo = (file_exists($_SERVER['DOCUMENT_ROOT'] . $custom) ? $custom : $stock);
$logo = '<img class="logo" src="' . $logo . '">';

// Git information for footer
$pwd = getcwd();
chdir(dirname($scriptPath));
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
chdir($pwd);

$title = 'BLR: Chamber Dashboard';

?>
<!DOCTYPE html >
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<link type="text/css" href="<?php echo $css ?>/redmond/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?php echo $css ?>/style.css" />
<link rel="apple-touch-icon" href="<?php echo $imgloc . '/'; ?>touch-icon-iphone.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $imgloc . '/'; ?>touch-icon-ipad.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $imgloc . '/'; ?>touch-icon-iphone-retina.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $imgloc . '/'; ?>touch-icon-ipad-retina.png">
<meta name="apple-mobile-web-app-title" content="<?php echo $title; ?>">
<link rel="icon" type="image/png" href="<?php echo $imgloc . '/'; ?>favicon.ico">
</head>
<body>

<div id="lcd-logo-panel" class="ui-widget ui-widget-content ui-corner-all">
    <div id="lcd-top-bar" class="ui-widget ui-widget-header ui-corner-all">
    	<div id="lcd-logo-container">
    		<div id=lcd-name-container>
                <?php echo $logo; ?>
                <span class="lcd-title">Multi-Chamber Dashboard</span>
    		</div>
    	</div>
    </div>
</div>

<div id="lcd-panel" class="ui-widget ui-widget-content ui-corner-all">
    <?php echo $dyn_table; ?>
</div>

<div id="lcd-version-panel" class="ui-widget ui-widget-content ui-corner-all">
    <div id="lcd-bottom-bar" class="ui-widget ui-widget-header ui-corner-all">
        <div id="version-text">
            <span>BrewPi Legacy Remix version: <?php echo $gitinfo; ?></span>
        </div>
    </div>
</div>

</body>
</html>
