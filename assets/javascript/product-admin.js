
/**
 * "Main" for the product administration page.
 */

(function() {

var 
	$etr = $('#product-editor'),
	$nameEditor = $('#name-editor'),
	$manufacturerEditor = $('#manufacturer-editor'),
	$categoryEditor = $('#category-editor'),
	$priceEditor = $('#price-editor'),
	$quantityEditor = $('#quantity-editor'),
	$imageEditor = $('#image-editor'),
	$imageDisplay = $('#image-display'),
	$imageButton = $('#image-button'),
	$editorTitle = $etr.find('h4'),
	$idSpan = $('#product-id'),
	$tableBody = $('#table-body'),
	$lastTr = undefined,
	editorMode = 'mod';

/**
 * Non-modal logic
 */

$('#add-btn').click(function(){
	// reset them all to blanks
	$nameEditor.val("");
	$manufacturerEditor[0].selectedIndex = 0;
	$categoryEditor[0].selectedIndex = 0;
	$priceEditor.val("");
	$quantityEditor.val("");
	$idSpan.text("none");
	$imageDisplay.attr('src','');
	
	// set the title
	$editorTitle.html('Add &nbsp;');
	
	// set the mode to add
	editorMode = 'add';
	
	$etr.modal('show');
});

$('.mod-product').click(function(){
	var 
		$t = $(this),
		$tr = $t.parent().parent().parent(),
		$ch = $tr.children(),
		name = $ch.eq(1).text(),
		manufacturer = $ch.eq(2).attr('data-manufacturer-id'),
		category = $ch.eq(3).attr('data-category-id'),
		price = $ch.eq(4).text(),
		quantity = $ch.eq(5).text(),
		img = $ch.eq(0).find('img').attr('src'),
		id = $tr.attr('data-product-id');
	
	$lastTr = $tr;
	
	$editorTitle.html('Edit &nbsp;<small>' + name + '</small>');
	$nameEditor.val(name);
	$manufacturerEditor.val(manufacturer);
	$categoryEditor.val(category);
	$priceEditor.val(price);
	$quantityEditor.val(quantity);
	$imageDisplay.attr('src', img);
	$idSpan.text(id);
	
	// Set the mode to mod for the logic split
	// of the save button, etc.
	editorMode = 'mod';
	
	$etr.modal('show');
});

$('.del-product').click(function(){
	var 
		$t = $(this),
		$tr = $t.parent().parent().parent(),
		id = $tr.attr('data-product-id');
		
	$.ajax({
		url: "ajax/del-product.php",
		type: "POST",
		data: {
			"product-id": id
		},
		success: function(data){
			
			if(data.error){
				alert(data.error);
			} else {
				// success! we should remove the element now...
				$tr.remove();
			}
			
		},
		error: function(){
			alert("Error connecting to server");
		}
	})
	
});


/**
 * Editor/modal dialogue logic
 */

$('#save-btn').click(function(){
	var 
		name = $nameEditor.val(),
		quantity = $quantityEditor.val(),
		price = $priceEditor.val(),
		manufacturer = $manufacturerEditor.val(),
		category = $categoryEditor.val();
		
	if(isNaN(quantity) || isNaN(price) 
			|| quantity < 0 || price < 0.01
			|| name === "")
		alert('Invalid input.');
	else if(editorMode === 'mod'){
		var 
			$tr = $lastTr,
			$ch = $tr.children();
		
		//if($imageEditor[0].files[0]) alert("custom file!");
		
		//var formData = new FormData();
		$.ajax({
			url: "ajax/mod-product.php",
			type: 'POST',
			data: {
				"product-id": $idSpan.text(),
				"product-quantity": quantity,
				"product-name": name,
				"product-category": category,
				"product-manufacturer": manufacturer,
				"product-price": price
			},
			success: function(data){
				if(data.error) {
					alert(data.error);
				} else {
					dataToRow($ch,data.vars);
					$etr.modal('hide');
				}
			},
			error: function(){
				alert('Error connecting to server');
			}
		});
	} else if(editorMode === 'add') {
		$.ajax({
			url: 'ajax/add-product.php',
			type:'POST',
			data: {
				"product-id": $idSpan.text(),
				"product-quantity": quantity,
				"product-name": name,
				"product-category": category,
				"product-manufacturer": manufacturer,
				"product-price": price
			},
			success:function(data){
				if(data.error){
					alert(data.error);
				} else {
					var $clone = $tableBody
						.find('tr:first-child')
						.clone(true);//clone with events
					
					
					
					$tableBody.prepend($clone[0]);
					
					dataToRow($clone.children(), data.vars);
					
					//alert($clone);
					
					console.log('The element is: ', $clone[0]);
					
					$etr.modal('hide');
				}
			},
			error: function(){
				alert('Error connecting to server');
			}
		});
	}
});

// Sends data taken from the server's response 
// and editor to the row's children
function dataToRow($trChildren, vars){
	$trChildren.eq(1).text(vars['product-name']);
	$trChildren.eq(2).text($manufacturerEditor.find('option:selected').text());
	$trChildren.eq(2).attr('data-manufacturer-id', vars['product-manufacturer']);
	$trChildren.eq(3).text($categoryEditor.find('option:selected').text());
	$trChildren.eq(3).attr('data-category-id', vars['product-category']);
	$trChildren.eq(4).text(vars['product-price']);
	$trChildren.eq(5).text(vars['product-quantity']);
}

// make it so that the title changes as you write
// in the input for the name.
$nameEditor.on('input',function(ev){
	var pre = editorMode === 'mod' ? 'Edit' : 'Add';
	$editorTitle.html(pre + ' &nbsp;<small>' + $nameEditor.val() + '</small>');
});

// Click on the file input if the button replacing it is clicked.
// I'm using a regular button instead of input so that it can be styled.
$imageButton.click(function(){
	$imageEditor.click()
});

$imageEditor.change(function(ev){
	
	console.log('changed:', ev);
	
	
	
});

})();