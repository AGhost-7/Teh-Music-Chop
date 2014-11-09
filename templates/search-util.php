<?php
	
	// so what is going to be persisted through to the form?
	$search_name = isset($_GET['n']) ? $_GET['n'] : "";
	$search_category = isset($_GET['c']) ? $_GET['c'] : "0";
	$search_manufacturer = isset($_GET['m']) ? $_GET['m'] : "0";
	$search_min_price = isset($_GET['p-min']) ? $_GET['p-min'] : "";
	$search_max_price = isset($_GET['p-max']) ? $_GET['p-max'] : "";

?>


<form class="form-horizontal jumbotron" action="<?php echo $self_url; ?>" method="GET">
	<div class="form-group">
		<label>Name</label>
		<input type="text" name="n" class="form-control" value="<?php echo $search_name;?>"/>
	</div>
	
	<div class="form-group">
		<label>Category</label>
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
	
	<div class="form-group">
		<label>Manufacturer</label>
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
	
	<div class="form-group">
		<label>Price Range</label>
		
		<br/>
		
		<div class="row">
			<div class="col-md-2">
				<h5>Minimum</h5>
			</div>
			<div class="col-md-4">
				<input type="text" name="p-min" class="form-control" value="<?php echo $search_min_price;?>"/>
			</div>
			
			<div class="col-md-2">
				<h5>Maximum</h5>
			</div>
			<div class="col-md-4">
				<input type="text" name="p-max" class="form-control" value="<?php echo $search_max_price;?>"/>
			</div>
		</div>
		
	</div>
	
	<div class="form-group">
		<input type="submit" class="btn btn-default btn-lg" value="Search"/>
	</div>
</form>
<script>
	$('#search-manufacturer').val('<?php echo $search_manufacturer ?>');
	$('#search-category').val('<?php echo $search_category ?>');
</script>

