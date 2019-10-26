/* Copyright (C) 2019 Lee C. Bussy (@LBussy)
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
 * Legacy branch.*/

$(function () {
    // Get the form
    var form = $('#message-send');

    // Get the messages div
    var formMessages = $('#socket-messages');

    // Set up an event listener for the contact form
    $(form).submit(function (e) {
        // Stop the browser from submitting the form
        e.preventDefault();

        // Check against valid commands
        var text = $('#messageType').val();
        if (!checkValue(text, validCommands)) {
            alert('Invalid message type: "' + text + '"');
            clearForm();
            return;
        }

        // Serialize the form data
        var formData = $(form).serialize();

        // Submit the form using AJAX
        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData
        })
            .done(function (response) { // Success
                // Make sure that the formMessages div has the green 'success' class
                $(formMessages).removeClass('error');
                $(formMessages).addClass('success');

                // Set the message text
                $(formMessages).text(response);

                // Clear the entries
                document.getElementById("message-send").reset();
                //$('#messageType').reset();
                //$('#message').reset();
            })
            .fail(function (data) { // Failure
                // Make sure that the formMessages div has the red 'error' class
                $(formMessages).removeClass('success');
                $(formMessages).addClass('error');

                // Set the message text.
                if (!$.trim(data.responseText)) {
                    $(formMessages).text('An undefined error occurred and your message could not be sent.');
                } else {
                    $(formMessages).text(data.responseText);
                }
            })
            .always(function () { // Do every submission
                //
            });
    });
});

// Available BrewPi commands
var validCommands = [
    // Simple commands (no arguments)
    "ack",
    "getBeer",
    "getControlConstants",
    "getControlSettings",
    "getControlVariables",
    "getDeviceList",
    "getFridge",
    "getMode",
    "getVersion",
    "lcd",
    "loadDefaultControlConstants",
    "loadDefaultControlSettings",
    "refreshControlConstants",
    "refreshControlSettings",
    "refreshControlVariables",
    "refreshDeviceList",
    // Action commands
    "eraseLogs",
    "pauseLogging",
    "quit",
    "resetController",
    "resumeLogging",
    "stopLogging",
    "stopScript",
    // Commands which require arguments
    "api",
    "applyDevice",
    "dateTimeFormatDisplay",
    "interval",
    "programArduino",
    "programController",
    "setActiveProfile",
    "setBeer",
    "setFridge",
    "setOff",
    "setParameters",
    "startNewBrew",
    "writeDevice"
];


// Check if value exists in array
function checkValue(value, arr) {
    var status = false;
    for (var i = 0; i < arr.length; i++) {
        var name = arr[i];
        if (name == value) {
            status = true;
            break;
        }
    }
    return status;
}

// Clear the form elements and reset timer
function clearForm() {
    document.getElementById("message-send").reset();
    resetResults();
}

// Reset the results division
function resetResults() {
    var formMessages = $('#socket-messages');
    $(formMessages).removeClass('success');
    $(formMessages).removeClass('error');
    $(formMessages).text('');
}
