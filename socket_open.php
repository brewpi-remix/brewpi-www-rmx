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

require_once('configuration.php');

function open_socket()
{
    $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');
    $useInetSocket = getConfig("useInetSocket", $isWindows);
    if ($useInetSocket)
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    else
        $sock = socket_create(AF_UNIX, SOCK_STREAM, 0);

    if (!($sock === false))
    {
        if(
            ((!$useInetSocket) && socket_connect($sock, "$GLOBALS[scriptPath]/BEERSOCKET"))
            || (($useInetSocket) && socket_connect($sock, getConfig("scriptAddress", "localhost"),
            getConfig("scriptPort",6332)))
            )
        {
            socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 15, 'usec' => 0));
        }
        else{
            socket_close($sock);
            if (getConfig('debug', false))
				echo "Socket connection failed: " . socket_strerror(socket_last_error($sock));
		}
	}
	return $sock;
}
