<?php
	
	// so what is going to be persisted through to the form?
	$search_name = isset($_GET['n']) ? $_GET['n'] : "";
	$search_category = isset($_GET['c']) ? $_GET['c'] : "0";
	$search_manufacturer = isset($_GET['m']) ? $_GET['m'] : "0";
	$search_min_price = isset($_GET['p-min']) ? $_GET['p-min'] : "";
	$search_max_price = isset($_GET['p-max']) ? $_GET['p-max'] : "";
	$search_order = isset($_GET['o']) ? $_GET['o'] : "0";
?>


<div class="panel panel-default">

<form class="form-horizontal panel-body" action="<?php echo $self_url; ?>" method="GET">
	
	<div class="form-group">
		<label class="col-sm-2">Name</label>
		<div class="col-sm-10">
			<input type="text" name="n" class="form-control" value="<?php echo $search_name;?>"/>
		</div>
	</div>
	
	<div class="form-group">
		
		<label class="col-sm-2">Category</label>
		<div class="col-sm-4">
			<select name="c" class="form-control" value="2" id="search-category">
				<option value="0"></option>
				<?php 
				while($cat = $categories->fetch_assoc()) {
					echo '<option value="' . $cat['category_id'] . '">' 
						. $cat['category_name'] .'</option>';
				}
				?>
			</select>
		</div>
	
		<label class="col-sm-2">Manufacturer</label>
		<div class="col-sm-4">
			<select name="m" class="form-control" id="search-manufacturer">
				<option value="0"></option>
				<?php 
				while($man = $manufacturers->fetch_assoc()){
					echo '<option value="' . $man['manufacturer_id'] . '">'
						. $man['manufacturer_name'] . '</option>';
				}
				?>
			</select>
		</div>
		
	</div>
	<div class="">
		<label>Price Range</label>
	</div>
	<div class="form-group">
	
			<div class="col-sm-1">
				<h5>Minimum</h5>
			</div>
			<div class="col-sm-2">
				<input type="text" name="p-min" class="form-control" value="<?php echo $search_min_price;?>"/>
			</div>
			
			<div class="col-sm-1">
				<h5>Maximum</h5>
			</div>
			<div class="col-sm-2">
				<input type="text" name="p-max" class="form-control" value="<?php echo $search_max_price;?>"/>
			</div>
		
			<label class="col-sm-2">Order By</label>
			<div class="col-sm-4">
				<select class="form-control" name="o" id="search-order">
					<option value="1" selected>Product Name</option>
					<option value="2">Highest Price</option>
					<option value="3">Lowest Price</option>
				</select>
			</div>
	</div>
	
	<div class="form-group">
		<div class="pull-right">
			<div class="col-sm-12">
				<input type="submit" class="btn btn-lg btn-primary " value="Search"/>
			</div>
		</div>
	</div>
</form>
</div>

<script>
	$('#search-manufacturer').val('<?php echo $search_manufacturer ?>');
	$('#search-category').val('<?php echo $search_category ?>');
	$('#search-order').val('<?php echo $search_order ?>');
</script>

