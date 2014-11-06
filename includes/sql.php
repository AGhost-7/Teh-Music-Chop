<?php

$con = new mysqli('localhost', 'root', '', 'music_shop');

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}



/**
 * SQL Entities
 */


class user {
	private $user_name;
	private $user_id;
	private $is_admin;
	
	public function __construct($user_name, $user_id,$is_admin) {
		$this->user_name = $user_name;
		$this->user_id = $user_id;
		$this->is_admin = $is_admin;
	}
	
	public function get_user_id() {
		return $this->user_id;
	}
	
	public function get_user_name() {
		return $this->user_name;
	}
	
	public function get_is_admin(){
		return $this->is_admin;
	}
	
	public static function login($user_name, $password, $is_cookie) {
		global $con;
		$prep = $con->prepare("
			SELECT user_id, user_is_admin FROM `users`
			WHERE user_name = ? AND user_password = ?
		");
		
		$prep->bind_param("ss", $user_name, $password);
		$prep->execute();
		$prep->bind_result($user_id, $is_admin);
		
		
		
		if($prep->fetch()) {
			$_SESSION['user-name'] = $user_name;
			$prep->close();
			return new User($user_name, $user_id, $is_admin);
		} else {
			error_log("nope...");
			$prep->close();
			return false;
		}
		
	}
	
	public static function from_session() {
		if(isset($_SESSION['user-name'])) {
			global $con;
			$user_name = $_SESSION['user-name'];
			$prep = $con->prepare("
				SELECT user_id, user_is_admin FROM `users` 
				WHERE user_name = ?
			");
			$prep->bind_param("s", $user_name);
			$prep->execute();
			$prep->bind_result($user_id, $is_admin);
			if($prep->fetch()) {
				$prep->close();
				return new user($user_name, $user_id, $is_admin);
			} else {
				$prep->close();
				return false;
			}
		} else {
			return false;
		}
	}
	
	public static function insert_new($user_name, $password) {
		global $con;
		$prep = $con->prepare("INSERT INTO `users`(user_name,user_password) VALUES (?, ?)");
		$prep->bind_param("ss", $user_name, $password);
		$prep->execute();
		
		// Already exists
		if($prep->errno == 1062){
			$prep->close();
			return false;
		} else {
			$prep->close();
			return true;
		}
	}
	
	public function purchases() {
		global $con;
		return $con->query("
			SELECT * FROM purchases
			JOIN `products` 
				ON products.product_id = purchases.purchase_product 
			WHERE purchase_user = $this->user_id
				AND purchase_is_payed = FALSE;
		");
	}
}

/**
 * Returns the result set based on function and HTTP GET arguments
 */

function products_from_request($page, $page_count){
	global $con;
	
	$from = ($page - 1) * 20;
	
	$ord_using = order_using();
	
	if(isset($_GET['o-o']) && $_GET['o-o'] == 'DESC'){
		$ord_in = 'DESC';
	} else {
		$ord_in = 'ASC';
	}
	
	// If this is a page search, we need to
	// use a prepared statement.
	$field = page_query();
	if($field) {
		$query = "
			SELECT *
			FROM `products`
			INNER JOIN `categories`
				ON `categories`.category_id = `products`.product_category
			INNER JOIN manufacturers
				ON `manufacturers`.manufacturer_id = `products`.product_manufacturer
			WHERE `$field` LIKE ?
			ORDER BY $ord_with $ord_in
			LIMIT $from, 20
		";
		
		$prep = $con->prepare(query);
		$prep->bind_param("s", "%" . $_GET['s'] . "%");
		$result = $prep->get_result();
		$prep->close();
		
		return $result;
	} else {
		return $con->query("
				SELECT *
				FROM `products`
				INNER JOIN `categories`
					ON `categories`.category_id = `products`.product_category
				INNER JOIN manufacturers
					ON `manufacturers`.manufacturer_id = `products`.product_manufacturer
				ORDER BY $ord_using $ord_in
				LIMIT $from, 20
			");
	}
}

function order_using() {
	$result = 'product_name';
	
	switch($_GET['o-n']){
		case 'category':
			$result = 'category_name';
			break;
			
		case 'name':
			$result = 'product_name';
			break;
		case 'price':
			$result = 'product_price';
			break;
		case 'manufacturer':
			$result = 'manufacturer_name';
			break;
	}
	
	return $result;
}

function page_query() {
	if(isset($_GET['s'])) {
		$field;
		switch($_GET['s-f']){
			case 'name':
				$field = 'product_name';
				break;
			case 'manufacturer':
				$field = 'manufacturer_name';
				break;
			case 'category';
				$field = 'category_name';
			default:
				$field = 'product_name'; 
		}
		return $field;
	}
	return false;
}

session_start();

$user = user::from_session();

?>