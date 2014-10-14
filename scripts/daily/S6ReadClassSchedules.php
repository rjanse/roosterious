<?php
class S6ReadClassSchedules implements iSubscript {
  public function execute($oMysqli) {    
    //Read normal schedules
    if ($hDir = opendir(dirname(__FILE__) . "/../../cache/class/")) {
      while (false !== ($sFile = readdir($hDir))) {
        if ($sFile == '.' || $sFile == '..') { 
          continue; 
        }
        $sFullPath = dirname(__FILE__) . "/../../cache/class/" . $sFile;
        if (is_file($sFullPath) && strpos($sFile, ".ics") !== FALSE) {
          $sClassCode = substr($sFile, 0, -4);
          $this->readClassSchedule($oMysqli, $sFullPath, $sClassCode, false);
        } else {
          die("File " . $sFullPath . " doesn't seem to be a file. Nice!");
        }
      }
    } else {
      die("Can't run the given scripts, no valid period / subdirectory");
    }
    
    //Read beta schedules
    if ($hDir = opendir(dirname(__FILE__) . "/../../cache/class_beta/")) {
      while (false !== ($sFile = readdir($hDir))) {
        if ($sFile == '.' || $sFile == '..') { 
          continue; 
        }
        $sFullPath = dirname(__FILE__) . "/../../cache/class_beta/" . $sFile;
        if (is_file($sFullPath) && strpos($sFile, ".ics") !== FALSE) {
          $sClassCode = substr($sFile, 0, -4);
          $this->readClassSchedule($oMysqli, $sFullPath, $sClassCode, true);
        } else {
          die("File " . $sFullPath . " doesn't seem to be a file. Nice!");
        }
      }
    } else {
      die("Can't run the given scripts, no valid period / subdirectory");
    }
  }	
  
  private function readClassSchedule($oMysqli, $sFullPath, $sClassCode, $bIsBeta) {
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
        //Example format: "DESCRIPTION:Docent R. Greven\, J.W.M. Stroet\, Kick-off kwartiel 1.1"
        //Example format: "Docent <doc>|,<doc> !, <SUBJECT>
        //Exampe format (bug): DESCRIPTION:Docent N.G.J. Vloon\, how to be succesful\, not for german-dutch Students
        $aDescriptionParts = preg_split("/[,|]+/", $sDescription);
        $iDescriptionPartsLength = count($aDescriptionParts);
        $aLecturers = array();
        for($i = 0; $i < ($iDescriptionPartsLength-1); $i++) {
          $sLecturer = trim($aDescriptionParts[$i]);
          $sLecturer = str_replace("\\", "", $sLecturer);
          $sLecturer = str_replace("Docent ", "", $sLecturer);
          if (strlen($sLecturer)!=0) {
            $sQuery = "SELECT id, name FROM lecturer WHERE name=\"" . $sLecturer . "\";";
            $oResult = $oMysqli->query($sQuery);
            $oObj = $oResult->fetch_object();
            if ($oObj!=null) {
              array_push($aLecturers, $oObj->id);
            } else {
              array_push($aLecturers, "NULL");
              echo "No lecturer found for " . $sLecturer . "\n";
            }

          }
        }
        $sActivityTypeId = trim($aDescriptionParts[$iDescriptionPartsLength - 1]);
        $aLecturers = array_unique($aLecturers);
        
        sort($aRooms);
        sort($aClasses);
        sort($aLecturers);
      
        //Add entry
        $this->createEntry($oMysqli, $sDate, $sStartTime, $sEndTime, $aRooms, $aClasses, $aLecturers, $sActivityId, $sActivityTypeId, $sDescription, $sSummary, $sLocation, $bIsBeta);
        echo ".";
      }
    }
    echo "OK!\n";
  }
  
  private function createEntry($oMysqli, $sDate, $sStarttime, $sEndtime, $aRooms, $aClasses, $aLecturers, $sActivityId, $sActivityTypeId, $sDescription, $sSummary, $sLocation, $bBeta) {
    if (sizeof($aRooms) != 0) {
      $sRooms = implode(",", $aRooms);
    } else {
      $sRooms = "NULL";
    }
    
    //Create lesson
    $sQuery = "INSERT INTO lesson(date, starttime, endtime, activity_id, activitytype_id, description, summary, location, rooms, beta) VALUES (" . 
    "\"" . $sDate . "\", " .
    "\"" . $sStarttime . "\", " .
    "\"" . $sEndtime . "\", " .
    "\"" . $sActivityId . "\", " . 
    "\"" . $sActivityTypeId . "\", " . 
    "\"" . $sDescription . "\", " .
    "\"" . $sSummary . "\", " .
    "\"" . $sLocation . "\", " .
    "\"" . $sRooms . "\", " .
    "\"" . ($bBeta == true ? 1 : 0) . "\"" . 
    ");";
    
    if ($oMysqli->query($sQuery)) {
      $iLessonId = $oMysqli->insert_id;
    } else {
      $sQuery = "SELECT id FROM lesson WHERE date=\"" . $sDate . "\" AND starttime=\"" . $sStarttime . "\" AND endtime=\"" . $sEndtime . "\" AND activity_id = \"" . $sActivityId . "\" AND activitytype_id = \"" . $sActivityTypeId . "\" AND rooms = \"" . $sRooms . "\";";
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
