<?php
	session_start();
	session_unset();
	session_destroy();
	
	$msg_body = "Successfully logged out! Redirecting to home page.";
	$msg_title = "Logout";
	include 'templates/msg-with-redirect.php';
?>