<?php include 'includes/sql.php'; ?>
<html>
	<head>
		
		<title>Welcome!</title>
		<?php include 'templates/head.php'; ?>
	</head>
	<body>
		<div class="container">
			<?php include 'templates/navbar.php'; ?>
			<?php if($user): ?>		
				<p>
					Welcome to this random online shopping site, <?php echo $user->get_user_name(); ?>! You can logout 
					<a href='logout.php'>here</a>. Check out the <a href="product-browser.php">product browser</a>.
				</p>
			<?php else:	?>
				<p>Please login <a href="login.php">here</a> or register <a href="registration.php">here</a> if you haven't already.</p>
			<?php endIf; ?>
		</div>
	</body>
	
</html>

<?php $con->close(); ?>