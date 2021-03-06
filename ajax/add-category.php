<?php
	include '../includes/sql.php';
	header("Content-Type: application/json");
	
	if(!$user || !$user->get_is_admin()){
		echo '{"error":"Illegal access."}';
	} else if(!isset($_POST['category-name'])) {
		echo '{"error":"Missing arguments."}';
	}	else {
		$prep = $con->prepare("
			INSERT INTO `categories`(category_name) VALUES(?)
		");
		
		$prep->bind_param("s",$_POST['category-name']);
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