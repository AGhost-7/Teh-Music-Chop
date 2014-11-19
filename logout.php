<?php
	include 'includes/sql.php';
	
	$user->logout();
	
	// so that the navbar doesn't show the name...
	$user = false;
	
	$msg_body = "Successfully logged out! Redirecting to home page.";
	$msg_title = "Logout";
	
	include 'templates/msg-with-redirect.php';
	
	$con->close();
?>