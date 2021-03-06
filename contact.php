<?php 
	include 'includes/sql.php';
	$fields = array('first-name', 'last-name', 'message',
		'address', 'city', 'province', 'email', 'postal-code', 'phone');
	
	$all_set = array_reduce($fields, function($accu, $field){
		return $accu && isset($_POST[$field]);
	}, true);
	
	if($all_set){
		// Escape html characters to make sure
		// the email sent won't have any malicious
		// code when you open it.
		$input = array_reduce($fields, function($result, $field){
			$result[$field] = htmlspecialchars($_POST[$field]);
			return $result;
		}, array());
		
		$message = "
			<p>
				{$input['message']}
			</p>
			
			
			<address>
				<strong>{$input['last-name']}, {$input['first-name']}</strong><br/>
				{$input['address']}<br/>
				{$input['city']}, {$input['province']} {$input['postal-code']}<br/>
				Phone number: {$input['phone']}<br/>
			</address>
			";
		
		$headers = "
			Content-type: text/html; charset=utf-8\r\n
			From: {$input['first-name']} {$input['last-name']} <{$input['email']}>\r\n
			Reply-To: {$input['email']}";
			
		
		/*bool mail ( 
			string $to , 
			string $subject , 
			string $message [, 
			string $additional_headers [, 
			string $additional_parameters ]] )*/
		if(mail(
				'hellomynameisjony@gmail.com', 
				"An email from {$input['first-name']} {$input['last-name']}",
				$message,
				$headers)){
			
			$msg_title = 'Email Sent';
			$msg_body = 'Thank you for contacting us!';
			include 'templates/msg-with-redirect.php';
			$con->close();
			exit;
		} else {
			$error = 'There was an error processing your message.';
		}
	}
	
?>

<html>
	<head>
		<?php include 'templates/head.php'; ?>
		<style>
		#map-block {
			margin-top: 15px;
			margin-bottom: 15px;
		}
		</style>
	</head>
	<body>
		<div class="container">
			<?php include 'templates/navbar.php'; ?>
			<h3>Where you can find us</h3>
			<div class="row" id="map-block">
				<div class="col-md-8">
					<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2800.0287889882466!2d-75.685125!3d45.428920999999995!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4cce050491a41235%3A0xe5bb41dd9efc55b6!2sSteve&#39;s+Music+Store!5e0!3m2!1sen!2sca!4v1417452697946" width="600" height="450" frameborder="0" style="border:0"></iframe>
					<br/><br/>
				</div>
				<div class="col-md-3">
					<address>
						<strong>Steve's Music Store</strong><br/>
						308 Rideau St<br/>
						Ottawa, ON K1N 5Y5<br/>
						Canada<br/>
						<abbr title="Phone">P:</abbr> (613) 
					</address>
					
				</div>
			</div>
			
			<h3>Tell Us What You Think</h3>
			
			<form class="form" method="POST" action="contact.php">
			
				<div class="row">
					<div class="form-group col-md-6">
						<label>First Name</label>
						<input type="text" name="first-name" class="form-control" autofocus/>
					</div>
					<div class="form-group col-md-6">
						<label>Last Name</label>
						<input type="text" name="last-name" class="form-control"/>
					</div>
				</div>
			
				<div class="row">
					<div class="form-group col-md-6">
						<label>Address</label>
						<input type="text" name="address" class="form-control"/>
					</div>
					<div class="form-group col-md-6">
						<label>City</label>
						<input type="text" name="city" class="form-control"/>
					</div>
				</div>
			
				<div class="row">
					<div class="form-group col-md-4">
						<label>Province</label>
						<input type="text" name="province" class="form-control"/>
					</div>
					
					<div class="form-group col-md-2">
						<label>Postal Code</label>
						<input type="text" name="postal-code" class="form-control"/>
					</div>
					
					<div class="form-group col-md-6">
						<label>Phone Number</label>
						<input type="tel" name="phone" class="form-control"/>
					</div>
				</div>
				
				<div class="row">
					<div class="form-group col-md-6">
						<label>Email</label>
						<input type="email" name="email" class="form-control"/>
					</div>
				</div>
				
				<div class="row">
					<div class="form-group col-md-12">
						<label>Message</label>
						<textarea name="message" class="form-control"></textarea>
					</div>
				</div>
				<input type="submit" value="Submit" class="btn btn-default"/>
			</form>
			
		</div>
	</body>
</html>
<?php $con->close() ?>