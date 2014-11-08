<?php
	include '../includes/sql.php';
	header("Content-Type: application/json");
	
	if(!$user || !$user->get_is_admin()){
		echo '{"error":"Illegal access."}';
	} else if(!isset($_POST['manufacturer-id'])) {
		echo '{"error":"Missing arguments."}';
	}	else {
	
		$id = intval($_POST['manufacturer-id']);
	
		if($con->query("DELETE FROM `manufacturers` WHERE manufacturer_id = $id")){
			echo json_encode(
				array(
					'vars' => array(
						'manufacturer-id' => $id
					)
				)
			);
		} else {
			echo json_encode(
				array('error' => $prep->error)
			);
		}
	} 
	$con->close();
?>