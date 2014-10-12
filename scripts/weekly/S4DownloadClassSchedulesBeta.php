<?php
class S4DownloadClassSchedulesBeta implements iSubscript {
  public function execute($oMysqli) {
    $sAcademysHtml = getHtmlString("http://beta.rooster.saxion.nl/groups");
    preg_match_all ("/\/groups\/academy:[A-Za-z0-9]*\"/", $sAcademysHtml, $aMatches, PREG_PATTERN_ORDER);
    foreach ($aMatches[0] as $sMatch) {
      $sAcademy = substr($sMatch, 16, -1);
      $this->downloadSchedulesForAcademy($sAcademy);
    }
  }
  
  public function downloadSchedulesForAcademy($sAcademy) {
    echo "Downloading class schedule for academy " . $sAcademy . "\n";
    $sAcademyCoursesHtml = getHtmlString("http://beta.rooster.saxion.nl/groups/academy:" . $sAcademy);
    
    preg_match_all ("/\/groups\/course:[^\"]*\"/", $sAcademyCoursesHtml, $aMatches, PREG_PATTERN_ORDER);
    
    foreach ($aMatches[0] as $sMatch) {
      $sCourseCode = substr($sMatch, 15,-1);
      $this->downloadSchedulesForCourse($sCourseCode);
    }
  }
  
  public function downloadSchedulesForCourse($sCourse) {
    echo "  Downloading class schedule for course " . $sCourse;
    $sCourseClassesHtml = getHtmlString("http://beta.rooster.saxion.nl/groups/course:" . $sCourse);
    
    preg_match_all ("/\/schedule\/group:[^\/]*\/week:0/", $sCourseClassesHtml, $aMatches, PREG_PATTERN_ORDER);
    $i = 0;
    foreach ($aMatches[0] as $sMatch) {
      $sCourseCode = substr($sMatch, 16,-7);
      $this->downloadClassSchedule($sCourseCode);
      $i++;
    }
    echo "OK! (" . $i . " downloaded)\n";
  }
  
  public function downloadClassSchedule($sCourseCode) {
$sFile = dirname(__FILE__) . "/../../cache/class_beta/" . $sCourseCode . ".ics";
    if (file_exists($sFile)) {
      if (filemtime($sFile)  >= time() - 43200) {
        echo "_";
      } else {
        //http://croosters.saxion.nl/ical/group/EIN1Va.ics
        file_put_contents($sFile, fopen("http://croosters.saxion.nl/ical/group/" . $sCourseCode . ".ics", 'r'));
        echo "U";
      }
    } else {
      //http://croosters.saxion.nl/ical/group/EIN1Va.ics
      file_put_contents($sFile, fopen("http://croosters.saxion.nl/ical/group/" . $sCourseCode . ".ics", 'r'));
      echo "N";
    }
  }
  
}
?>
