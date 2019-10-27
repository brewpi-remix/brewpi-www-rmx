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
?>

<div id="top-bar" class="ui-widget ui-widget-header ui-corner-all">
	<div id="logo-container">
		<?php
		// Get site root url
		$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
		// Get logo name (use custom if it exists)
		$logo = (file_exists('images/custom_logo.png') ? 'images/custom_logo.png' : 'images/brewpi_logo.png');
		// Use link on logo if we are multi-chamber
		$logo = ($chamber == '' ? '<img class="logo" src="' . $logo . '">' : '<a href="' . $root . '"><img class="logo" src="' . $logo . '"></a>');
		echo $logo;
		?>
		<div id=beer-name-container>
			<span>Tracking: </span>
			<a href='#' id="beer-name"><?php echo urldecode($beerName); ?></a>
			<?php
			// If we are in multi-chamber, print ' in {chamber name}' after
			// the batch name
			echo ($chamber == '' ? '' : ' in ' . $chamber);
			?>
			<span class="data-logging-state"></span>
		</div>
	</div>
	<div id="lcd" class="lcddisplay">
		<span class="lcd-text">
			<span class="lcd-line" id="lcd-line-0">Live LCD waiting</span>
			<span class="lcd-line" id="lcd-line-1">for update from</span>
			<span class="lcd-line" id="lcd-line-2">script.</span>
			<span class="lcd-line" id="lcd-line-3"></span>
		</span>
	</div>
	<div id="lcd" class="new-status-display">
		<span class="new-status-header" id="status-line-header">Status Items:</span>
		<span class="new-status-line" id="status-line-0">_</span>
		<span class="new-status-line" id="status-line-1">_</span>
		<span class="new-status-line" id="status-line-2">_</span>
		<span class="new-status-line" id="status-line-3">_</span>
	</div>
	<button class="script-status ui-state-error"></button>
	<button id="maintenance" class="ui-state-default">Maintenance panel</button>
</div>
