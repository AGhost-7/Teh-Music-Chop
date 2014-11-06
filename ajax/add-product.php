<?php
	include '../includes/sql.php';
	header('Content-Type: application/json');
	
	if(!$user || !$user->get_is_admin()) {
		echo '{"error":"You do no have appropriate permissions"}';
	} else if(!isset($_POST['product-quantity'])
			|| !isset($_POST['product-name'])
			|| !isset($_POST['product-price'])
			|| !isset($_POST['product-manufacturer'])){
		echo '{"error":"Missing arguments"}';
	} else {
		
		$price = floatval($_POST['product-price']);
		$name = htmlspecialchars($_POST['product-name']);
		$quantity = $_POST['product-quantity'];
		$manufacturer = $_POST['product-manufacturer'];
		$category = $_POST['product-category'];
		
		$prep = $con->prepare("
			INSERT INTO `products`(
				product_name,
				product_quantity,
				product_price,
				product_manufacturer,
				product_category
			)
			VALUES
			(?,?,?,?,?)
		");
		
		$prep->bind_param('sidii', $name, $quantity, $price, $manufacturer, $category);
		
		if($prep->execute()) {
			echo json_encode(array(
				'vars' => $_POST, 
				'id' => $prep->insert_id
			));
		} else {
			echo '{"error":"' . $prep->error . '"}';
		}
		
		$prep->close();
	}
	
	$con->close();
?>