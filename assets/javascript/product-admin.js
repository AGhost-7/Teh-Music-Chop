
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
		category = $categoryEditor.val(),
		img = $imageDisplay.attr('src');
	
	if(isNaN(quantity) || isNaN(price) 
			|| quantity < 0 || price < 0.01
			|| name === "" || img === ""){
		alert('Invalid input.');
	} else if(editorMode === 'mod'){
		var 
			$tr = $lastTr,
			$ch = $tr.children();
		
		$.ajax({
			url: "ajax/mod-product.php",
			type: 'POST',
			data: {
				"product-id": $idSpan.text(),
				"product-quantity": quantity,
				"product-name": name,
				"product-category": category,
				"product-manufacturer": manufacturer,
				"product-price": price,
				"product-img": img
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
				"product-price": price,
				"product-img": img
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
function dataToRow($ch, vars){
	console.log(vars);
	var $img = $ch.eq(0).find('img');
	if($img.attr('src') !== vars['product-img']){
		$img.attr('src', vars['product-img'])
	}
	
	$ch.eq(1).text(vars['product-name']);
	$ch.eq(2).text($manufacturerEditor.find('option:selected').text());
	$ch.eq(2).attr('data-manufacturer-id', vars['product-manufacturer']);
	$ch.eq(3).text($categoryEditor.find('option:selected').text());
	$ch.eq(3).attr('data-category-id', vars['product-category']);
	$ch.eq(4).text(vars['product-price']);
	$ch.eq(5).text(vars['product-quantity']);
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
	console.log('image changed.');
	var form = new FormData(),
		file = ev.target.files[0];
	form.append("image", file);
	
	$.ajax({
		url:"ajax/upload-image.php",
		type:'POST',
		data: form,
		success: function(data){
			if(data.error){
				alert(data.error);
				// since this is an on-change event, might 
				// be a good idea to clear...
				ev.target.value = "";
			} else {
				$imageDisplay.attr('src', data.result)
			}
		},
		error: function(x,err,msg){
			alert("Error connecting to server.")
		},
		// jQuery is going to scream, so turn off
		// processing and type checking shenanigans.
		contentType: false,
		processData: false
	});
	
});

simpleEditor('manufacturer');
simpleEditor('category');

// wOOoOo scary... ~(^0^)~
function simpleEditor(name) {
	var 
		$root = $('#' + name + '-root'),
		$sel = $root.find('select');

	
	// Add
	$root.find('.btn-success').popover({
		html: true,
		content: popoverHtml('Add'),
		container:'.modal-body',
		placement: 'top'
	}).on('shown.bs.popover', function(){
		var 
			$t = $(this),
			$pop = $('#popover-add'),
			$btn = $pop.find('button');
		
		$btn.click(function(){
			var inName = $pop.find('input').val();
			if(inName === '') {
				alert('You should give some info...');
			} else {
				var args = {};
				args[name + '-name'] = inName;
				$.ajax({
					url: "ajax/add-" + name + ".php",
					data: args,
					type: "POST",
					success: function(data){
						if(data.error) {
							alert(data.error)
						} else {
							var opt = '<option value="' + data.id + '">' + 
								data.vars[name + '-name'] + '</option>';
							
							$sel.prepend(opt);
							
							// also got to add it to the search bar.
							$('#search-' + name + ' > option:first-child').after(opt);
							
							$t.popover('hide');
						}
					},
					error: function(){
						alert("Error connecting to server.")
					}
				});
			}
		});
	
	});
	
	// Modify: this is the hard one...
	$root.find('.btn-warning').popover({
		html: true,
		content: withState(function($selected, id, inName){
			return popoverHtml('Modify', inName)
		}),
		container: '.modal-body',
		placement: 'top'
	}).on('shown.bs.popover', function(){
		var 
			$t = $(this),
			$pop = $('#popover-modify'),
			$btn = $pop.find('button');
		
		$btn.click(withState(function($selected,id,inName){

			var inName = $pop.find('input').val();
			
			if(inName === ""){
				alert("Name cannot be empty.")
			} else {
				var 
					id = $sel.val(),
					args = {};
					
				// Generate the arguments object
				args[name + "-id"] = id;
				args[name + "-name"] = inName;
				
				$.ajax({
					url: "ajax/mod-" + name + ".php",
					type: "POST",
					data: args,
					success: function(data){
						
						// The server will escape the html characters
						// and so on, so to have something accurate...
						inName = data.vars[name + "-name"];
						id = data.vars[name + "-id"];
						
						// set the option to the user's value
						$sel
							.find('[value="' + id + '"]')
							.text(inName);
							
						// change all fields that we've got outside of the modal
						// to what was modfied.
						$('tbody')
							.find('td[data-' + name + '-id="' + id + '"]')
							.text(inName);
						
						// Gotta update the search bar
						$('#search-' + name)
							.find('[value="' + id + '"]')
							.text(inName);
						
						// since it won't hide on its own...
						$t.popover('hide');
					},
					error: function(){
						alert("Error connecting to server")
					}
				})
			}
		}));
	});
	
	// Remove
	
	function deleteButton($selected, id) {
		args = {};
		args[name + '-id'] = id;
		
		$.ajax({
			url: "ajax/del-" + name + '.php',
			data: args,
			type: "POST",
			success: function(data){
				if(data.error){
					alert(data.error);
				} else {
					$selected.remove();
					// remove from search bar as well..
					$('#search-' + name + ' > option[value="' + data.vars[name + '-id']+ '"]').remove();
					// and reset select input to first element after removing
					$sel[0].selectedIndex = 0;
				}
			},
			error: function(xhr, status, err){
				alert(err)
			}
		})
	
	}
	
	
	withConfirmation.call(
		$root.find('.btn-danger')[0],
		'Confirm deletion', withState(deleteButton)
	);
	
	function withConfirmation(msg, handler){
		var $t = $(this), 
			t = this, 
			$sub = $t.parent().parent();
		
		$t.popover({
			html: true,
			placement: 'left',
			container: $sub,
			content: function(){
				return '<strong>' + msg + '</strong> &nbsp; '
					+ '<button class="btn btn-sm btn-default" id="btn-confirm">Ok</button>'
			}
		}).on('shown.bs.popover', function(){
			
			$sub.find('#btn-confirm').click(function(){
				handler.apply(t, arguments);
				$t.popover('hide');
			});
			
		}).on('focusout', function(){
			alert("focus lost");
			$(this).popover('hide')
		}).blur(function(){
			alert("focus lost")
		});
		
	}
	
	// Functional inheritance :D... Used for functions
	// that need info on the state of the select block.
	function withState(handler){
		return function(){
			var $selected = $sel.find('option:selected'),
				id = $selected.val(),
				name = $selected.text();
	
			return handler($selected,id,name);
		};
	}
	
	function popoverHtml(title, data){
		data = data || "";
		var id = 'popover-' + title.toLowerCase();
		var html = 
			'<div class="input-group" id="' + id + '">' +
				'<input type="text" class="form-control" value="' + data + '"width=1>' +
				'<span class="input-group-btn">' +
					'<button class="btn btn-default" type="button">' + title + '</button>' +
				'</span>' +
			'</div>';
		return html;
	}
}




})();







