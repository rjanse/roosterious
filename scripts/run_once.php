<?php
require_once(dirname(__FILE__) . "/../inc/commons.inc.php");

//Read period variable
$sPeriod = $argv[1];
$sScript = $argv[2];
$sClassName = $argv[2];

//Set timezone
date_default_timezone_set(CONFIG_TIMEZONE);

//Open database connection
$oMysqli = getMysqli();
    
//Iterate over scriptfiles  
$sFile = dirname(__FILE__) . "/" . $sPeriod . "/" . $sScript . ".php";
if (is_file($sFile)) {
    require_once($sFile);
    $oSubscript = new $sClassName;
    $oSubscript->execute($oMysqli);
} else {
  die("Can't run the given scripts, no valid period and script name");
}

//Close database connection
$oMysqli->close();
?>