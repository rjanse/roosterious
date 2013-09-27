<?php
require_once(dirname(__FILE__) . "/../inc/commons.inc.php");

//Read period variable
$sPeriod = $argv[1];

//Set timezone
date_default_timezone_set(CONFIG_TIMEZONE);

//Open database connection
$oMysqli = getMysqli();
    
//Iterate over scriptfiles  
if ($hDir = opendir(dirname(__FILE__) . "/" . $sPeriod)) {
  while (false !== ($sFile = readdir($hDir))) {
    if ($sFile == '.' || $sFile == '..') { 
      continue; 
    }
    $sClassName = str_replace(".php","", $sFile); 
    $sFile  = dirname(__FILE__) . "/" . $sPeriod . "/".$sFile;
    if (is_file($sFile)) {
      require_once($sFile);
      $oSubscript = new $sClassName;
      $oSubscript->execute($oMysqli);
    }
  }
} else {
  die("Can't run the given scripts, no valid period / subdirectory");
}

//Close database connection
$oMysqli->close();
?>