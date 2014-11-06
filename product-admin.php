<?php
	include 'includes/sql.php';
	
	if(!$user || !$user->get_is_admin()){
		$msg_title = 'Illegal access';
		$msg_body = 'You must have administrative rights to access this page.';
		include 'templates/msg-with-redirect.php';
		$con->close();
		exit;
	}
	
	$page = isset($_GET['p']) ? intval($_GET['p']) : 1;

	$result = $con->query("SELECT Round((COUNT(*)/20)+0.5) As cnt FROM `products`");
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
		<title>Product Administration</title>
		<?php include 'templates/head.php'; ?>
	</head>
	<body>
		<div class="container">
			<?php include 'templates/navbar.php'; ?>
			<div>
				<button class="btn btn-success btn-sm" id="add-btn">Add Product</button>
			</div>
			<table class="table">
				<thead>
					<tr>
						<th class="hidden-xs hidden-sm"></th>
						<th>Product Name</th>
						<th>Manufacturer</th>
						<th>Category</th>
						<th>Product price</th>
						<th>Product quantity</th>
						<th></th>
					</tr>
				</thead>
				<tbody id="table-body">
					<?php while($row = $rows->fetch_assoc()): ?>
						<tr data-product-id="<?php echo $row['product_id']?>">
							<td class="hidden-xs hidden-sm img-container">
								<img class="img-rounded" src="<?php echo 'assets/images/products/' . $row['product_img']; ?>"/>
							</td>							
							<td><?php echo $row['product_name']; ?></td>
							<td data-manufacturer-id="<?php echo $row['product_manufacturer']; ?>"><?php echo $row['manufacturer_name']; ?></td>
							<td data-category-id="<?php echo $row['product_category']; ?>"><?php echo $row['category_name']; ?></td>
							<td><?php echo $row['product_price']; ?></td>
							<td><?php echo $row['product_quantity']; ?></td>
							<td class="">
								<div class="btn-group-vertical">
								<button class="btn btn-sm btn-warning mod-product">Modify</button>
								<button class="btn btn-sm btn-danger del-product">Delete</button>
								</div>
							</td>
						</tr>
					<?php endWhile; ?>
				</tbody>
			</table>
		
			<nav>
				<ul class="pager">
				<?php 
				if($page > 1) 
					echo '<li><a href="product-admin.php?p=' . ($page - 1). '">&laquo; Previous</a></li>';
				
				if($page < $page_count) 
					echo '<li><a href="product-admin.php?p=' . ($page + 1). '">Next &raquo;</a></li>';
				?>
				</ul>
			</nav>
			
			<div class="modal fade" id="product-editor">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">
								<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
							</button>
							<h4 class="modal-title">Edit -</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label>Product Id: </label> <span class="form-control-static" id="product-id">1</span>
							</div>
							
							<div class="form-group">
								<img id="image-display" class="img-rounded" src=""/><br /><br />
								<button id="image-button" class="btn btn-default btn-sm">Change Image</button>
								<input style="display:none;" name="product-img" type="file" accept="image/*" id="image-editor"/>
							</div>
							
							<div class="form-group">
								<label>Product Name</label>
								<input type="text" name="product-name" class="form-control" id="name-editor"/>
							</div>
							
							<div class="form-group">
								<label>Manufacturer</label>
								<select class="form-control" name="product-manufacturer" id="manufacturer-editor">
								<?php
									while($man = $manufacturers->fetch_assoc()){
										echo '<option value="' . $man['manufacturer_id'] . '">'
											. $man['manufacturer_name'] . '</option>';
									}
								?>
								</select>
							</div>
							
							<div class="form-group">
								<label>Category</label>
								<select class="form-control" name="product-category" id="category-editor">
								<?php
									while($cat = $categories->fetch_assoc()){
										echo '<option value="' . $cat['category_id'] . '">' 
											. $cat['category_name'] .'</option>';
									}
								?>
								</select>
							</div>
							
							<div class="form-group">
								<label>Product Price</label>
								<input type="text" name="product-price" class="form-control" id="price-editor"/>
							</div>
							
							<div class="form-group">
								<label>Product Quantity</label>
								<input type="text" name="product-quantity" class="form-control" id="quantity-editor"/>
							</div>
							
							
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" id="save-btn" class="btn btn-primary">Save changes</button>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<script src="/assets/javascript/product-admin.js"></script>
		
	</body>
</html>