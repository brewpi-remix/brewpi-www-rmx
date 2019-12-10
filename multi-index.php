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

  // Add trailing slash if missing
  if(substr($dir, -1) != "/") {
    $dir .= "/";
  }

  // Open pointer to directory and read list of files
  $d = @dir($dir) or die("getFileList: Failed opening directory {$dir} for reading");
  while(FALSE !== ($entry = $d->read())) {
    // Skip hidden files
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

// Create table of frames
$i = 0;
// Establish the output variable
$dyn_table = '<table summary="Multi-Chamber Display" class="lcd-table" cellpadding="5px">';
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
$dyn_table .= ($i % $columns == 0 ? '' : '</tr>' . "\n"); // Close row if it has not been closed
$dyn_table .= '<tr><td></td></tr>' . "\n"; // Hack to pad the bottom of lcd-panel to allow dynamic height
$dyn_table .= '</table>';

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
$docloc = str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname($scriptPath));
$tbwd = getcwd();
chdir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['docloc']);
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
$division = "<div id=\"version-panel\" class=\"ui-widget ui-widget-content ui-corner-all\">\r\n";
$division .= "<div id=\"bottom-bar\" class=\"ui-widget ui-widget-header ui-corner-all\">\r\n";
$division .= "<div id=\"version-text\">\r\n";
$division .= "<span>BrewPi Remix version: " . trim($version) . " (" . trim($branch) . ")</span>\r\n";
$division .= trim($commit) . "\r\n";
$division .= "</div>\r\n</div>\r\n</div>";
$gitinfo = $division;

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

<!-- Bonjour prompt bar start -->
<?php echo $bjprompt; ?>
<!-- Bonjour prompt bar end -->

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

<!-- Git version bar start -->
<?php echo $gitinfo; ?>
<!-- Git version bar end -->

</body>
</html>
