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

include 'settings.php'; // Get default and custom settings
include 'top-bottom.php'; // Get bonjour header and version footer
include 'paths.php'; // Get correct paths
$title = 'BLR: Chamber Dashboard';
include 'html-head.php'; // Get HTML header

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
$dyn_table = '';
$dirlist = '';
$file = '';
// Establish the output variable
$dyn_table = '<table summary="Multi-Chamber Display" class="lcd-table" cellpadding="15px">';
// Get file list
$dirlist = getFileList("./", TRUE, 1);
// Sort the array
sort($dirlist);
foreach($dirlist as $file) {
    $dyn_table .= ($i % $columns == 0 ? '<tr>' . "\n" : ''); // Make a table up to $columns columns wide
    $dyn_table .= '<td>' . '<iframe src="' . $file['name'] . '" scrolling="no" width="312" height="135">';
    $dyn_table .= '<p>Your browser does not support iframes.</p></iframe>';
    $dyn_table .= '</td>';
    $i++;
    $dyn_table .= ($i % $columns == 0 ? '</tr>' : ''); // Close row after $columns
}
$dyn_table .= ($i % $columns == 0 ? '' : '</tr>'); // Close row if it has not been closed
$dyn_table .= '<tr><td></td></tr>'; // Hack to pad the bottom of lcd-panel to allow dynamic height
$dyn_table .= '</table>';

echo $header; // HTML Header
?>
<body>

<div id="lcd-logo-panel" class="ui-widget ui-widget-content ui-corner-all">
    <div id="lcd-top-bar" class="ui-widget ui-widget-header ui-corner-all">
    	<div id="lcd-logo-container">
    		<div id=lcd-name-container>
          <?= $logo; ?> <!-- Logo -->
          <span class="lcd-title">Multi-Chamber Dashboard</span>
    		</div>
    	</div>
    </div>
</div>

<div id="lcd-panel" class="ui-widget ui-widget-content ui-corner-all">
    <?php echo $dyn_table; ?>
</div>

<?= $gitinfo ?> <!-- Git info footer -->

</body>
</html>
