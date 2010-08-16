<?php
require_once 'include.php';
$facebook->require_frame();
head();

function form() {
	global $facebook, $user, $db;
	
	
	$query = "SELECT * FROM ids WHERE fbid = '$user';";
	$result = sqlite_query($db, $query);
	if(sqlite_num_rows($result)) {
		$arr = sqlite_fetch_array($result);
		// sent to form
		$bartext = strtr($arr['bartext'], "`", "'");
		$gcuname = rawurldecode(str_replace('%2E', '.', $arr['gcuname']));
	}
	$sel1 = $sel2 = '';
	${'sel'.$arr['logo']} = 'selected="selected"';

	$box = imgdiv($arr['gcguid'], $arr['bartext'], $arr['logo']);
	$box .= links($arr['gcguid'], $arr['gcuname']);
	$ret = "<h2><fb:intl>Current stats banner preview:</fb:intl></h2>";
	$ret .= "<div style='margin: 10px;'>$box</div>";
	
$form = <<<END
<style type="text/css">
    option { padding: 1px 1px 1px 19px; background-repeat: no-repeat; background-position: 1px center; }
    h2 { padding-left: 10px; }
    .top_row td { padding-top: 0px; } /* Hack for add to profile button in editor */
</style>
<h2><fb:intl>Enter your geocaching.com details to show the stats banner on your profile</fb:intl></h2>
<fb:editor action="">
<fb:editor-text name="gun" value="$gcuname">
    <fb:fbml-attribute name="label"><fb:intl desc="Field label">Username</fb:intl></fb:fbml-attribute>
</fb:editor-text>
<fb:editor-text name="guid" value="${arr['gcguid']}">
    <fb:fbml-attribute name="label"><fb:intl desc="Field label">GUID</fb:intl></fb:fbml-attribute>
</fb:editor-text>
<fb:editor-custom><div style='padding: 5px; border: 1px solid #3B5998; background: #DDDDDD;'>
<fb:intl desc="GUID short howto">Just copy and paste a link to your geocaching profile in here.</fb:intl><br/>
        <fb:intl desc="GUID description">It should contain a bit that looks a little like this: {sample-guid}<fb:intl-token name="sample-guid">818ddfde-b830-4eef-80b8-0ca2f3480ffe</fb:intl-token></fb:intl></div></fb:editor-custom>
<fb:editor-text name="text" value="$bartext">
    <fb:fbml-attribute name="label"><fb:intl desc="Field label">Text to show on stats banner</fb:intl></fb:fbml-attribute>
</fb:editor-text>
<fb:editor-custom>
    <fb:fbml-attribute name="label"><fb:intl desc="Field label">Logo</fb:intl></fb:fbml-attribute>
    <select name="logo" style="border: 1px solid #8496BA;">
        <option $sel1 value="1" style="background-image: url('http://photos-c.ak.facebook.com/photos-ak-sctm/v43/1/535261609/app2_535261609_2443916522_1320.gif');"><fb:intl desc="Description for statbar logo">Geocaching Logo</fb:intl></option>
        <option $sel2 value="2"  style="background-image: url('http://ge.pythonmoo.co.uk/fbook/signal.gif');"><fb:intl desc="Description for statbar logo">Signal the Frog</fb:intl></option>
    </select>
</fb:editor-custom>
<fb:editor-buttonset><fb:editor-button name="submit" value="Set">
    <fb:fbml-attribute name="value"><fb:intl desc="Button text">Set Stats Banner</fb:intl></fb:fbml-attribute>
</fb:editor-button></fb:editor-buttonset>
<fb:editor-custom><fb:add-section-button section="profile" /></fb:editor-custom>
</fb:editor>

END;

	return $form.$ret;
}



$fbml = <<<END
<fb:ref handle="subtitle"/>
<fb:ref handle="style"/>
<fb:ref handle="img_$user"/>
<fb:ref handle="between"/>
<fb:ref handle="lnk_$user"/>
END;

if (isset($_POST['submit'])) {
	if(@preg_match('/[\dabcdef]{8}-[\dabcdef]{4}-[\dabcdef]{4}-[\dabcdef]{4}-[\dabcdef]{12}/i', $_POST['guid'], $gcguid)) {
		
		$gcguid = $gcguid[0];
		$gcuname = $_POST['gun'] ? $_POST['gun'] : '';
		$gcuname = str_replace('.', '%2E', rawurlencode($gcuname));
		$bartext = $_POST['text'] ? $_POST['text'] : 'View my profile';
		$bartext = strtr($bartext, "'", "`");
		$logo = intval($_POST['logo']);
		
		$facebook->api_client->fbml_setRefHandle("img_$user", imgdiv($gcguid, $bartext, $logo));
		$facebook->api_client->fbml_setRefHandle("lnk_$user", links($gcguid, $gcuname));
		
		$facebook->api_client->profile_setFBML('', $user, $fbml, '', '', $fbml);
		echo "<fb:success><fb:message><fb:intl desc='Confirmation message'>Stats banner updated</fb:intl></fb:message><fb:add-section-button section=\"profile\" /></fb:success>";
		
		$gcuname = sqlite_escape_string($gcuname);
		$bartext = sqlite_escape_string($bartext);
		
		$result = sqlite_query($db, "SELECT fbid FROM ids WHERE fbid = '$user';", SQLITE_BOTH, $error1);
		if($error1) error($error1);
		
		if(sqlite_num_rows($result)) {
			$sql = "UPDATE ids SET gcguid='$gcguid',gcuname='$gcuname',bartext='$bartext',logo='$logo' WHERE fbid='$user';";
		} else {
			$sql = "INSERT INTO ids VALUES('$user', '$gcguid', '$gcuname', '$bartext', '$logo');";
		}
		
		$result2 = sqlite_query($db, $sql, SQLITE_BOTH, $error2);
		
		if($error2) error($error);
		
		echo form();
		
	} else {
		if($_POST['submit'] == 'Yes') {
			$facebook->api_client->profile_setFBML('', $user);
			echo "<fb:success><fb:message>Stats panel removed</fb:message></fb:success>";
			echo "<a href='$appcallbackurl'>Return</a>";
		} else {
			if($_POST['submit'] == 'No') $facebook->redirect($appcallbackurl);
			
			echo '<fb:error><fb:message>'.(empty($guid) ? 'No' : 'Invalid').' GUID entered, do you want to remove the stats panel?</fb:message>';
			echo '<form action="" method="post">';
			echo '<input name="submit" type="submit" value="Yes"/>';
			echo '<input name="submit" type="submit" value="No"/></form></fb:error>';
		}
	}
} else {
	echo form();
}

footer();
?>

