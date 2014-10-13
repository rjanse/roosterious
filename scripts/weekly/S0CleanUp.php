<?php
class S0CleanUp implements iSubscript {
  public function execute($oMysqli) {
    //Log start proces of updating
    $oMysqli->query("INSERT INTO stats_updates (date, starttime) VALUES (CURDATE(), CURTIME());");
    
  
    //Remove all entries after the monday of this week
    $oMysqli->query("DELETE FROM lesson WHERE date >= CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) - 2 DAY;");
  }  
}
?>
