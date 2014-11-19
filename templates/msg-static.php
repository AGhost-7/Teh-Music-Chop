<?php
	/**
	 * Stock message template. Use include then exit
	 * before the html in the php file you want to
	 * use this for.
	 */
	
	if(!isset($msg_type)){
		$msg_type = 'success';
	}
	
	$msg_class = 'alert alert-' . $msg_type;
?>
<html>
	<head>
		<title><?php echo $msg_title; ?></title>
		<?php include 'templates/head.php'; ?>
	</head>
	<body>
		<div class="container">
			<?php include 'templates/navbar.php'; ?>
			<p class="<?php echo $msg_class?>"><?php echo $msg_body; ?></p>
		</div>
	</body>
</html>