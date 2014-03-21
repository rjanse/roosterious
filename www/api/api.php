<?php
/**
 * Roosterious API Dispatcher
 */
require_once(dirname(__FILE__) . "/../../inc/commons.inc.php");
require_once(dirname(__FILE__) . "/../../inc/lib/flight/flight/Flight.php");

function mySqlResultToFormat($sFormat, $oResult) {
  if ($sFormat == "ical") {
  
  } else if ($sFormat == "json") {
    $sJson = "{\"status\": \"ok\", \"response\": [";
    while($aObject = $oResult->fetch_assoc()) {
      $sJsonObject = "";
      foreach ($aObject as $sKey => $sValue) {
        if (strpos($sKey, "id") === false) {
          if ($sJsonObject == "") {
            $sJsonObject .= json_encode($sKey) . ":" . json_encode($sValue);
          } else {
            $sJsonObject .=  ", " . json_encode($sKey) . ":" . json_encode($sValue);
          }
        }
      }
      $sJson .= "{" . $sJsonObject . "}, \n"; 
    }
    $sJson .= "]}";
    return $sJson;
  }
}

function errorInFormat($sFormat) {
  if ($sFormat == "ical") {
  
  } else if ($sFormat == "json") {
    return "{\"status\": \"error\", \"message\": \"Error in database query\"}";
  }
}

/**
 * Get's the schedule for a specified lecturer
 */
Flight::route('GET /schedule/lecturer/@sLecturerId\.@sFormat', function($sLecturerId, $sFormat){
    $oMysqli = getMysqli();
    $sQuery = "select * from lesson,lessonlecturers where lesson.id = lessonlecturers.lesson_id AND lecturer_id = \"" . $sLecturerId . "\" ORDER BY date, starttime";
    
    if ($oResult = $oMysqli->query($sQuery)) {
      echo mysqlResultToFormat($sFormat, $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }
});

/**
 * Get's the schedule for a specified class
 */
Flight::route('GET /schedule/class/@sClassId\.@sFormat', function($sClassId, $sFormat){
    $oMysqli = getMysqli();
    $sQuery = "select * from lesson,lessonclasses where lesson.id = lessonclasses.lesson_id AND class_id = \"" . $sClassId . "\" ORDER BY date, starttime";
    
    if ($oResult = $oMysqli->query($sQuery)) {
      echo mysqlResultToFormat($sFormat, $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }
});

/**
 * Get's the schedule for a specified room
 */
Flight::route('GET /schedule/room/@sRoomId\.@sFormat', function($sRoomId, $sFormat){
    $oMysqli = getMysqli();
    $sQuery = "select * from lesson,lessonrooms where lesson.id = lessonrooms.lesson_id AND room_id = \"" . $sRoomId . "\" ORDER BY date, starttime";
    
    if ($oResult = $oMysqli->query($sQuery)) {
      echo mysqlResultToFormat($sFormat, $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }
});

Flight::start();
?>
