<?php
	include '../includes/sql.php';
	header("Content-Type: application/json");
	
	if(!$user || !$user->get_is_admin()){
		echo '{"error":"Illegal access."}';
	} else if(!isset($_POST['manufacturer-name'])
			|| !isset($_POST['manufacturer-id'])) {
		echo '{"error":"Missing arguments."}';
	}	else {
		$id = intval($_POST['manufacturer-id']);
		$name = htmlspecialchars($_POST['manufacturer-name']);
		
		$prep = $con->prepare("
			UPDATE `manufacturers` 
			SET manufacturer_name = ? 
			WHERE manufacturer_id = $id
		");
		
		$prep->bind_param("s", $name);
		
		if($prep->execute()) {
			// reply with what was actually inserted into the database.
			echo json_encode(
				array(
					'vars' => array(
						'manufacturer-name' => $name,
						'manufacturer-id' => $id
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