<?php
	include '../includes/sql.php';
	header('Content-Type: application/json');
	
	if(!$user || !$user->get_is_admin()) {
		echo '{"error":"You do no have appropriate permissions"}';
	} else if(!isset($_POST['product-id']) 
			|| !isset($_POST['product-quantity'])
			|| !isset($_POST['product-name'])
			|| !isset($_POST['product-price'])
			|| !isset($_POST['product-category'])
			|| !isset($_POST['product-manufacturer'])){
		echo '{"error":"Missing arguments"}';
	} else {
		$price = floatval($_POST['product-price']);
		$name = $_POST['product-name'];
		$quantity = intval($_POST['product-quantity']);
		$manufacturer = intval($_POST['product-manufacturer']);
		$category = intval($_POST['product-category']);
		$id = intval($_POST['product-id']);
		
		$prep = $con->prepare("
			UPDATE `products`
			SET product_name = ?,
				product_price = ?,
				product_quantity = ?,
				product_category = $category,
				product_manufacturer = $manufacturer
			WHERE product_id = $id
			LIMIT 1
		");
		
		$prep->bind_param('sdi', $name, $price, $quantity);
		
		if($prep->execute()) {
			echo json_encode(array(
				'vars' => $_POST
			));
		} else {
			echo '{"error":"' . $prep->error . '"}';
		}
		
		$prep->close();
	}
	
	$con->close();
?>