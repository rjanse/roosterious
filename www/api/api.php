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
	, beta AS is_beta, WEEK(date) as weeknr " . $sFrom;
}

function getFromDateString() {
  return "CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) - 2 DAY";
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
    $sQuery = generateLessonQuery("FROM lesson,lessonlecturers WHERE lesson.id = lessonlecturers.lesson_id AND lecturer_id = \"" . $sLecturerId . "\" AND date >= " . getFromDateString() . " ORDER BY date, starttime;");
    
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
    $sQuery = generateLessonQuery("FROM lesson,lessonclasses WHERE lesson.id = lessonclasses.lesson_id AND class_id = \"" . $sClassId . "\" AND date >= " . getFromDateString() . " ORDER BY date, starttime");
    
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
    $sQuery = generateLessonQuery("FROM lesson,lessonrooms WHERE lesson.id = lessonrooms.lesson_id AND room_id = \"" . $sRoomId . "\" AND date >= " . getFromDateString() . " ORDER BY date, starttime");
    
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult($sFormat, $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }
});

/**
 * Get's the schedule for a specified activity
 */
Flight::route('GET /schedule/activity/@sActivityId\.@sFormat', function($sActivityId, $sFormat){
    $oMysqli = getMysqli();
    $sQuery = generateLessonQuery("FROM lesson WHERE activity_id = \"" . $sActivityId . "\" AND date >= " . getFromDateString() . " ORDER BY date, starttime");
    
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult($sFormat, $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }
});

/**
 * Get's the schedule for now
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

/**
 * Get's a lecturer, based on search query
 */
Flight::route('GET /lecturer.json', function(){
	$oMysqli = getMysqli();
	
  $sSearchword = Flight::request()->query->q;
	$iPageLimit = Flight::request()->query->page_limit;
	if (!is_numeric($iPageLimit)) {
  	$iPageLimit = 10;
	}
 	
  $sQuery = "SELECT lecturer_id, lecturer_name, activities FROM search_lecturer WHERE searchwords LIKE '%" . $sSearchword . "%' LIMIT " . $iPageLimit . ";";
	
  if ($oResult = $oMysqli->query($sQuery)) {
    echo formatLessonResult("json", $oResult); 
  } else {
    echo errorInFormat("json");
  }	
});



/**
 * Get's the dashboard stats
 */
Flight::route('GET /stats/dashboard.json', function() {
	$oMysqli = getMysqli();
	
	$sQuery = "SELECT * FROM stats_updates WHERE date = (SELECT max(date) FROM stats_updates);";
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult("json", $oResult); 
    } else {
      echo errorInFormat("json");
    }	
});

/**
 * Get's the update stats
 */
Flight::route('GET /stats/updates.json', function() {
	$oMysqli = getMysqli();
	
	$sQuery = "SELECT * FROM stats_updates WHERE date > DATE_SUB(CURDATE(), INTERVAL 1 YEAR) ORDER BY date DESC;";
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult("json", $oResult); 
    } else {
      echo errorInFormat("json");
    }	
}); 

/**
 * Get's the stats for lecturer
 */
 /**
Flight::route('GET /stats/lecturer/@sLecturerId.json', function($sLecturerId) {
	$oMysqli = getMysqli();
	
	$sQuery = "SELECT DISTINCT YEARWEEK(date) AS weeknr, activity_id AS activity_id_summary, (SELECT count(*) AS number FROM lesson,lessonlecturers WHERE activity_id = activity_id_summary AND lesson.id = lessonlecturers.lesson_id AND lecturer_id=\"" . $sLecturerId . "\" AND YEARWEEK(date) = weeknr) AS number FROM lesson,lessonlecturers WHERE lesson.id = lessonlecturers.lesson_id AND lecturer_id = \"" . $sLecturerId . "\" ORDER BY weeknr, activity_id_summary;";
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult("json", $oResult); 
    } else {
      echo errorInFormat($sFormat);
    }	
}); 
**/
Flight::start();
?>
