<?php
$con = new mysqli('localhost', 'root', '', 'music_shop');

if ($con->connect_errno) {
	$msg_title = "Database failure.";
	$msg_body = "Database is down. Error number " . $con->connect_errno;
	$user = false;
	include '../templates/msg-static.php';
	exit;
}
?>