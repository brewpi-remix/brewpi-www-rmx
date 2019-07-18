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

include 'settings.php'; // Get default and custom settings
include 'top-bottom.php'; // Get bonjour header and version footer
include 'paths.php'; // Get correct paths
$title = ($chamber=='' ? 'BrewPi Remix' : 'BLR: ' . $chamber);
include 'html-head.php'; // Get HTML header

echo $header; // HTML Header
?>
<body>

<?php echo $bjprompt; ?><!-- Bonjour prompt -->

<div id="beer-panel" class="ui-widget ui-widget-content ui-corner-all">
    <?php include 'beer-panel.php'; ?>
</div>
<div id="control-panel" style="display:none"> <!--// hide while loading -->
    <?php include 'control-panel.php'; ?>
</div>
<div id="maintenance-panel" style="display:none"> <!--// hide while loading -->
    <?php include 'maintenance-panel.php'; ?>
</div>

<!-- Load scripts after the body, so they don't block rendering of the page -->
<!-- <script type="text/javascript" src="js/jquery-1.11.0.js"></script> -->
<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="js/spin.js"></script>
<script type="text/javascript" src="js/dygraph-combined.js"></script>
<script type="text/javascript">
    // pass parameters to JavaScript
    window.tempFormat = <?php echo "'$tempFormat'" ?>;
    window.beerName = <?php echo "\"$beerName\""?>;
    window.profileName = <?php echo "\"$profileName\""?>;
    window.dateTimeFormat = <?php echo "\"$dateTimeFormat\""?>;
    window.dateTimeFormatDisplay = <?php echo "\"$dateTimeFormatDisplay\""?>;
</script>
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript" src="js/device-config.js"></script>
<script type="text/javascript" src="js/control-panel.js"></script>
<script type="text/javascript" src="js/maintenance-panel.js"></script>
<script type="text/javascript" src="js/beer-chart.js"></script>
<script type="text/javascript" src="js/profile-table.js"></script>

<?= $gitinfo ?> <!-- Git info footer -->

</body>
</html>
