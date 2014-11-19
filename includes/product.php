<?php
class product {
	
	public $name;
	public $price;
	public $quantity;
	public $manufacturer;
	public $id;
	public $category;
	public $img;
	public $error = false;
	
	public function __construct() {}
	
	public static function from_post(){
		$product = new product();
		
		if(!isset($_POST['product-quantity'])
			|| !isset($_POST['product-name'])
			|| !isset($_POST['product-price'])
			|| !isset($_POST['product-category'])
			|| !isset($_POST['product-manufacturer'])
			|| !isset($_POST['product-img'])){
			$product->error = "Missing arguments";
		}	else {
			$product->price = floatval($_POST['product-price']);
			$product->name = $_POST['product-name'];
			$product->quantity = intval($_POST['product-quantity']);
			$product->manufacturer = intval($_POST['product-manufacturer']);
			$product->category = intval($_POST['product-category']);
			$product->id = intval($_POST['product-id']);
			$product->img = htmlspecialchars($_POST['product-img']);
		}
		
		return $product;
	}
	
	public function db_update(){
	
		if(!isset($_POST['product-id'])){
			$this->error = "Missing arguments";
			return false;
		} else {
			
			global $con;
			
			$id = intval($_POST['product-id']);
			
			$prep = $con->prepare("
				UPDATE `products`
				SET product_name = ?,
					product_quantity = ?,
					product_price = ?,
					product_manufacturer = ?,
					product_category = ?,
					product_img = ?
				WHERE product_id = $id
				LIMIT 1
			");
			
			$prep->bind_param('sidiis', 
				$this->name, 
				$this->quantity, 
				$this->price, 
				$this->manufacturer, 
				$this->category, 
				$this->img);
				
			if($prep->execute()){
				$prep->close();
				return array(
					'vars' => $this->to_array()
				);
			} else {
				$this->error = $prep->error;
				$prep->close();
				return false;
			}
		}
	}
	
	public function db_insert(){
		
		global $con;
		
		$prep = $con->prepare("
			INSERT INTO `products`(
				product_name,
				product_quantity,
				product_price,
				product_manufacturer,
				product_category,
				product_img
			)
			VALUES
			(?,?,?,?,?,?)
		");
		
		$prep->bind_param('sidiis', 
				$this->name, 
				$this->quantity, 
				$this->price, 
				$this->manufacturer, 
				$this->category, 
				$this->img);
		
		if($prep->execute()){
			$result = array(
				'vars' => $this->to_array(),
				'id' => $prep->insert_id
			);
		} else {
			$result = false;
			$this->error = $prep->error;
		}
		
		$prep->close();
		return $result;
	}
	
	public function to_array(){
		return array(
				'product-price' => $this->price,
				'product-name' => $this->name,
				'product-quantity' => $this->quantity,
				'product-manufacturer' => $this->manufacturer,
				'product-category' => $this->category,
				'product-img' => $this->img,
				'error' => $this->error
			);
	}
}
?>