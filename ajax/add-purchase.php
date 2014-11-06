<?php
	include '../includes/sql.php';
	header('Content-Type: application/json');
	
	if(!$user) {
		echo '{"error":"User is not logged in."}';
	} else if(!isset($_GET['product-id']) || !isset($_GET['amount'])) {
		echo '{"error":"Data from request missing."}';
	} else {
	
		$product_id = intval($_GET['product-id']);
		$amount = intval($_GET['amount']);
		$user_id = $user->get_user_id();
		if($amount < 1) {
			echo '{"error":"Amount is invalid."}';
		} else if($con->query("
			INSERT INTO `purchases`(purchase_amount,purchase_user,purchase_product)
			VALUES
			($amount, $user_id, $product_id)
		")) {
			echo "{}";
		} else {
			echo '{"error":"SQL error number ' . $con->errno . '"';
		}
		
	}
	$con->close();
?>