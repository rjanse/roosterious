<?php
  require_once(dirname(__FILE__) . "/../inc/commons.inc.php");
  
  $oMysqli = getMysqli();

  echo "Create drop queries ...";
	$sQuery = "SELECT concat('DROP TABLE IF EXISTS ', table_name, ';') AS query FROM information_schema.tables WHERE table_schema = '" . CONFIG_DB_DATABASE . "';";
	echo "OK!\n";
  
  echo "Delete tables ";
  if ($oResult = $oMysqli->query($sQuery)) {
    while($aObject = $oResult->fetch_assoc()) {
      $oMysqli->query($aObject['query']);
      echo ".";
    }
  }
  echo "OK!\n";
?>