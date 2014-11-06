<?php
	/* Not currently used... */
	include '../includes/sql.php';
	header("Content-Type: application/json");
	if($user) {
		$id = intval($_GET['product_id']);
		$amount = intval($_GET['amount']);
		if($amount === 0) {
			if($con->query("
				DELETE FROM `purchases` 
				WHERE purchase_user = $user->get_user_id()
					AND purchase_product = $id
					AND is_payed = FALSE
			")) {
				echo '{}';
			} else {
				echo '{"error": "There was and error processing the request"}';
			}
		} else {
			if($con->query("
				CALL mod_purchase($user->get_user_id(), $id, $amount)
			")) {
				echo '{}';
			} else {
				echo '{"error": "There was an error processing the request."}';
			}
		}
	} else {
		echo '{"error":"User is not logged in"}';
	}
	$con->close();
?>