<?php
require_once(dirname(__FILE__) . "/../inc/commons.inc.php");

//Read period variable
$sPeriod = $argv[1];

//Set timezone
date_default_timezone_set(CONFIG_TIMEZONE);

//Open database connection
$oMysqli = getMysqli();
    
//Iterate over scriptfiles  
$aFileList = scandir(dirname(__FILE__) . "/" . $sPeriod);
foreach ($aFileList as $sFile) {
  if ($sFile != '.' || $sFile != '..') { 
    $sClassName = str_replace(".php","", $sFile); 
    $sFile  = dirname(__FILE__) . "/" . $sPeriod . "/".$sFile;
    if (is_file($sFile)) {
      require_once($sFile);
      $oSubscript = new $sClassName;
      $oSubscript->execute($oMysqli);
    }
  }
}

//Close database connection
$oMysqli->close();
?>