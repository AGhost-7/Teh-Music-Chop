<?php
	include '../includes/sql.php';
	include '../includes/product.php';
	
	header('Content-Type: application/json');
	
	if(!$user || !$user->get_is_admin()) {
		echo '{"error":"You do no have appropriate permissions"}';
	} else {
		$product = product::from_post();
		if($product->error){
			echo '{"error":"' . $product->error . '"}';
		} else if(!$result = $product->db_insert()){
			echo '{"error":"' . $product->error . '"}';
		} else {
			echo json_encode($result);
		}
	}
	
	$con->close();
?>