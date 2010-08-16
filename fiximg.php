<?php
require_once 'include.php';

if(!isset($_REQUEST["fb_sig_in_canvas"])) {
	if (isset($_SERVER['HTTP_REFERER'])) {
		$user = explode('id=', $_SERVER['HTTP_REFERER']);
		$user = $user[1];
	}
	$facebook->redirect($appcallbackurl.'fiximg.php?user='.$user);
	print_r($_SERVER);
	print_r($_REQUEST);
	echo $user;
}

if(isset($_GET['user'])) {
	$user = intval($_GET['user']);
}
$result = sqlite_query($db, "SELECT gcguid,bartext FROM ids WHERE fbid = '$user';", SQLITE_BOTH, $error1);
if($error1) die('<fb:error message="Error when connecting to the database: '.$error1.'"/>');
if($array = sqlite_fetch_array($result)) {
	$facebook->api_client->fbml_refreshImgSrc(imgurl($array['gcguid'], $array['bartext']));
	echo '<fb:success><fb:message>Refreshed <fb:name uid="'.$user.'" possessive="true" /> stat bar</fb:message>(Stat bars are also refreshed automatically each day, sometimes facebook messes up, though)</fb:success>';
	//$facebook->redirect('http://www.facebook.com/profile.php');
} else {
	echo '<fb:error message="User ID not found"/>';
}

footer();
?>
