<?php
	include 'includes/sql.php';
	if(isset($_POST['user-name']) && isset($_POST['password'])) {
		$user_name = $_POST['user-name'];
		$password = $_POST['password'];
		
		if(!preg_match("/^[A-z0-9_#%&*!@$-]{6,}$/", $user_name)) {
			$error = "Your user name doesn't follow the naming rules.";
		} else if(!preg_match("/^[A-z0-9_#%&*!@$-]{6,}$/", $password)) {
			$error = "Your password doesn't follow the character rules.";
		} else {
		
			$user = User::insert_new($user_name, $password);
			
			if(!user) {
				$error = "There is already a user with your chosen name.";
			} else {
				$msg_title = "Registration Success";
				$msg_body = "Registration was a success, you may now login.";
				include 'templates/msg-with-redirect.php';
				$con->close();
				exit;
			}
		}
	}
?>
<html>
	<head>
		<title>Registration</title>
		<?php include 'templates/head.php'; ?>
	</head>
	<body>
	<div class="container">
		<?php include 'templates/navbar.php'; ?>
		<?php if($user):?>
			<div class="alert alert-warning">
				You seem to be already logged in... Wanna <a class="alert-link" href="logout.php">logout</a>?
			</div>
		<?php else: ?>
		
		<?php if(isset($error)): ?>
		<div class="container-fluid">
			<div class="alert alert-danger">
				<?php echo $error; ?>
			</div>
		</div>
		<?php endIf; ?>
				
		<form action="registration.php" method="POST" role="form"/>
			<div class="form-group col-xs-7">
				<label>User name </label>
				<p>
					<small>Minimum length of 6 characters. Legal characters are alphanumeric, and the following symbols: _#%&*!@$-</small>
				</p>
				<input type="text" class="form-control" name="user-name" value="<?php if($error) echo $user_name; ?>"/>
			</div>
			<div class="form-group col-xs-7">
				<label>Password</label>
				<p>
					<small>Minimum length of 6 characters. Legal characters are alphanumeric, and the following symbols: _#%&*!@$-</small>
				</p>
				<input type="password" class="form-control" name="password"/>
			</div>
			<div class="col-xs-7">
				<input type="submit" class="btn btn-default" value="Send"/>
			</div>
		</form>
		<?php endIf; ?>
	</div>
	</body>
</html>

<?php $con->close(); ?>