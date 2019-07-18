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

// Get correct paths
$scriptPath = '';
$docloc = '';
$imgloc = '';
$custom = '';
$stock = '';
$css = '';
$logo = '';
$lcdcss = '';
list($scriptPath) = get_included_files();
$docloc = str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname($scriptPath));
$imgloc = $docloc . '/images';
$custom = $imgloc . '/custom_logo.png';
$stock = $imgloc . '/brewpi_logo.png';
$css = $docloc . '/css'; // Get css location
// Get correct logo
$logo = (file_exists($_SERVER['DOCUMENT_ROOT'] . $custom) ? $custom : $stock);
$logo = '<img class="logo" src="' . $logo . '">';
// Attach lcd.css if needed
if (basename($_SERVER['PHP_SELF']) == 'fullscreen-lcd.php') {
    $lcdcss = '<link rel="stylesheet" type="text/css" href="' . $css . '/lcd.css" />';
}