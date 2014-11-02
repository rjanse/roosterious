<?php
/**
 * Roosterious API Dispatcher
 */
require_once(dirname(__FILE__) . "/../../inc/commons.inc.php");
require_once(dirname(__FILE__) . "/../../inc/lib/flight/flight/Flight.php");
require_once(dirname(__FILE__) . "/../../inc/lib/icalcreator/iCalcreator.class.php");

/**
 * Returns a generic query to get lesson rows
 */
function generateLessonQuery($sFrom) {
	return "SELECT date, WEEK(date) as weeknr, DATE_FORMAT(date, \"%w\") AS day_of_week, TIME_FORMAT(starttime, \"%H:%i\") AS starttime, TIME_FORMAT(endtime, \"%H:%i\") AS endtime, startlecturehour, endlecturehour, activity_id AS activity, activitytype_id AS activitytype, 
  (SELECT GROUP_CONCAT(lecturer_id) FROM lessonlecturers WHERE lesson_id=id) AS lecturers, 
  (SELECT GROUP_CONCAT(class_id) FROM lessonclasses WHERE lesson_id=id) AS classes,
  (SELECT GROUP_CONCAT(room_id) FROM lessonrooms WHERE lesson_id=id) AS rooms
	, beta AS is_beta " . $sFrom;
}

function getFromDateString() {
  return "CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) - 2 DAY";
}


function formatDbResult($sFormat, $oResult, $sFilename = "response") {
	if ($sFormat == "json") {
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $sFilename . '.json');
		
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
  } else if ($sFormat == "csv") {
	  header('Content-Type: text/csv; charset=utf-8');
	  header('Content-Disposition: attachment; filename=' . $sFilename . '.csv');
	  
	  $fOutput = fopen('php://output', 'w');
	  fputs($fOutput, "sep=,\n");
	  
	  fputcsv($fOutput, array('datum', 'weeknr', 'dag_van_de_week', 'starttijd', 'eindtijd', 'startlesuur', 'eindlesuur', 'activiteit', 'activiteittype', 'docenten', 'klassen', 'lokalen', 'is_beta'));
	  
	  while ($aObject = $oResult->fetch_assoc()) {
		  fputcsv($fOutput, $aObject);
	  }
  }
}

/**
 * Format a lesson result to json, ical or csv
 */
function formatLessonResult($sFormat, $oResult, $sTitle = "Rooster", $sDescription = "", $sFilename = "schedule") {
  if ($sFormat == "ical") {
	date_default_timezone_set('Europe/Amsterdam');
  	header('Content-type: text/calendar; charset=utf-8');
  	header('Content-Disposition: attachment; filename=' . $sFilename . '.ical');

	$config    = array( "unique_id" => "roosterious.nl", "TZID" => "Europe/Amsterdam" );
	$vcalendar = new vcalendar( $config );
	$vcalendar->setProperty( "method",        "PUBLISH" );
	$vcalendar->setProperty( "x-wr-calname",  $sTitle );
	$vcalendar->setProperty( "X-WR-CALDESC",  $sDescription );
	$vcalendar->setProperty( "X-WR-RELCALID", uniqid() );
	$vcalendar->setProperty( "X-WR-TIMEZONE", "Europe/Amsterdam" );
	
	while($aObject = $oResult->fetch_assoc()) {
		$vevent = & $vcalendar->newComponent( "vevent" );
		$vevent->setProperty("DTSTART", date('Ymd\THis', strtotime($aObject['date'] . " " . $aObject['starttime'])));
		$vevent->setProperty("DTEND", date('Ymd\THis', strtotime($aObject['date'] . " " . $aObject['endtime'])));
		$vevent->setProperty("SUMMARY" , $aObject['activitytype'] . " - " . $aObject['activity']);
		$vevent->setProperty("LOCATION", $aObject['rooms']);
		$vevent->setProperty("DESCRIPTION", "Klassen: " . $aObject['classes'] . "\nDocenten: " . $aObject['lecturers']);
	}
	
	//Timezone
	$xprops = array( "X-LIC-LOCATION" => "Europe/Amsterdam" );
	iCalUtilityFunctions::createTimezone( $vcalendar, "Europe/Amsterdam", $xprops );
	
	$vcalendar->returnCalendar(); 
  } else if ($sFormat == "json") {
	header('Content-Type: application/json; charset=utf-8');
	header('Content-Disposition: attachment; filename=' . $sFilename . '.json');
	
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
  } else if ($sFormat == "csv") {
	  header('Content-Type: text/csv; charset=utf-8');
	  header('Content-Disposition: attachment; filename=' . $sFilename . '.csv');
	  
	  $fOutput = fopen('php://output', 'w');
	  fputs($fOutput, "sep=,\n");
	  
	  fputcsv($fOutput, array('datum', 'weeknr', 'dag_van_de_week', 'starttijd', 'eindtijd', 'startlesuur', 'eindlesuur', 'activiteit', 'activiteittype', 'docenten', 'klassen', 'lokalen', 'is_beta'));
	  
	  while ($aObject = $oResult->fetch_assoc()) {
		  fputcsv($fOutput, $aObject);
	  }
  }
}

function errorInFormat($sFormat, $sMessage = "") {
  if ($sFormat == "ical") {
  
  } else if ($sFormat == "json") {
	if ($sMessage == "") {
		return "{\"status\": \"error\", \"message\": \"An unknown error occured. You should now panic\"}";
	} else {
		return "{\"status\": \"error\", \"message\": \"" . $sMessage . "\"}";
	}
  }
}




/**
 * Get's the schedule for a specified lecturer
 */
Flight::route('GET /schedule/lecturer/@sLecturerId\.@sFormat', function($sLecturerId, $sFormat){
	if (!preg_match('/^[A-Za-z0-9]{5}$/', $sLecturerId)) {
		echo errorInFormat($sFormat, "Lecturer id should be in format XXXXX (five letters or numbers)");
		return;
	}
    $oMysqli = getMysqli();
    $sQuery = generateLessonQuery("FROM lesson,lessonlecturers WHERE lesson.id = lessonlecturers.lesson_id AND lecturer_id = \"" . $sLecturerId . "\" AND date >= " . getFromDateString() . " ORDER BY date, starttime;");

    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult($sFormat, $oResult, $sLecturerId, "Opgebouwd vanuit Roosterious", $sLecturerId); 
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
      echo formatLessonResult($sFormat, $oResult, $sClassId, "Opgebouwd vanuit Roosterious", $sClassId); 
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
      echo formatLessonResult($sFormat, $oResult, $sRoomId, "Opgebouwd vanuit Roosterious", $sRoomId); 
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
      echo formatLessonResult($sFormat, $oResult, $sActivityId, "Opgebouwd vanuit Roosterious", $sActivityId); 
    } else {
      echo errorInFormat($sFormat);
    }
});

/**
 * Get's the schedule for now
 */
Flight::route('GET /schedule/datetime/@sDate/@sLectureHour\.@sFormat', function($sDate, $sLectureHour, $sFormat){
	if (!preg_match('/^[0-9]+\-[0-9]+\-[0-9]+$/', $sDate)) {
		echo errorInFormat($sFormat, "Date should be in format YYYY-MM-DD");
		return;
	}
	if (!preg_match('/^[0-9]+$/', $sLectureHour)) {
		echo errorInFormat($sFormat, "Lecturehour should be in number format e.g: 1,2,3 ... 16");
		return;
	}
	
	$oMysqli = getMysqli();
	
	$sQuery = generateLessonQuery("FROM lesson, lessonrooms WHERE lesson.id = lessonrooms.lesson_id AND date = \"" . $sDate . "\" AND startlecturehour <= " . $sLectureHour . " AND endlecturehour > " . $sLectureHour . " ORDER BY startlecturehour, room_id;");
	
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult($sFormat, $oResult, $sLectureHour . "e lesuur op " . $sDate, "Opgebouwd vanuit Roosterious", $sDate . "_" . $sLectureHour); 
    } else {
      echo errorInFormat($sFormat);
    }	
});

/**
 * Get's the schedule for now
 */
Flight::route('GET /schedule/datetime/now.@sFormat', function($sFormat){
	$oMysqli = getMysqli();
	
	$sQuery = generateLessonQuery("FROM lesson, lessonrooms WHERE lesson.id = lessonrooms.lesson_id AND date = DATE_FORMAT(NOW(),\"%Y-%m-%d\") AND starttime <= NOW() AND endtime > NOW() ORDER BY startlecturehour, room_id;");
	
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatLessonResult($sFormat, $oResult, "nu", "Opgebouwd vanuit Roosterious", "nu"); 
    } else {
      echo errorInFormat($sFormat);
    }	
});

/**
 * Get's a lecturer, based on search query
 */
Flight::route('GET /lecturer.@sFormat', function($sFormat){
	$oMysqli = getMysqli();
	
  $sSearchword = Flight::request()->query->q;
	$iPageLimit = Flight::request()->query->page_limit;
	if (!is_numeric($iPageLimit)) {
  	$iPageLimit = 10;
	}
 	
 	if ($sSearchword != "") {
   	$sQuery = "SELECT lecturer_id, lecturer_name, activities FROM search_lecturer WHERE searchwords LIKE '%" . $sSearchword . "%' LIMIT " . $iPageLimit . ";";
 	} else {
   	$sQuery = "SELECT lecturer_id, lecturer_name, activities FROM search_lecturer;";
 	}
	
  if ($oResult = $oMysqli->query($sQuery)) {
    echo formatDbResult($sFormat, $oResult); 
  } else {
    echo errorInFormat("json");
  }	
});

/**
 * Get's a class, based on search query
 */
Flight::route('GET /class.@sFormat', function($sFormat){
	$oMysqli = getMysqli();
	
  $sSearchword = Flight::request()->query->q;
	$iPageLimit = Flight::request()->query->page_limit;
	if (!is_numeric($iPageLimit)) {
  	$iPageLimit = 10;
	}
 	
  if ($sSearchword != "") {
   	$sQuery = "SELECT class_id, activities FROM search_class WHERE searchwords LIKE '%" . $sSearchword . "%' LIMIT " . $iPageLimit . ";";
 	} else {
   	$sQuery = "SELECT class_id, activities FROM search_class;";
 	}
 	
  if ($oResult = $oMysqli->query($sQuery)) {
    echo formatDbResult($sFormat, $oResult);
  } else {
    echo errorInFormat("json");
  }	
});

/**
 * Get's a room, based on search query
 */
Flight::route('GET /room.@sFormat', function($sFormat){
	$oMysqli = getMysqli();
	
  $sSearchword = Flight::request()->query->q;
	$iPageLimit = Flight::request()->query->page_limit;
	if (!is_numeric($iPageLimit)) {
  	$iPageLimit = 10;
	}
 	
  if ($sSearchword != "") {
   	$sQuery = "SELECT room_id, activities FROM search_room WHERE searchwords LIKE '%" . $sSearchword . "%' LIMIT " . $iPageLimit . ";";
 	} else {
   	$sQuery = "SELECT room_id, activities FROM search_room;";
 	}
 	
  if ($oResult = $oMysqli->query($sQuery)) {
    echo formatDbResult($sFormat, $oResult);
  } else {
    echo errorInFormat("json");
  }	
});

/**
 * Get's a activity, based on search query
 */
Flight::route('GET /activity.@sFormat', function($sFormat){
	$oMysqli = getMysqli();
	
  $sSearchword = Flight::request()->query->q;
	$iPageLimit = Flight::request()->query->page_limit;
	if (!is_numeric($iPageLimit)) {
  	$iPageLimit = 10;
	}
 	
  if ($sSearchword != "") {
   	$sQuery = "SELECT DISTINCT activity_id FROM search_activity WHERE searchwords LIKE '%" . $sSearchword . "%' LIMIT " . $iPageLimit . ";";
 	} else {
   	$sQuery = "SELECT DISTINCT activity_id FROM search_activity;";
 	}
 	
  if ($oResult = $oMysqli->query($sQuery)) {
    echo formatDbResult($sFormat, $oResult);
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
      echo formatDbResult("json", $oResult);
    } else {
      echo errorInFormat("json");
    }	
});

/**
 * Get's a list of updates
 */
Flight::route('GET /stats/updates.json', function() {
	$oMysqli = getMysqli();
	
	$sQuery = "SELECT * FROM stats_updates WHERE date > DATE_SUB(CURDATE(), INTERVAL 1 YEAR) ORDER BY date DESC;";
    if ($oResult = $oMysqli->query($sQuery)) {
      echo formatDbResult("json", $oResult);
    } else {
      echo errorInFormat("json");
    }	
}); 

/**
 * Get's the update stats
 */
Flight::route('GET /stats/is_updating.json', function() {
	$oMysqli = getMysqli();
	
	$sQuery = "SELECT * FROM stats_updates WHERE date = (SELECT max(date) FROM stats_updates) AND endtime IS NULL;";
    if ($oResult = $oMysqli->query($sQuery)) {
      if (mysqli_num_rows($oResult) != 0) {
        echo "{\"status\": \"ok\", \"response\": \"true\"}";
      } else {
        echo "{\"status\": \"ok\", \"response\": \"false\"}";
      }
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
