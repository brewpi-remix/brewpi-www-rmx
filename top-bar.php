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
			<span>Logging: </span>
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
	<div id="new-status" class="new-status-display">
		<span class="new-status-text">
		<table cellpadding="0">
				<tbody>
					<tr>
						<td class="new-status-line" id="new-status-item-0"></td><td class="new-status-line" id="new-status-value-0"></td>
					</tr>
					<tr>
						<td class="new-status-line" id="new-status-item-1"></td><td class="new-status-line" id="new-status-value-1"></td>
					</tr>
					<tr>
						<td class="new-status-line" id="new-status-item-2"></td><td class="new-status-line" id="new-status-value-2"></td>
					</tr>
					<tr>
						<td class="new-status-line" id="new-status-item-3"></td><td class="new-status-line" id="new-status-value-3"></td>
					</tr>
				</tbody>
			</table>
		</span>
	</div>
	<button class="script-status ui-state-error"></button>
	<button id="maintenance" class="ui-state-default">Maintenance panel</button>
</div>