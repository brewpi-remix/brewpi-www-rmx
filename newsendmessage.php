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

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>BrewPi Socket Message</title>
    <link type="text/css" href="css/socket.css" rel="stylesheet" />
    <link rel="apple-touch-icon" href="images/touch-icon-iphone.png">
    <link rel="apple-touch-icon" sizes="76x76" href="images/touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="images/touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="images/touch-icon-ipad-retina.png">
    <meta name="apple-mobile-web-app-title" content="BrewPi Socket Message">
    <link rel="icon" type="image/png" href="images/favicon.ico">
</head>

<body>
    <div id="page-wrapper">
        <h1>BrewPi Socket Message</h1>

        <form id="message-send" method="post" action="newsocketmessage.php">
            <div class="field">
                <label for="messageType">Message Type:</label>
                <input type="text" id="messageType" name="messageType" oninput="resetResults()" required>
            </div>

            <div class="field">
                <label for="message">Message:</label>
                <input type="text" id="message" name="message" oninput="resetResults()">
            </div>

            <div class="field">
                <button type="submit">Send</button>
            </div>
        </form>

        <div id="socket-messages"></div>

    </div>

    <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="js/socket.js"></script>

</body>

</html>
