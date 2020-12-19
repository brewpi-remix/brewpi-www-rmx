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
?>

<div class="header-grid-container ui-widget ui-widget-header ui-corner-all" id="top-bar">

	<div class="logo">
		<?php
			// Get site root url
			$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
			// Get logo name (use custom if it exists)
			$logo = (file_exists('images/custom_logo.png') ? 'images/custom_logo.png' : 'images/brewpi_logo.png');
			// Use link on logo if we are multi-chamber
			$logo = ($chamber == '' ? '<img class="logo" src="' . $logo . '">' : '<a href="' . $root . '"><img class="logo" src="' . $logo . '"></a>');
			echo $logo;
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
