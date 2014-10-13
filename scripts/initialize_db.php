<?php
  require_once(dirname(__FILE__) . "/../inc/commons.inc.php");
  
  $oMysqli = getMysqli();

  echo "Creating building table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS building (id INT, name varchar(32), PRIMARY KEY (id));");
	echo "OK!\n";

	echo "Creating building part table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS buildingpart (id CHAR(1), building_id INT, name varchar(32), PRIMARY KEY (id));");
	echo "OK!\n";

	echo "Creating buildingfloor table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS buildingfloor (id CHAR(1), buildingpart_id CHAR(1), PRIMARY KEY (id, buildingpart_id));");
	echo "OK!\n";
	
	echo "Creating room table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS room (id VARCHAR(16), buildingfloor_id CHAR(1), buildingpart_id CHAR(1), fullname VARCHAR(8), PRIMARY KEY (id, buildingfloor_id, buildingpart_id));");
	echo "OK!\n";
	
	echo "Creating class table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS class (id VARCHAR(16), PRIMARY KEY (id));");
	echo "OK!\n";
	  
  echo "Creating lecturer table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS lecturer (id VARCHAR(5), name VARCHAR(64), PRIMARY KEY (id));");
	echo "OK!\n";
	
	echo "Creating activitytype table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS activitytype (id VARCHAR(128), PRIMARY KEY (id));");
	echo "OK!\n";
	
	echo "Creating activity table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS activity (id VARCHAR(128), PRIMARY KEY (id));");
	echo "OK!\n";
	
  echo "Creating lesson rooms table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS lessonrooms (lesson_id INT, room_id VARCHAR(16), PRIMARY KEY (lesson_id, room_id));");
	echo "OK!\n";
	
  echo "Creating lesson classes table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS lessonclasses (lesson_id INT, class_id VARCHAR(16), PRIMARY KEY (lesson_id, class_id));");
	echo "OK!\n";
	
	echo "Creating lessonlectures table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS lessonlecturers (lesson_id INT, lecturer_id VARCHAR(5), PRIMARY KEY (lesson_id, lecturer_id));");
	echo "OK!\n";
	
	echo "Creating lesson table...";
	$oMysqli->query("CREATE TABLE IF NOT EXISTS lesson (id INT AUTO_INCREMENT, date DATE, starttime TIME, endtime TIME, activity_id VARCHAR(128), activitytype_id VARCHAR(128), description VARCHAR(512), summary VARCHAR(512), location VARCHAR(512), rooms VARCHAR(512), beta BOOL, PRIMARY KEY (id), CONSTRAINT uc_lessonunique UNIQUE (date, starttime, endtime, activity_id, activitytype_id, rooms));");
	echo "OK!\n";
	
	
	
	// SEARCH TABLES
	
	echo "Creating lecturer search table...";
  $oMysqli->query("CREATE TABLE IF NOT EXISTS search_lecturer (searchwords VARCHAR(128), lecturer_id VARCHAR(5), lecturer_name VARCHAR(64), activities VARCHAR(512));");
	echo "OK!\n";
	
  echo "Creating class search table...";
  $oMysqli->query("CREATE TABLE IF NOT EXISTS search_class (searchwords VARCHAR(128), class_id VARCHAR(5), activities VARCHAR(512));");
	echo "OK!\n";
	
  echo "Creating room search table...";
  $oMysqli->query("CREATE TABLE IF NOT EXISTS search_room (searchwords VARCHAR(128), room_id VARCHAR(5), activities VARCHAR(512));");
	echo "OK!\n";
	
	// STATS TABLES
  echo "Creating updates stats table...";
  $oMysqli->query("CREATE TABLE IF NOT EXISTS stats_updates (date DATE, starttime TIME, endtime TIME, number_of_lecturers INT, number_of_classes INT, number_of_rooms INT, number_of_activities INT, number_of_lessons INT, PRIMARY KEY(date));");
	echo "OK!\n";
?>