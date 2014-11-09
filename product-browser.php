<?php
	include 'includes/sql.php';
	
	if(!$user){
		$msg_body = "You must be logged in to access this page.";
		$msg_title = "Illegal Access";
		$msg_type = "danger";
		include 'templates/msg-with-redirect.php';
		exit;
	}
	
	// manually declare to prevent any hackery.
	$self_url = "product-browser.php";
	
	$page = isset($_GET['p']) ? intval($_GET['p']) : 1;

	$result = $con->query("SELECT Round((COUNT(*) / 20) + 0.5) As cnt FROM `products`");
	$page_count = intval($result->fetch_assoc()['cnt']);

	$rows = products_from_request($page,$page_count);
	
	$manufacturers = $con->query("
		SELECT * FROM `manufacturers`
	");
	
	$categories = $con->query("
		SELECT * FROM `categories`
	");
	
?>
<html>
	<head>
		<title>Products</title>
		<?php include 'templates/head.php'; ?>
	</head>
	<body>
		<div class="container">
		
		<?php 
			include 'templates/navbar.php'; 
			include 'templates/search-util.php';
		?>
		
		<table class="table">
			<thead>
				<tr>
					<th class="hidden-xs hidden-sm"></th>
					<th>Product Name</th>
					<th>Manufacturer</th>
					<th>Category</th>
					<th>Product price</th>
					<th>Product quantity</th>
					<th>Purchase</th>
				</tr>
			</thead>
			<tbody>
				<?php while($row = $rows->fetch_assoc()): ?>
					<tr data-product-id="<?php echo $row['product_id']?>" >
						<td class="hidden-xs hidden-sm img-container">
							<img class="img-rounded" src="<?php echo 'assets/images/products/' . $row['product_img']; ?>"/>
						</td>
						<td><?php echo $row['product_name']; ?></td>
						<td><?php echo $row['manufacturer_name']; ?></td>
						<td><?php echo $row['category_name']; ?></td>
						<td><?php echo $row['product_price']; ?></td>
						<td><?php echo $row['product_quantity']; ?></td>
						<td>
							<button class="btn btn-default add-cart">Add to cart</button>
						</td>
					</tr>
				<?php endWhile; ?>
			</tbody>
			<!--<?php echo $rows->num_rows; ?> -->
		</table>
		
		<?php include 'templates/pager.php'; ?>
	
	
		<div id="pop-template" style="display:none">
		
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Quantity" width=1>
				<span class="input-group-btn">
					<button class="btn btn-default add-prod" id="add" type="button">Add</button>
				</span>
			</div>
			
		</div>

		<script>
		(function(){
			$('.add-cart').popover({
				html: true,
				content: function(){ 
					return $('#pop-template').html()
				},
				placement: 'top'
			}).on('shown.bs.popover', function(){
				
				// For adding the purchase, I'm going to need
				// the product id and quantity.
				var $tr = $(this).parent().parent(),
					$pvr = $(this);
				$('.add-prod').click(function(){
					var id = $tr.attr('data-product-id'),
						amount = $pvr.parent().find('input[type="text"]').val();
						
					if(amount == "" || isNaN(amount))
						alert("Amount is invalid.");
					else
						$.ajax({
							url:"ajax/add-purchase.php",
							data: {
								"product-id": id,
								"amount": amount
							},
							success: function(obj){
								if(obj.error){
									alert(obj.error);
								} else {
									$pvr.popover('hide');
								}
							},
							error: function(){
								alert("There was a server error processing the request.")
							}
						});
				});
			});
		})();
		</script>
	</body>
</html>

<?php $con->close(); ?>