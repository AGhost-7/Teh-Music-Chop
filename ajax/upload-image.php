<?php
	
	$file = &$_FILES['image'];
	$relative_path = 'assets/images/products/' . basename($file['name']);
	$full_path = $_SERVER['DOCUMENT_ROOT']. '/' . $relative_path;
	$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
	
	header('Content-Type: application/json');
	
	if(file_exists($full_path)){
		echo '{"error":"File of same name already exists."}';
		
	} else if($file['size'] > 500000) {
		echo '{"error":"File is too large."}';
	} else if($ext != 'jpg' 
		&& $ext != 'png' 
		&& $ext != 'jpeg'
		&& $ext != 'gif'){
		echo '{"error":"File' . $ext . ' must be an image."}';
	} else {
		if(move_uploaded_file($_FILES['image']['tmp_name'], $full_path)){
			echo '{"result":"' . $relative_path . '"}';
		} else {
			echo '{"error":"Error uploading file."}';
		}
	}

?>