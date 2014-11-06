<?php
	include '../includes/sql.php';
	header('Content-Type: application/json');
	
	if(!$user || !$user->get_is_admin()) {
		echo '{"error":"You do no have appropriate permissions"}';
	} else if(!isset($_POST['product-id'])){
		echo '{"error":"Missing arguments"}';
	} else {
		
		$id = intval($_POST['product-id']);
		
		if($con->query("DELETE FROM products WHERE product_id = $id")) {
			echo json_encode(array(
				'vars' => $_POST
			));
		} else {
			echo '{"error":"' . $con->error . '"}';
		}

	}
	
	$con->close();
?>