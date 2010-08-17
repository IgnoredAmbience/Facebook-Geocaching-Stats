<?php
//die('<fb:error message="Temporarily offline for code updates."/>');
$start_time = microtime(true);
require_once 'api/facebook.php';
require_once 'config.php';

$facebook = new Facebook($appapikey, $appsecret);

if(!isset($nofbookcfg) && !isset($nologin)) {
  error_reporting(0);
  $user = $facebook->require_login();
  
  //catch the exception that gets thrown if the cookie has an invalid session_key in it
  try {
    if (!$facebook->api_client->users_isAppAdded()) {
      $facebook->redirect($facebook->get_add_url());
    }
  } catch (Exception $ex) {
    //this will clear cookies for your application and redirect them to a login prompt
    $facebook->set_user(null, null);
    $facebook->redirect($appcallbackurl);
  }
} else {
  if(isset($nofbookcfg))
    $facebook->set_user($offline_user, $offline_key);
}

function head() {
	echo <<<END
<fb:dashboard>
	<fb:action href="index.php"><fb:intl desc="Navigation link">Edit stats banner</fb:intl></fb:action>
    <fb:action href="friends.php"><fb:intl desc="Navigation link">View your friends' stats</fb:intl></fb:action>
</fb:dashboard>
END;
}

function footer() {
	global $start_time;
	$time = microtime(true) - $start_time;
	echo '<div style="font-size: 9px; color: #3B5998; border-top: solid 1px #B7B7B7; margin-top: 10px;">This application is not created nor endorsed by geocaching.com. The Groundspeak Geocaching Logo is a trademark of <a href="http://www.groundspeak.com/">Groundspeak, Inc.</a> Used with permission.  (Exec time: '.round($time,3).' seconds)</div>';
}

function imgurl($gcguid, $bartext, $logo=1) {
	return "http://img.geocaching.com/stats/img.aspx?uid=$gcguid&txt=$bartext&bg=$logo";
}

function imgdiv($gcguid, $bartext, $logo=1) {
   global $internalurl;
	return "<div id='gcimg' clickrewriteurl='$internalurl/ajaxrefresh.php' clickrewriteid='gcimg' clickrewriteform='unused'><img src='".imgurl($gcguid, $bartext, $logo)."' /></div>";
}

function links($gcguid, $gcuname) {
	return "<a href='http://www.geocaching.com/profile/?guid=$gcguid'>Profile</a> | <a href='http://www.geocaching.com/seek/nearest.aspx?ul=$gcuname'>Finds</a> | <a href='http://www.geocaching.com/seek/nearest.aspx?u=$gcuname'>Hides</a>";
}

function share_button() {
	return '<fb:share-button class="meta"><meta name="title" content="Geocaching Stats on Facebook"/><meta name="description" content="Insert geocaching stats onto your facebook profile with this app."/><link rel="image_src" href="http://photos-c.ak.facebook.com/photos-ak-sctm/v43/1/535261609/app3_535261609_2443916522_3851.gif"/><link rel="target_url" href="http://apps.facebook.com/geocachestats/"/></fb:share-button>';
}

function updateFBML() {
  global $facebook, $internalurl, $appcallbackurl;
  $facebook->api_client->fbml_setRefHandle('style', '<fb:narrow><style>div div { width: 198px; height: 50px; border-right: solid #000 1px; overflow: hidden; margin-left: -8px; }</style><span></span></fb:narrow>');
  $facebook->api_client->fbml_setRefHandle('subtitle', "<fb:subtitle><a href='$appcallbackurl/friends.php'>View your friends' stats</a></fb:subtitle>");
  $facebook->api_client->fbml_setRefHandle('between', "<span  clickrewriteurl=$internalurl/ajaxrefresh.php' clickrewriteform='unused' clickrewriteid='gcimg' style='color: grey; font-size: 9px; font-style: italic;'>Click to show current counts<br/></span><form id='unused'></form>");
}

function error($msg, $die = 0) {
    echo <<<END
<fb:error><fb:message>
<fb:intl desc="Error message">Error: {message}
    <fb:intl-token name="message">$msg</fb:intl-token>
</fb:intl>
</fb:message></fb:error>
END;
    if($die) die;
}

?>
