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
	
	/**
	 * Sets session variables and returns an instance of user
	 * if the password and user_name where correct.
	 */
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
			
			$user = new user($user_name, $user_id, $is_admin);
			
			if($is_cookie){
				$user->init_cookie();
			}
			
			return $user;
		} else {
			error_log("nope...");
			$prep->close();
			return false;
		}
	}
	
	/**
	 * Creates a new user from the session variables.
	 */
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
	
	/**
	 * Inserts a new user in the mysql table
	 */
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
	
	/**
	 * Returns true if there was a cookie and that
	 * the session variable was restored.
	 */
	public static function restore_from_cookie(){
		global $con;
		
		if(isset($_COOKIE['id'])){
			$prep = $con->prepare("
				SELECT user_name FROM `users`
				INNER JOIN `tokens`
					ON token_user = user_id
				WHERE token_val = ?
					AND token_created_on < DATE_ADD(NOW(), INTERVAL 30 DAY)
			");
			
			$prep->bind_param("s", $_COOKIE['id']);
			
			if($prep->execute()) {
				$prep->bind_result($user_name);
				
				if($prep->fetch()) {
					
					$_SESSION['user-name'] = $user_name;
					$prep->close();
					
					return true;
				} else {
					$prep->close();
					return false;
				}
			} else {
				$prep->close();
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Returns all of the purchases that the user hasn't
	 * payed yet.
	 */
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
	
	/**
	 * Initializes the cookie so that the session
	 * can be persisted even after closing the browser.
	 */
	public function init_cookie(){
		global $con;
		
		// generate token value...
		$token = hash('sha512', (rand() . microtime()));
		
		// insert into table
		if($con->query("
				INSERT INTO `tokens`(token_user,token_val)
				VALUES ($this->user_id, '$token')
				")) {
				
			// now set it
			$_COOKIE['id'] = $token;
			setcookie('id', $token, time() + (60 * 60 * 24 * 30));
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Takes care of removing all login related session variables/cookies
	 * and deletes the token from the database for logging out.
	 */
	public function logout(){
		global $con;
		
		// If the session was persisted through cookies,
		// get rid of that first.
		if(isset($_COOKIE['id'])){
			$prep = $con->prepare("
				DELETE FROM `tokens` 
				WHERE token_user = $this->user_id
					AND token_val = ?
			");
			
			$prep->bind_param("s", $_COOKIE['id']);
			$prep->execute();
			// and clear the cookies...
			unset($_COOKIE['id']);
			/*bool setcookie ( string $name [, string $value [, 
				int $expire = 0 [, string $path [, string $domain [, 
				bool $secure = false [, bool $httponly = false ]]]]]] )*/
			setcookie('id', '', time() - 3600);
		}
		
		// and now destroy the session variables...
		session_unset();
		session_destroy();
	
		
	}
	
}

/**
 * Returns the result set based on function and HTTP GET arguments
 */
function products_from_request($page, $page_count){
	global $con;
	
	$args = [];
	$arg_types = "";
	$query = "
		SELECT *
		FROM `products`
		INNER JOIN `categories`
			ON `categories`.category_id = `products`.product_category
		INNER JOIN manufacturers
			ON `manufacturers`.manufacturer_id = `products`.product_manufacturer";
	
	// Now the dynamic part of our query, filtering logic
	
	$has_w = false;
	
	// view only products of a certain category.
	if(!empty($_GET['c'])){
		$args [] = &$_GET['c'];
		$query .= " WHERE category_id = ? ";
		$arg_types .= "i";
		$has_w = true;
	}
	
	// view only products of a certain manufacturer
	if(!empty($_GET['m'])) {
		$args [] = &$_GET['m'];
		$query .= $has_w ? " AND " : " WHERE ";
		$query .= " manufacturer_id = ? ";
		$arg_types .= "i";
		$has_w = true;
	}
	
	// where the name of the product is...
	if(!empty($_GET['n'])) {
		$query_name = "%" . $_GET['n'] . "%";
		$args [] = &$query_name;
		$query .= $has_w ? " AND " : " WHERE ";
		$query .= " product_name LIKE ? ";
		$arg_types .= "s";
		$has_w = true;
	}
	
	// price range...
	if(!empty($_GET['p-min'])) {
		$args [] = &$_GET['p-min'];
		$query .= $has_w ? " AND " : " WHERE ";
		$query .= " product_price >= ? ";
		$arg_types .= "d";
		$has_w = true;
	}
	
	if(!empty($_GET['p-max']) ) {
		$args [] = &$_GET['p-max'];
		$query .= $has_w ? " AND " : " WHERE ";
		$query .= " product_price <= ? ";
		$arg_types .= "d";
		$has_w = true;
	}
	
	// ordering
	
	if(isset($_GET['o'])){
		$order = intval($_GET['o']);
	} else {
		$order = 1;
	}
	
	switch($order){
		case 1:
			$query .= " ORDER BY product_name ASC ";
			break;
		case 2:
			$query .= " ORDER BY product_price DESC ";
			break;
		case 3:
			$query .= " ORDER BY product_price ASC ";
			break;
	}
	
	
	// now we want to limit number of rows we're getting.
	$from = ($page - 1) * 20;
	$query .= " LIMIT $from, 20 ";
	
	
	// Now put the query together...
	if(count($args) > 0){
		// the input data types string is the first argument to the bind_param 
		// method, so we have to prepend it at the start of the args array
		array_unshift($args, $arg_types);
		
		$prep = $con->prepare($query);
		
		// since the bind_param is a vararg function, we need to use some
		// funky shit... i.e., we need to be able to call the function by 
		// passing an array of our arguments.
		call_user_func_array(array($prep,'bind_param'), $args);
		
		$prep->execute();
		$result = $prep->get_result();
		$prep->close();
		
	} else {
		$result = $con->query($query);
	}
	return $result;
	
}

session_start();

$user = user::from_session();

if(!$user) {
	// try to restore from cookie
	if(user::restore_from_cookie()){
		// lets try again...
		$user = user::from_session();
	}
}

?>