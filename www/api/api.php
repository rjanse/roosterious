<?php
/**
 * Roosterious API Dispatcher
 */
require_once(dirname(__FILE__) . "/../../inc/commons.inc.php");
require_once(dirname(__FILE__) . "/../../inc/lib/flight/flight/Flight.php");

echo "blob";

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::start();
?>
