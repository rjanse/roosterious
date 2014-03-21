<?php
/**
 * Roosterious API Dispatcher
 */
require_once(dirname(__FILE__) . "/../../inc/commons.inc.php");
require_once(dirname(__FILE__) . "/../../inc/lib/flight/flight/Flight.php");

/**
 * Returns a generic query to get lesson rows
 */
function generateLessonQuery($sFrom) {
	return "SELECT date, DATE_FORMAT(date, \"%w\") AS day_of_week, TIME_FORMAT(starttime, \"%H:%i\") AS starttime, TIME_FORMAT(endtime, \"%H:%i\") AS endtime, activity_id AS activity, activitytype_id AS activitytype,
  (SELECT GROUP_CONCAT(lecturer_id) FROM lessonlecturers WHERE lesson_id=id) AS lecturers, 
  (SELECT GROUP_CONCAT(class_id) FROM lessonclasses WHERE lesson_id=id) AS classes,
  (SELECT GROUP_CONCAT(room_id) FROM lessonrooms WHERE lesson_id=id) AS rooms
	" . $sFrom;
}

/**
 * Format a lesson result to json or ical
 */
function formatLessonResult($sFormat, $oResult) {
  if ($sFormat == "ical") {
  
  } else if ($sFormat == "json") {
    $sJson = "{\"status\": \"ok\", \"response\": [";
    $bFirst = true;
    while($aObject = $oResult->fetch_assoc()) {
      if (!$bFirst) {
      	$sJson .= ",";
      } else {
      	$bFirst = false;
      }
 	  $sJson .= json_encode($aObject);
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
    $sQuery = generateLessonQuery("FROM lesson,lessonlecturers WHERE lesson.id = lessonlecturers.lesson_id AND lecturer_id = \"" . $sLecturerId . "\" ORDER BY date, starttime;");
    
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult($sFormat, $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }
});

/**
 * Get's the schedule for a specified class
 */
Flight::route('GET /schedule/class/@sClassId\.@sFormat', function($sClassId, $sFormat){
    $oMysqli = getMysqli();
    $sQuery = generateLessonQuery("FROM lesson,lessonclasses WHERE lesson.id = lessonclasses.lesson_id AND class_id = \"" . $sClassId . "\" ORDER BY date, starttime");
    
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult($sFormat, $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }
});

/**
 * Get's the schedule for a specified room
 */
Flight::route('GET /schedule/room/@sRoomId\.@sFormat', function($sRoomId, $sFormat){
    $oMysqli = getMysqli();
    $sQuery = generateLessonQuery("FROM lesson,lessonrooms WHERE lesson.id = lessonrooms.lesson_id AND room_id = \"" . $sRoomId . "\" ORDER BY date, starttime");
    
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult($sFormat, $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }
});

/**
 * Get's the schedule for a specified room
 */
Flight::route('GET /schedule/now.@sFormat', function($sFormat){
	$oMysqli = getMysqli();
	
	$sQuery = generateLessonQuery("FROM lesson, lessonrooms WHERE lesson.id = lessonrooms.lesson_id AND date = DATE_FORMAT(NOW(),\"%Y-%m-%d\") AND starttime < NOW() AND endtime > NOW() ORDER BY starttime, room_id;");
	
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult($sFormat, $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }	
});
Flight::start();
?>
