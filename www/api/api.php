<?php
/**
 * Roosterious API Dispatcher
 */
require_once(dirname(__FILE__) . "/../../inc/commons.inc.php");
require_once(dirname(__FILE__) . "/../../inc/lib/flight/flight/Flight.php");

/**
 * Get's the schedule for a specified lecturer
 */
Flight::route('GET /schedule/lecturer/@sLecturerId\.@sFormat', function($sLecturerId, $sFormat){
    echo 'hello world!' . $sLecturerId . " + " . $sFormat;
});

Flight::start();
?>
