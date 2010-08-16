<pre>
<?php
/*$db = sqlite_open('db');

$r = sqlite_query($db, 'select * from ids where gcuname glob "*%25*"');
$arr = sqlite_fetch_all($r);
foreach($arr as $item) {
    $fixed = preg_replace("/%(25)*/", "%", $item[2]);
    sqlite_query($db, "UPDATE ids SET gcuname = '$fixed' WHERE fbid='${item[0]}'");
}*/

?>
</pre>