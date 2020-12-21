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

$mimetypes = array(
    "image/bmp",
    "image/cmu-raster",
    "image/fif",
    "image/florian",
    "image/g3fax",
    "image/gif",
    "image/ief",
    "image/jpeg",
    "image/jutvision",
    "image/naplps",
    "image/pict",
    "image/pjpeg",
    "image/png",
    "image/tiff",
    "image/vasa",
    "image/vnd.dwg",
    "image/vnd.fpx",
    "image/vnd.net-fpx",
    "image/vnd.rn-realflash",
    "image/vnd.rn-realpix",
    "image/vnd.wap.wbmp",
    "image/vnd.xiff",
    "image/xbm",
    "image/x-cmu-raster",
    "image/x-dwg",
    "image/x-icon",
    "image/x-jg",
    "image/x-jps",
    "image/x-niff",
    "image/x-pcx",
    "image/x-pict",
    "image/xpm",
    "image/x-portable-anymap",
    "image/x-portable-bitmap",
    "image/x-portable-graymap",
    "image/x-portable-greymap",
    "image/x-portable-pixmap",
    "image/x-quicktime",
    "image/x-rgb",
    "image/x-tiff",
    "image/x-windows-bmp",
    "image/x-xbitmap",
    "image/x-xbm",
    "image/x-xpixmap",
    "image/x-xwd",
    "image/x-xwindowdump"
);

$mimeexts = array(
    ".art",
    ".bm",
    ".bmp",
    ".dwg",
    ".dxf",
    ".fif",
    ".flo",
    ".fpx",
    ".g3",
    ".gif",
    ".ico",
    ".ief",
    ".iefs",
    ".jfif",
    ".jfif-tbnl",
    ".jpe",
    ".jpeg",
    ".jpg",
    ".jps",
    ".jut",
    ".mcf",
    ".nap",
    ".naplps",
    ".nif",
    ".niff",
    ".pbm",
    ".pct",
    ".pcx",
    ".pgm",
    ".pic",
    ".pict",
    ".pm",
    ".png",
    ".pnm",
    ".ppm",
    ".qif",
    ".qti",
    ".qtif",
    ".ras",
    ".rast",
    ".rf",
    ".rgb",
    ".rp",
    ".svf",
    ".tif",
    ".tiff",
    ".turbot",
    ".wbmp",
    ".xbm",
    ".xif",
    ".xpm",
    ".x-png",
    ".xwd"
);

// Grab the custom logo if it exists, otherwise use the stock one
$custom_logo = glob('images/custom_logo.*');
$logo = (count($custom_logo) ? $custom_logo[0] : 'images/brewpi_logo.png');
// Get site root url
$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';

?>

<div class="header-grid-container ui-widget ui-widget-header ui-corner-all" id="top-bar">

	<div class="logo">
		<?php
		if (file_exists($logo)) {
            // Get some additional information about the logo file
            $filemime = mime_content_type($logo);
            $fileext = pathinfo($logo, PATHINFO_EXTENSION);
            // Check to make sure the logo is a valid image
            if (in_array ($filemime, $mimetypes) && in_array ("." . $fileext, $mimeexts)) {
                // Use chamber path as link on logo if we are multi-chamber
                $logo_code = ($chamber == '' ? '<img class="logo" src="' . $logo . '">' : '<a href="' . $root . '"><img class="logo" src="' . $logo . '"></a>');
                // Logo will be resized by CSS
                echo $logo_code;
            } else {
                echo $logo . " is invalid";
                error_log($logo . " is an invalid image.");
            }
		} else {
            echo "Missing " . $logo;
            error_log($logo . " not found.");
		}
		?>
	</div>

	<div class="name" id="beer-name-container">
		<span>Logging: </span>
		<a href='#' id="beer-name"><?php echo urldecode($beerName); ?></a>
		<?php
		// If we are in multi-chamber, print ' in {chamber name}' after
		// the batch name
		echo ($chamber == '' ? '' : ' in ' . $chamber);
		?>
		<span class="data-logging-state"></span>
	</div>

	<div id="lcd" class="lcddisplay">
		<div class="lcd-text">
			<div class="lcd0 lcd-line" id="lcd-line-0">Waiting for</div>
			<div class="lcd1 lcd-line" id="lcd-line-1">update from</div>
			<div class="lcd2 lcd-line" id="lcd-line-2">script.</div>
			<div class="lcd3 lcd-line" id="lcd-line-3"></div>
		</div>
	</div>

	<div id="new-status" class="lcddisplay">
		<div class="scrollname0 lcd-line status-name" id="new-status-item-0"></div>
		<div class="scrollname1 lcd-line status-name" id="new-status-item-1"></div>
		<div class="scrollname2 lcd-line status-name" id="new-status-item-2"></div>
		<div class="scrollname3 lcd-line status-name" id="new-status-item-3"></div>
	</div>

	<div id="new-value" class="lcddisplay">
		<div class="scrollvalue0 lcd-line status-value" id="new-status-value-0"></div>
		<div class="scrollvalue1 lcd-line status-value" id="new-status-value-1"></div>
		<div class="scrollvalue2 lcd-line status-value" id="new-status-value-2"></div>
		<div class="scrollvalue3 lcd-line status-value" id="new-status-value-3"></div>
	</div>

	<div>
		<button class="script-status ui-state-error"></button>
		<button id="maintenance" class="ui-state-default">Maintenance panel</button>
	</div>

</div>
