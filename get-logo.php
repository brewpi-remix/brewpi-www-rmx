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

if (isset($chamberLoc)) {
    // If we are running as multi-index.php, be sure to prepend a chamber path
    // It will not read as a multi-chamber otherwise
    $logo = $chamberLoc . "/" . $logo;
}

if (file_exists($logo)) {
    // Get some additional information about the logo file
    $filemime = mime_content_type($logo);
    $fileext = pathinfo($logo, PATHINFO_EXTENSION);
    // Check to make sure the logo is a valid image
    if (in_array ($filemime, $mimetypes) && in_array ("." . $fileext, $mimeexts)) {
        // Use chamber path as link on logo if we are multi-chamber
        $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        $logo_code = (isset($chamberLoc) ? '<a href="' . $root . '"><img class="logo" src="' . $logo . '"></a>' : '<img class="logo" src="' . $logo . '">');
        // Logo will be resized by CSS
        $displayLogo = $logo_code;
    } else {
        $displayLogo = $logo . " is invalid";
        error_log("BrewPi: " . $logo . " is an invalid image.");
    }
} else {
    $displayLogo = "BrewPi: Missing " . $logo . ".";
    error_log("BrewPi: " . $logo . " not found.");
}

echo $displayLogo;
echo "\n";

?>
