<?php
class ReadLecturerSchedules implements iSubscript {
  public function execute($oMysqli) {
    if ($hDir = opendir(dirname(__FILE__) . "/../../cache/lecturer/")) {
      while (false !== ($sFile = readdir($hDir))) {
        if ($sFile == '.' || $sFile == '..') { 
          continue; 
        }
        $sFullPath = dirname(__FILE__) . "/../../cache/lecturer/" . $sFile;
        if (is_file($sFullPath) && strpos($sFile, ".ics") !== FALSE) {
          $sLecturerCode = substr($sFile, 0, -4);
          $this->readLecturerSchedule($oMysqli, $sFullPath, $sLecturerCode);
        } else {
          die("File " . $sFullPath . " doesn't seem to be a file. Nice!");
        }
      }
    } else {
      die("Can't run the given scripts, no valid period / subdirectory");
    }
  }	
  
  private function readLecturerSchedule($oMysqli, $sFullPath, $sLecturerId) {
    echo "Reading lecturer schedule from " . $sLecturerId . " ";
    //Retrieve data
    $oiCal = getiCalEvents($sFullPath);
    $sLecturerString = $oiCal->cal['VCALENDAR']['X-WR-CALNAME'];
    $sLecturerName = trim(substr($sLecturerString, 20));
    
    //Add lecturer to database
    $oMysqli->query("INSERT INTO lecturer(id, name) VALUES (\"" . $sLecturerId . "\", \"" . $sLecturerName . "\") ON DUPLICATE KEY UPDATE name = \"" . $sLecturerName . "\";");
    
    if ($oiCal->hasEvents()) {
      $aEvents = $oiCal->events();
    
      foreach ($aEvents as $aEvent) {
        $sDate = date("Y-m-d", $oiCal->iCalDateToUnixTimestamp($aEvent['DTSTART']));
        $sStartTime = date("G:i:00", $oiCal->iCalDateToUnixTimestamp($aEvent['DTSTART']));
        $sEndTime = date("G:i:00", $oiCal->iCalDateToUnixTimestamp($aEvent['DTEND']));
      
        $sSummary = $aEvent['SUMMARY'];
        $sDescription = $aEvent['DESCRIPTION'];
        $sLocation = $aEvent['LOCATION'];
      
      
        //Special types
        $sActivityId = $sSummary;
      
        //Extract classes and activitytype from description
        $sClasses = trim(substr($sDescription, 5, strpos($sDescription, "(") - 6));
        preg_match_all("/[A-Za-z0-9]+/", $sClasses, $aClasses);
        $aClasses = array_unique($aClasses[0]);
        $sActivityTypeId = trim(substr($sDescription, strpos($sDescription, ")") + 1));
      
        //Extract class id from location
        $aLocationParts = preg_split("/[,]+/", $sLocation);
        $aRooms = array_unique(preg_split("/[\s\|]+/", trim($aLocationParts[1])));
      
        //Create lecturers array
        $aLecturers = array($sLecturerId);
      
        sort($aRooms);
        sort($aClasses);
        sort($aLecturers);
      
        //Add entry
        $this->createEntry($oMysqli, $sDate, $sStartTime, $sEndTime, $aRooms, $aClasses, $aLecturers, $sActivityId, $sActivityTypeId, $sDescription, $sSummary, $sLocation);
        echo ".";
      }
    }
    echo "OK!\n";
  }
  
  private function createEntry($oMysqli, $sDate, $sStarttime, $sEndtime, $aRooms, $aClasses, $aLecturers, $sActivityId, $sActivityTypeId, $sDescription, $sSummary, $sLocation) {
    
    //Create lesson
    $sQuery = "INSERT INTO lesson(date, starttime, endtime, activity_id, activitytype_id, description, summary, location) VALUES (" . 
    "\"" . $sDate . "\", " .
    "\"" . $sStarttime . "\", " .
    "\"" . $sEndtime . "\", " .
    "\"" . $sActivityId . "\", " . 
    "\"" . $sActivityTypeId . "\", " . 
    "\"" . $sDescription . "\", " .
    "\"" . $sSummary . "\", " .
    "\"" . $sLocation . "\"" .
    ");";
    
    if ($oMysqli->query($sQuery)) {
      $iLessonId = $oMysqli->insert_id;
    } else {
      $sQuery = "SELECT id FROM lesson WHERE date=\"" . $sDate . "\" AND starttime=\"" . $sStarttime . "\" AND endtime=\"" . $sEndtime . "\" AND location LIKE \"" . substr($sLocation,0,512) . "%\";";
      $oResult = $oMysqli->query($sQuery);
      $oObj = $oResult->fetch_object();
      $iLessonId = $oObj->id;
    }
    
    //Add activities and activity types
    if ($sActivityId!="") {
      $oMysqli->query("INSERT INTO activity VALUES (\"" . $sActivityId . "\");");
    }
    if ($sActivityTypeId!="") {
      $oMysqli->query("INSERT INTO activitytype VALUES (\"" . $sActivityTypeId . "\");");
    }
    
    //Add rooms, classes, lecturers
    foreach ($aRooms as $sRoom) {
      //TODO: splitting in buildingfloor, buildingpart and building 
      //Add room to room table when not exists
      $oMysqli->query("INSERT INTO room(id) VALUES (\"" . $sRoom . "\");");

      $oMysqli->query("INSERT INTO lessonrooms VALUES (" . $iLessonId . ", \"" . $sRoom . "\");");

    }
    foreach ($aClasses as $sClass) {
      //Add class to class table when not exists
      $oMysqli->query("INSERT INTO class(id) VALUES (\"" . $sClass . "\");");
      
      $oMysqli->query("INSERT INTO lessonclasses VALUES (" . $iLessonId . ", \"" . $sClass . "\");");
      
    }
    foreach ($aLecturers as $sLecturer) {
      //Add lecturer to lecturer table when not exists
      $oMysqli->query("INSERT INTO lecturer(id) VALUES (\"" . $sLecturer . "\");");
      
      $oMysqli->query("INSERT INTO lessonlecturers VALUES (" . $iLessonId . ", \"" . $sLecturer . "\");");
      
    }
  }
}
?>