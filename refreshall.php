<?php
if(!isset($argv)) die("Must be run from command line!");
$nofbookcfg = 1;
require_once 'include.php';
$sqlresult = sqlite_unbuffered_query($db, "SELECT count(1) FROM ids;");
$numrows = sqlite_fetch_single($sqlresult);

$hour = date('G');
$lower = ceil(($hour/24)*$numrows);
$count = ceil($numrows/24);

$sql = "SELECT gcguid,bartext,logo FROM ids LIMIT $lower,$count;";
echo $sql;
$sqlresult = sqlite_query($db, $sql, SQLITE_BOTH, $error1);
if($error1) die('Error when connecting to the database: '.$error1.'"/>');

$results = array();
while($array = sqlite_fetch_array($sqlresult)) {
	$url = imgurl($array['gcguid'], $array['bartext'], $array['logo']);
	$result[0] = $url;
	$result[1] = $facebook->api_client->fbml_refreshImgSrc($url);
	if(!($result[1] === '1'))
	  $results[] = $result;
}

var_dump($results);

echo "Time taken: ", microtime(true) - $start_time;

?>
