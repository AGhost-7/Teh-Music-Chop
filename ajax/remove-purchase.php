<?php
	include '../includes/sql.php';
	header('Content-Type: application/json');
	if(!$user){
		echo '{"error":"User is not logged in."}';
	} else if(!isset($_GET['purchase-id'])) {
		echo '{"error":"Arguments are missing."}';
	} else {
		$id = intval($_GET['purchase-id']);
		if($id < 1) {
			echo '{"error":"Invalid arguments"}';
		} else {
			$user_id = $user->get_user_id();
			
			if($con->query("
				DELETE FROM `purchases`
				WHERE purchase_user = $user_id
					AND purchase_is_payed = FALSE
					AND purchase_id = $id
				LIMIT 1
			"))
				echo '{"error":"' . $con->error . '"}';
			else 
				echo '{}';
		}
	}
	$con->close();
?>