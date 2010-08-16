<?php
require_once 'include.php';
if($user != 535261609) {
	die('<fb:error message="No permission to do this!"/>');
}

updateFBML();

?>
