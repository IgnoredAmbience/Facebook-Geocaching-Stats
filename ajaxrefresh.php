<?php
require_once 'include.php';

if($user = @intval($_POST['fb_sig_profile'])) {
  $query = "SELECT gcguid, bartext, logo FROM ids WHERE fbid = '$user';";
	$result = sqlite_query($db, $query);
	if(sqlite_num_rows($result)) {
		$arr = sqlite_fetch_array($result);
  	echo "<img src='".imgurl($arr['gcguid'], $arr['bartext'], $arr['logo'])."' />";
	}
}
