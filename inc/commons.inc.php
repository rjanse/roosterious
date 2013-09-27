<?php
require_once(dirname(__FILE__) . "/../config.inc.php");
require_once(dirname(__FILE__) . "/lib/class.iCalReader.php");

/**
 * An interface for the subscripts
 */
interface iSubscript
{
    public function execute($oMysqli);
}

/**
 * An interface for API calls
 */
interface iApiFunction
{
    public function allowAccess($iLevel);
    public function execute($iLevel, $aArgs);
}


/**
 * Returns a connection to mysql
 */
function getMysqli() {
  $oMysqli = new mysqli(CONFIG_DB_HOSTNAME, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
  if (mysqli_connect_errno()) {
    die('{"status": "failed", "error": "'  . addslashes(mysqli_error()) . '"}');
  }
  return $oMysqli;
}

/**
 * This method can be used to get a JSON object from a resource
 */
function getJSON($sUrl) {
    //Read data from URL
    $oContext = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
    $sData = file_get_contents($sUrl, false, $oContext);
    $oData = json_decode($sData);
    return $oData;
}

function getHtmlString($sUrl) {
    //Read data from URL
    $oContext = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
    $sData = file_get_contents($sUrl, false, $oContext);
    return $sData;
}

function getiCalEvents($sUrl) {
  $oiCal = new ICal($sUrl);
  return $oiCal;
}

?>