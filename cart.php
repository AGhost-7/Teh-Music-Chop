<?php 
	include 'includes/sql.php';
	
	if(!$user) {
		$msg_title = "Illegal Access";
		$msg_body = "You must my logged on to access this page.";
		$msg_type = "danger";
		include 'templates/msg-with-redirect.php';
		$con->close();
		exit;
	}
	
	$purchases = $user->purchases();
?>
<html>
	<head>
		<title>My Cart</title>
		<?php include 'templates/head.php'; ?>
	</head>
	<body>
		<div class="container">
			<?php include 'templates/navbar.php'; ?>
			<table class="table">
				<thead>
					<tr>
						<th>Product Name</th>
						<th>Price</th>
						<th>Amount</th>
						<th>Sub-total</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
				<?php while($purchase = $purchases->fetch_assoc()): ?>
					<tr data-purchase-id="<?php echo $purchase['purchase_id']; ?>">
						<td><?php echo $purchase['product_name']; ?></td>
						<td><?php echo $purchase['product_price']; ?></td>
						<td><?php echo $purchase['purchase_amount']; ?></td>
						<td><?php echo $purchase['product_price'] * $purchase['purchase_amount']; ?></td>
						<td><button class="btn btn-sm btn-default remove-btn">Remove</button></td>
					</tr>
				<?php endWhile; ?>
				</tbody>
			</table>
		</div>
		<script>
		(function(){
			$('.remove-btn').click(function(){
				var $b = $(this),
					$tr = $b.parent().parent(),
					purchaseId = $tr.attr('data-purchase-id');
					
				$.ajax({
					url: "ajax/remove-purchase.php",
					data: {
						"purchase-id": purchaseId
					},
					success: function(data){
						if(data.error){
							alert(data.error);
						} else {
							$tr.remove();
						}
					},
					error: function(){
						alert('There was an error processing the request');
					}
				});
				
			});
		})();
		</script>
	</body>
</html>