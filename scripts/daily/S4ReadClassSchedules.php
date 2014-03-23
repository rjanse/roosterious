<?php
class S4ReadClassSchedules implements iSubscript {
  public function execute($oMysqli) {
    if ($hDir = opendir(dirname(__FILE__) . "/../../cache/class/")) {
      while (false !== ($sFile = readdir($hDir))) {
        if ($sFile == '.' || $sFile == '..') { 
          continue; 
        }
        $sFullPath = dirname(__FILE__) . "/../../cache/class/" . $sFile;
        if (is_file($sFullPath) && strpos($sFile, ".ics") !== FALSE) {
          $sClassCode = substr($sFile, 0, -4);
          $this->readClassSchedule($oMysqli, $sFullPath, $sClassCode);
        } else {
          die("File " . $sFullPath . " doesn't seem to be a file. Nice!");
        }
      }
    } else {
      die("Can't run the given scripts, no valid period / subdirectory");
    }
  }	
  
  private function readClassSchedule($oMysqli, $sFullPath, $sClassCode) {
    echo "Reading class schedule from " . $sClassCode . " ";
    //Retrieve data
    $oiCal = getiCalEvents($sFullPath);
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
        $aClasses = array($sClassCode);
      
        //Extract class id from location
        $aLocationParts = preg_split("/[,]+/", $sLocation);
        $aRooms = array_unique(preg_split("/[\s\|]+/", trim($aLocationParts[1])));
      
        //Create lecturers array
        //Example format: "Docent G.M.T. Deterd Oude Weme|E.H. Ruiterkamp|M.H.T. Jansen|J.E. Nawijn (APO) SLB"
        //Example format: "Docent <doc>|<doc> (<ACADEMIE>) <SUBJECT>
        $aLecturers = array();
        $sLecturers = trim(substr($sDescription, 6, strpos($sDescription, "(") - 6));
        $aLecturerFullName = explode("|", $sLecturers);
        $aLecturerFullName = array_unique($aLecturerFullName);
        foreach ($aLecturerFullName as $sLecturerFullName) {
          if ($sLecturerFullName!="") {
            $sQuery = "SELECT id, name FROM lecturer WHERE name=\"" . $sLecturerFullName . "\";";
            $oResult = $oMysqli->query($sQuery);
            $oObj = $oResult->fetch_object();
            if ($oObj!=null) {
              array_push($aLecturers, $oObj->id);
            } else {
              array_push($aLecturers, "NULL");
              echo "No lecturer found for " . $sLecturerFullName . "\n";
            }
          }
        }
        
        $sActivityTypeId = trim(substr($sDescription, strpos($sDescription, ")") + 1));
        
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
    if (sizeof($aRooms) != 0) {
      $sRooms = implode(",", $aRooms);
    } else {
      $sRooms = "NULL";
    }
    
    //Create lesson
    $sQuery = "INSERT INTO lesson(date, starttime, endtime, activity_id, activitytype_id, description, summary, location, rooms) VALUES (" . 
    "\"" . $sDate . "\", " .
    "\"" . $sStarttime . "\", " .
    "\"" . $sEndtime . "\", " .
    "\"" . $sActivityId . "\", " . 
    "\"" . $sActivityTypeId . "\", " . 
    "\"" . $sDescription . "\", " .
    "\"" . $sSummary . "\", " .
    "\"" . $sLocation . "\", " .
    "\"" . $sRooms . "\"" .
    ");";
    
    if ($oMysqli->query($sQuery)) {
      $iLessonId = $oMysqli->insert_id;
    } else {
      $sQuery = "SELECT id FROM lesson WHERE date=\"" . $sDate . "\" AND starttime=\"" . $sStarttime . "\" AND endtime=\"" . $sEndtime . "\" AND rooms = \"" . $sRooms . "\";";
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
