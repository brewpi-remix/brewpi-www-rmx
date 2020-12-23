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

/* global google, loadControlPanel, drawBeerChart */

var prevScriptStatus = -1;
var controlConstants = {};
var controlSettings = {};
var controlVariables = {};
var lastStatus = 0;

// Determine if we are in a frame or on an LCD page
function inIframe() {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}

function isLCD() {
    var path = window.location.pathname;
    var pageName = path.split("/").pop();
    // if (typeof pageName !== 'undefined') {var pageName = "index.php"}
    if (pageName == "lcd.php" || pageName == "fullscreen-lcd.php" || inIframe()) {
        return true;
    }
}

function receiveControlConstants() {
    "use strict";
    $.ajax({
        type: "POST",
        dataType: "json",
        cache: false,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        data: { messageType: "getControlConstants", message: "" },
        url: 'socketmessage.php',
        success: function (controlConstantsJSON) {
            window.controlConstants = controlConstantsJSON;
            for (var i in window.controlConstants) {
                if (window.controlConstants.hasOwnProperty(i)) {
                    if ($('select[name="' + i + '"]').length) {
                        $('select[name="' + i + '"]').val(window.controlConstants[i]);
                    }
                    if ($('input[name="' + i + '"]').length) {
                        $('input[name="' + i + '"]').val(window.controlConstants[i]);
                    }
                    $('.cc.' + i + ' .val').text(window.controlConstants[i]);
                }
            }
        }
    });
}

function receiveControlSettings(callback) {
    "use strict";
    $.ajax({
        type: "POST",
        dataType: "json",
        cache: false,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        url: 'socketmessage.php',
        data: { messageType: "getControlSettings", message: "" },
        success: function (controlSettingsJSON) {
            window.controlSettings = controlSettingsJSON;
            for (var i in controlSettings) {
                if (controlSettings.hasOwnProperty(i)) {
                    if ($('select[name="' + i + '"]').length) {
                        $('select[name="' + i + '"]').val(window.controlSettings[i]);
                    }
                    if ($('input[name="' + i + '"]').length) {
                        $('input[name="' + i + '"]').val(window.controlSettings[i]);
                    }
                    $('.cs.' + i + ' .val').text(window.controlSettings[i]);
                }
            }
            if (typeof (controlSettings.dataLogging) !== 'undefined') {
                var $loggingState = $("span.data-logging-state");
                var $beerName = $("#beer-name");
                if (controlSettings.dataLogging === 'paused') {
                    $loggingState.text("(paused)");
                    $loggingState.show();
                }
                else if (controlSettings.dataLogging === 'stopped') {
                    $beerName.text("None");
                    $loggingState.text("(stopped)");
                    $loggingState.show();
                }
                else {
                    $loggingState.hide();
                }
            }
            // execute optional callback function
            if (callback && typeof (callback) === "function") {
                callback();
            }
        }
    });
}

function receiveControlVariables() {
    "use strict";
    $.ajax({
        type: "POST",
        dataType: "json",
        cache: false,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        url: 'socketmessage.php',
        data: { messageType: "getControlVariables", message: "" },
        success: function (controlVariablesJSON) {
            window.controlVariables = controlVariablesJSON;
            for (var i in window.controlVariables) {
                if (window.controlVariables.hasOwnProperty(i)) {
                    $('.cv.' + i + ' .val').text(window.controlVariables[i]);
                }
            }
            $('.cv.pid-result .val').text(Math.round(1000 * (window.controlVariables.p + window.controlVariables.i + window.controlVariables.d)) / 1000);
        }
    });
}

function loadDefaultControlSettings() {
    "use strict";
    $.post('socketmessage.php', { messageType: "loadDefaultControlSettings", message: "" }, function () {
        receiveControlSettings();
    });
}

function loadDefaultControlConstants() {
    "use strict";
    $.post('socketmessage.php', { messageType: "loadDefaultControlConstants", message: "" }, function () {
        receiveControlConstants();
    });
}

function reloadControlConstantsFromArduino() {
    "use strict";
    $.post('socketmessage.php', { messageType: "refreshControlConstants", message: "" }, function () {
        receiveControlConstants();
    });
}

function reloadControlSettingsFromArduino() {
    "use strict";
    $.post('socketmessage.php', { messageType: "refreshControlSettings", message: "" }, function () {
        receiveControlSettings();
    });
}

function reloadControlVariablesFromArduino() {
    "use strict";
    $.post('socketmessage.php', { messageType: "refreshControlVariables", message: "" }, function () {
        receiveControlVariables();
    });
}

function stopScript() {
    "use strict";
    $.post('socketmessage.php', { messageType: "stopScript", message: "" }, function () { });
}

function startScript() {
    "use strict";
    $.get('start_script.php');
}

function refreshLcd() {
    "use strict";
    $.ajax({
        type: "POST",
        dataType: "json",
        cache: false,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        url: 'socketmessage.php',
        data: { messageType: "lcd", message: "" }
    })
        .done(function (lcdText) {
            var $lcdText = $('#lcd .lcd-text');
            for (var i = lcdText.length - 1; i >= 0; i--) {
                $lcdText.find('#lcd-line-' + i).html(lcdText[i]);
            }
            updateScriptStatus(true);
        })
        .fail(function () {
            var $lcdText = $('#lcd .lcd-text');
            $lcdText.find('#lcd-line-0').html("Cannot connect");
            $lcdText.find('#lcd-line-1').html("to script");
            $lcdText.find('#lcd-line-2').html(" ");
            $lcdText.find('#lcd-line-3').html(" ");
            updateScriptStatus(false);
        })
        .always(function () {
            window.setTimeout(refreshLcd, 5000);
        }
        );
}

function refreshStatus() {
    "use strict";
    var numRows = 4;
    $.ajax({
        type: "POST",
        dataType: "json",
        cache: false,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        url: 'socketmessage.php',
        data: { messageType: "statusText", message: "" }
    })
        .done(function (data) {
            var $newStatusText = $('#new-status');
            var $newValueText = $('#new-value');
            // Display statuses
            var curRow = 0
            var numKeys = Object.keys(data).length;
            var dataName;
            var dataItem;
            if (numKeys <= numRows) {
                window.lastStatus = 0;
            }
            for (var i = window.lastStatus; i < (window.lastStatus + numKeys); i++) {
                var row = data[i % numKeys];
                for (var item in row) {
                    var keys = Object.keys(row);
                    dataName = keys[0];
                    if (item.indexOf("SG") > -1) {
                        dataItem = parseFloat(row[item]).toFixed(3);
                    } else {
                        dataItem = row[item];
                    }
                    $newStatusText.find('#new-status-item-' + curRow).html(dataName);
                    $newValueText.find('#new-status-value-' + curRow).html(dataItem);
                }
                curRow++;
            }
            window.lastStatus++;
            // Clear the rest of the statuses
            for (var i = curRow; i < numRows; i++) {
                $newStatusText.find('#new-status-item-' + i).html("");
                $newValueText.find('#new-status-value-' + i).html("");
            }
        })
        .fail(function () {
            var $newStatusText = $('#new-status');
            var $newValueText = $('#new-value');
            for (var i = 0; i < 4; i++) {
                $newStatusText.find('#new-status-item-' + i).html("");
                $newValueText.find('#new-status-value-' + i).html("");
            }
        })
    setTimeout(refreshStatus, 10000);
}

function updateScriptStatus(running) { // TODO:  Make a "Starting" status
    "use strict";
    if (window.scriptStatus == running) {
        return;
    }
    window.scriptStatus = running;
    var $scriptStatus = $(".script-status");
    var $scriptStatusIcon = $scriptStatus.find("span.ui-icon");
    var $scriptStatusButtonText = $scriptStatus.find("span.ui-button-text");
    if (running) {
        $scriptStatusIcon.removeClass("ui-icon-alert").addClass("ui-icon-check");
        $scriptStatus.removeClass("ui-state-error").addClass("ui-state-default");
        $scriptStatusButtonText.text("Script running");
        $scriptStatus.unbind();
        $scriptStatus.bind({
            click: function () {
                stopScript();
            },
            mouseenter: function () {
                $scriptStatusIcon.removeClass("ui-icon-check").addClass("ui-icon-stop");
                $scriptStatus.removeClass("ui-state-default").addClass("ui-state-error");
                $scriptStatusButtonText.text("Stop script");
            },
            mouseleave: function () {
                $scriptStatusIcon.removeClass("ui-icon-stop").addClass("ui-icon-check");
                $scriptStatus.removeClass("ui-state-error").addClass("ui-state-default");
                $scriptStatusButtonText.text("Script running");
            }
        });
    } else {
        $scriptStatusIcon.removeClass("ui-icon-check").addClass("ui-icon-alert");
        $scriptStatus.removeClass("ui-state-default").addClass("ui-state-error");
        $scriptStatusButtonText.text("Script not running!");
        $scriptStatus.unbind();
        $scriptStatus.bind({
            click: function () {
                startScript();
            },
            mouseenter: function () {
                $scriptStatusIcon.removeClass("ui-icon-alert").addClass("ui-icon-play");
                $scriptStatus.removeClass("ui-state-error").addClass("ui-state-default");
                $scriptStatusButtonText.text("Start script");
            },
            mouseleave: function () {
                $scriptStatusIcon.removeClass("ui-icon-play").addClass("ui-icon-alert");
                $scriptStatus.removeClass("ui-state-default").addClass("ui-state-error");
                $scriptStatusButtonText.text("Script not running!");
            }
        });
    }
}

function beerNameDialogInit() {
    "use strict";
    var $dialog = $("<div class='beer-name-dialog'></div>").dialog({
        modal: true,
        title: "Switch active brew",
        width: 480
    });
    var $backButton = $("<button class='back' title='Go back'></button>").button({ icons: { primary: "ui-icon-arrowthick-1-w" }, text: false });
    $dialog.append($backButton);
    var $body = $("<div class='dialog-body'></div>");
    $dialog.append($body);
    beerNameDialogStart($body, $backButton);
}

function beerNameDialogStart($body, $backButton) {
    "use strict";
    $body.empty();
    $backButton.hide();
    var beerName = $("#beer-name").text();
    var introText = "";

    var stopButton = true;
    var pauseButton = true;
    var continueButton = true;

    var dataLogging = 'undefined';
    if (typeof (window.controlSettings.dataLogging) !== 'undefined') {
        dataLogging = window.controlSettings.dataLogging;
    }
    if (dataLogging === 'stopped') {
        introText += "You are currently not logging data.";
        stopButton = false;
        pauseButton = false;
        continueButton = false;
    }
    else if (dataLogging === 'paused') {
        introText += "You have temporarily disabled data logging for the brew '" + beerName + "'.";
        pauseButton = false;
    }
    else if (dataLogging === 'active') {
        introText += "You are currently logging data for the brew '" + beerName + "'.";
        continueButton = false;
    }
    else {
        introText += "You are logging data for brew '" + beerName + "'.";
    }

    $body.append($("<span  class='dialog-intro'>" + introText + "<br>What would you like to do?</span>"));
    var $buttons = $("<div class='beer-name-buttons'></div>");
    $buttons.append($("<button>Start new brew</button>").button({ icons: { primary: "ui-icon-plus" } }).click(function () {
        beerNameDialogNew($body, $backButton);
    }));
    if (stopButton) {
        $buttons.append($("<button>Stop this brew</button>").button({ icons: { primary: "ui-icon-stop" } }).click(function () {
            beerNameDialogStop($body, $backButton);
        }));
    }
    if (pauseButton) {
        $buttons.append($("<button>Pause logging</button>").button({ icons: { primary: "ui-icon-pause" } }).click(function () {
            beerNameDialogPause($body, $backButton);
        }));
    }
    if (continueButton) {
        $buttons.append($("<button>Continue logging</button>").button({ icons: { primary: "ui-icon-play" } }).click(function () {
            beerNameDialogResume($body, $backButton);
        }));
    }
    $body.append($buttons);
}

function beerNameDialogNew($body, $backButton) {
    "use strict";
    $body.empty();
    $body.append($("<span  class='dialog-intro'>Please enter a name for your new brew. Your current brew will be stopped and BrewPi will start logging data for your new brew.</span>"));
    $body.append($("<input id='new-beer-name' type='text' size='30' placeholder='Enter new beer name..' autofocus> </input>"));
    document.getElementById('new-beer-name').focus();
    var $buttons = $("<div class='beer-name-buttons'></div>");
    $buttons.append($("<button>Start new brew</button>").button({ icons: { primary: "ui-icon-check" } }).click(function () {
        $.post('socketmessage.php', { messageType: "startNewBrew", message: encodeURIComponent($("input#new-beer-name").val()) }, function (reply) {
            $backButton.show().unbind().bind({ click: function () { beerNameDialogNew($body, $backButton); } });
            beerNameDialogResult($body, $backButton, reply);
        });
    }));
    $body.append($buttons);
    $backButton.show().unbind().bind({ click: function () { beerNameDialogStart($body, $backButton); } });
}

function beerNameDialogStop($body, $backButton) {
    "use strict";
    $body.empty();
    $body.append($("<span  class='dialog-intro'>Clicking stop will finish your current brew and will stop logging data. You can use this when you are between brews.</span>"));

    var $buttons = $("<div class='beer-name-buttons'></div>");
    $buttons.append($("<button>Stop this brew</button>").button({ icons: { primary: "ui-icon-stop" } }).click(function () {
        $.post('socketmessage.php', { messageType: "stopLogging", message: "" }, function (reply) {
            $backButton.show().unbind().bind({ click: function () { beerNameDialogStop($body, $backButton); } });
            receiveControlSettings();
            beerNameDialogResult($body, $backButton, reply);
        });
    }));
    $backButton.show().unbind().bind({ click: function () { beerNameDialogStart($body, $backButton); } });
    $body.append($buttons);
}

function beerNameDialogPause($body, $backButton) {
    "use strict";
    $body.empty();
    $body.append($("<span  class='dialog-intro'>Clicking the button below will temporarily disable data logging for this brew. You can later continue logging data for the same brew.</span>"));

    var $buttons = $("<div class='beer-name-buttons'></div>");
    $buttons.append($("<button>Pause logging temporarily</button>").button({ icons: { primary: "ui-icon-pause" } }).click(function () {
        $.post('socketmessage.php', { messageType: "pauseLogging", message: "" }, function (reply) {
            $backButton.show().unbind().bind({ click: function () { beerNameDialogPause($body, $backButton); } });
            receiveControlSettings();
            beerNameDialogResult($body, $backButton, reply);
        });
    }));
    $backButton.show().unbind().bind({ click: function () { beerNameDialogStart($body, $backButton); } });
    $body.append($buttons);
}

function beerNameDialogResume($body, $backButton) {
    "use strict";
    $body.empty();
    $body.append($("<span  class='dialog-intro'>Clicking the button below will resume logging for your currently active brew.</span>"));

    var $buttons = $("<div class='beer-name-buttons'></div>");
    $buttons.append($("<button>Resume logging for current brew</button>").button({ icons: { primary: "ui-icon-pause" } }).click(function () {
        $.post('socketmessage.php', { messageType: "resumeLogging", message: "" }, function (reply) {
            $backButton.show().unbind().bind({ click: function () { beerNameDialogResume($body, $backButton); } });
            receiveControlSettings();
            beerNameDialogResult($body, $backButton, reply);
        });
    }));
    $backButton.show().unbind().bind({ click: function () { beerNameDialogStart($body, $backButton); } });
    $body.append($buttons);
}

function beerNameDialogResult($body, $backButton, result) {
    "use strict";
    $body.empty();
    if (result === "") {
        result = { status: 2, statusMessage: "Could not receive reply from script" };
    }
    else {
        result = $.parseJSON(result);
    }
    if (result.status === 0) {
        // TODO: Reload page
        $body.append($("<span  class='dialog-result-success'>Success!</span>"));
        $backButton.hide();
    }
    else {
        $body.append($("<span  class='dialog-result-error'>Error:</span>"));
    }
    $body.append($("<span  class='dialog-result-message'>" + result.statusMessage + "</span>"));
}

$(document).ready(function () {
    "use strict";
    $(".script-status").button({ icons: { primary: "ui-icon-alert" } });
    $(".script-status span.ui-button-text").text("Checking script..");
    $("#beer-name").click(beerNameDialogInit);

    if (!isLCD()) { // Skip all this if we are on the LCD page
        loadControlPanel();
        drawBeerChart(window.beerName, 'curr-beer-chart');
        receiveControlConstants();
        receiveControlSettings();
        receiveControlVariables();
        refreshStatus(); // Refreshes status box in header
    }
    refreshLcd(); // Refreshes LCD
});
