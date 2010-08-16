<?php
$nologin = true;
require_once 'include.php';

if(!isset($_REQUEST['fb_sig_profile_id'])) {
  die();
} else {
  $uid = (int) $_REQUEST['fb_sig_profile_id'];
}


$query = "SELECT * FROM ids WHERE fbid = '$uid';";
$result = sqlite_query($db, $query);
if(!sqlite_num_rows($result)) {

} else {
  $arr = sqlite_fetch_array($result);
  $img = imgurl($arr['gcguid'], $arr['bartext']);
  $links = links($arr['gcguid'], $arr['gcuname']);
  echo "<img src='$img' /><br />$links";
}

footer();

?>
