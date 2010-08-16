<?php
require_once 'include.php';
$facebook->require_frame();
head();

// Simple script to output box content of all friends

$users = $facebook->api_client->friends_getAppUsers();

if(empty($users)) {
	echo '<fb:explanation><fb:message>None of your friends also have this app installed. Why not invite them to use it too?</fb:message>Their stats will appear here when they add it.<br/>'.share_button().'</fb:explanation>';
} else {
	echo "<table class=\"friendTable\" cellpadding=\"2\" cellspacing=\"2\" border=\"0\" height=\"100%\">\n";
	
	$i = 1;
	foreach($users as $uid) {
		$query = "SELECT * FROM ids WHERE fbid = '$uid';";
		$result = sqlite_query($db, $query);
		if(!sqlite_num_rows($result)) {
			$profilebox = $facebook->api_client->profile_getFBML($uid);
		} else {
			$arr = sqlite_fetch_array($result);
			$profilebox = imgdiv($arr['gcguid'], $arr['bartext']).links($arr['gcguid'], $arr['gcuname']);
		}
			
		if(!$profilebox) continue;

		if($i%2) echo "<tr>\n";
		echo "<td style='text-align: center;'>";
		echo "<a href=\"http://www.facebook.com/profile.php?id=$uid\"><fb:profile-pic uid=\"$uid\" size=\"q\"/></a>";
		echo "<br/><a href=\"http://www.facebook.com/profile.php?id=$uid\"><fb:name uid=\"$uid\"/></a>";
		echo "</td><td>$profilebox</td>\n";
		if(!($i%2)) echo "</tr>\n\n";
		else echo "<td width='5'></td>";
		$i++;
	}
	if(!($i%2)) echo "</tr>";
	echo "</table>";
	echo '<div style="font-size: 11px; border: 1px solid #BDC7D8; padding: 5px; margin-top: 20px;">Do you want to share this app with more of your friends?'.share_button().'</div>';
}

footer();

?>
