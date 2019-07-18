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

$header = '';
$header .= '<!DOCTYPE html >';
$header .= '<html>';
$header .= '<head>';
$header .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
$header .= '<title>' . $title . '</title>';
$header .= '<link rel="stylesheet" type="text/css" href="' . $css . '/redmond/jquery-ui-1.10.3.custom.css" />';
$header .= '<link rel="stylesheet" type="text/css" href="' . $css . '/style.css" />';
$header .= $lcdcss;
$header .= '<link rel="apple-touch-icon" href="' . $imgloc . '/touch-icon-iphone.png">';
$header .= '<link rel="apple-touch-icon" sizes="76x76" href="' . $imgloc . '/touch-icon-ipad.png">';
$header .= '<link rel="apple-touch-icon" sizes="120x120" href="' . $imgloc . '/touch-icon-iphone-retina.png">';
$header .= '<link rel="apple-touch-icon" sizes="152x152" href="' . $imgloc . '/touch-icon-ipad-retina.png">';
$header .= '<meta name="apple-mobile-web-app-capable" content="yes" />';
$header .= '<meta name="apple-mobile-web-app-title" content="' . $title . '">';
$header .= '<link rel="icon" type="image/png" href="' . $imgloc . '/favicon.ico">';
$header .= '<base target="_parent">';
$header .= '</head>';
$header .= '<body>';
