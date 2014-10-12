<?php
class S3DownloadLecturerSchedulesBeta implements iSubscript {
  public function execute($oMysqli) {
    $sAcademysHtml = getHtmlString("http://beta.rooster.saxion.nl/teachers");
    preg_match_all ("/\/teachers\/academy:[A-Za-z0-9]{0,5}/", $sAcademysHtml, $aMatches, PREG_PATTERN_ORDER);
    foreach ($aMatches[0] as $sMatch) {
      $sAcademy = substr($sMatch, 18);
      $this->downloadSchedulesForAcademy($sAcademy);
    }
  }
  
  public function downloadSchedulesForAcademy($sAcademy) {
    echo "Downloading lecturer schedule for academy " . $sAcademy;
    $sAcademyLecturersHtml = getHtmlString("http://beta.rooster.saxion.nl/teachers/academy:" . $sAcademy);
    
    preg_match_all ("/\/schedule\/teacher:[A-Za-z0-9]{0,5}\/week:0/", $sAcademyLecturersHtml, $aMatches, PREG_PATTERN_ORDER);
    
    foreach ($aMatches[0] as $sMatch) {
      $sLecturerCode = substr($sMatch, 18,-7);
      $this->downloadLecturerSchedule($sLecturerCode);
    }
    echo "OK!\n";
  }
  
  public function downloadLecturerSchedule($sLecturerId) {
    $sFile = dirname(__FILE__) . "/../../cache/lecturer_beta/" . $sLecturerId . ".ics";
    if (file_exists($sFile)) {
      if (filemtime($sFile)  >= time() - 43200) {
        echo "_";
      } else {
        //http://croosters.saxion.nl/ical/teacher/RGR05.ics
        file_put_contents($sFile, fopen("http://croosters.saxion.nl/ical/teacher/" . $sLecturerId . ".ics", 'r'));
        echo "U";
      }
    } else {
      //http://croosters.saxion.nl/ical/teacher/RGR05.ics
      file_put_contents($sFile, fopen("http://croosters.saxion.nl/ical/teacher/" . $sLecturerId . ".ics", 'r'));
      echo "N";
    }
  }
  
}
?>
