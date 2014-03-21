<?php
class DownloadLecturerSchedules implements iSubscript {
  public function execute($oMysqli) {
    $sAcademysHtml = getHtmlString("http://roosters.saxion.nl/teachers");
    preg_match_all ("/\/teachers\/academy:[A-Za-z0-9]{0,5}/", $sAcademysHtml, $aMatches, PREG_PATTERN_ORDER);
    foreach ($aMatches[0] as $sMatch) {
      $sAcademy = substr($sMatch, 18);
      $this->downloadSchedulesForAcademy($sAcademy);
    }
  }
  
  public function downloadSchedulesForAcademy($sAcademy) {
    echo "Downloading lecturer schedule for academy " . $sAcademy;
    $sAcademyLecturersHtml = getHtmlString("http://roosters.saxion.nl/teachers/academy:" . $sAcademy);
    
    preg_match_all ("/\/schedule\/teacher:[A-Za-z0-9]{0,5}\/week:0/", $sAcademyLecturersHtml, $aMatches, PREG_PATTERN_ORDER);
    
    foreach ($aMatches[0] as $sMatch) {
      $sLecturerCode = substr($sMatch, 18,-7);
      $this->downloadLecturerSchedule($sLecturerCode);
    }
    echo "OK!\n";
  }
  
  public function downloadLecturerSchedule($sLecturerId) {
    echo ".";
    $sFile = dirname(__FILE__) . "/../../cache/lecturer/" . $sLecturerId . ".ics";
    file_put_contents($sFile, fopen("http://roosters.saxion.nl/ical/teacher/" . $sLecturerId . ".ics", 'r'));
  }
  
}
?>
