<?php
class S8FillStatsTables implements iSubscript {
  public function execute($oMysqli) {    
    //Fill lecturer_search table
    $oMysqli->query("UPDATE stats_updates SET " .
      "endtime = CURTIME(), " .
      "number_of_lecturers = (SELECT count(*) FROM lecturer), " .
      "number_of_classes = (SELECT count(*) FROM class), " .
      "number_of_rooms = (SELECT count(*) FROM room), " .
      "number_of_activities = (SELECT count(*) FROM activity), ".
      "number_of_lessons = (SELECT count(*) FROM lesson) " .
    " WHERE date = CURDATE();");    
  }  
}
?>
