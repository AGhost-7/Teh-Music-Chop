<?php
	include 'includes/sql.php';
	if($user) {
	
		//hmm already logged in eh?
		$msg_title = "Logged in";
		$msg_body = "Already logged in, redirecting to home page.";
		include 'templates/msg-with-redirect.php';
		exit;
		
	} else if(isset($_POST['user-name']) || $_POST['password']) {
	
		if($user = User::login($_POST['user-name'], $_POST['password'], isset($_POST['cookie']))) {
			$msg_title = "Login Success";
			$msg_body = "Login succesful ". $user->get_user_name() . "! Redirecting to home page.";
			include 'templates/msg-with-redirect.php';
			$con->close();
			exit;
		} else {
			$error = "Username or password incorrect.";
		}
	}
?>
<html>
	<head>
		<title>Login</title>
		<?php include 'templates/head.php'; ?>
	</head>
	<body>
	<div class="container">
		<?php include 'templates/navbar.php'; ?>
		
		<?php if(isset($error)):	?>
			<div class="container-fluid">
				<div class="alert alert-danger">
					<?php echo $error; ?>
				</div>
			</div>
		<?php endIf; ?>
		
		<form action="login.php" method="POST" role="form"/>
			
			<div class="form-group col-xs-7">
				<label>User name</label>
				<input type="text" class="form-control" name="user-name" value="<?php if(isset($error)) echo $_POST['user-name']; ?>"/>
			</div>
			
			<div class="form-group col-xs-7">
				<label>Password</label>
				<input type="password" class="form-control" name="password"/>
			</div>
			<div class="checkbox col-xs-7">
				<label>
					<input type="checkbox" name="cookie"/> Remember me
				</label>
			</div>
			<div class="col-xs-7">
				<input class="btn btn-default" type="submit" value="Send"/>
			</div>
			
		</form>

		
	</div>
	</body>
</html>

<?php $con->close(); ?>