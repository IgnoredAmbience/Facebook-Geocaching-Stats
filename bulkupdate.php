<?php
if(!isset($argv)) die("Must be run from command line!");
$nofbookcfg = 1;
require_once 'include.php';

$limit = '';
$count = 1;
if(isset($argv[1])) {
  $limit = "LIMIT ${argv[1]}, 50000";
  $count = $argv[1];
}

$query = "SELECT * FROM ids $limit;";
$result = sqlite_query($db, $query);
while($arr = sqlite_fetch_array($result)) {
  echo "$count. Working on ${arr['gcuname']}, setting img_${arr['fbid']}, ";
  $facebook->api_client->fbml_setRefHandle("img_${arr['fbid']}", imgdiv($arr['gcguid'], $arr['bartext'], $arr['logo']));
  echo "setting lnk_${arr['fbid']}, ";
	$facebook->api_client->fbml_setRefHandle("lnk_${arr['fbid']}", links($arr['gcguid'], $arr['gcuname']));
	echo "all done.\r\n";
  $count++;
}

echo "** Running updateFBML\r\n";
updateFBML();

/**/
?>
