<?php
	include '../includes/sql.php';
	header("Content-Type: application/json");
	
	if(!$user || !$user->get_is_admin()){
		echo '{"error":"Illegal access."}';
	} else if(!isset($_POST['category-name'])
			|| !isset($_POST['category-id'])) {
		echo '{"error":"Missing arguments."}';
	}	else {
		$id = intval($_POST['category-id']);
		$name = htmlspecialchars($_POST['category-name']);
		
		$prep = $con->prepare("
			UPDATE `categories` 
			SET category_name = ? 
			WHERE category_id = $id
		");
		
		$prep->bind_param("s", $name);
		
		if($prep->execute()) {
			// reply with what was actually inserted into the database.
			echo json_encode(
				array(
					'vars' => array(
						'category-name' => $name,
						'category-id' => $id
					)
				)
			);
		} else {
			echo '{"error":"' . $prep->error . '"}';
		}
		
		$prep->close();
	} 
	$con->close();
?>