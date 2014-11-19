DROP DATABASE IF EXISTS `music_shop`;

CREATE DATABASE `music_shop`;

USE `music_shop`;

CREATE TABLE `users`(
	user_id INT NOT NULL AUTO_INCREMENT,
	user_name VarChar(50) NOT NULL UNIQUE,
	user_password CHAR(128) NOT NULL,
	user_is_admin BOOLEAN NOT NULL DEFAULT FALSE,
	PRIMARY KEY(user_id)
);

-- This contains the authentication cookie which is
-- used across pages to identify the user. Tokens
-- expire which means that IF session hijacking occurs
-- the time window is minimized.
CREATE TABLE `tokens`(
	token_val CHAR(128) NOT NULL UNIQUE,
	token_user INT NOT NULL REFERENCES users(user_id),
	-- Use DATETIME and not TIMESTAMP as it can cause problems...
	-- See TIMESTAMP's range for details:
	-- http://dev.mysql.com/doc/refman/5.5/en/datetime.html
	token_created_on DATETIME NOT NULL DEFAULT NOW(),
	PRIMARY KEY(token_val)
);

CREATE TABLE `categories`(
	category_id INT NOT NULL AUTO_INCREMENT,
	category_name VarChar(45) NOT NULL UNIQUE,
	PRIMARY KEY(category_id)
);

CREATE TABLE `manufacturers`(
	manufacturer_id INT NOT NULL AUTO_INCREMENT,
	manufacturer_name VarChar(45) NOT NULL UNIQUE,
	PRIMARY KEY(manufacturer_id)
);

CREATE TABLE `products`(
	product_id INT NOT NULL AUTO_INCREMENT,
	product_price FLOAT NOT NULL,
	product_name TEXT NOT NULL,
	product_quantity INT NOT NULL DEFAULT 0,
	product_img TEXT,
	product_manufacturer INT NOT NULL
		REFERENCES `manufacturers`(manufacturer_id),
	product_category INT NOT NULL 
		REFERENCES `categories`(category_id),
	PRIMARY KEY(product_id)
);

CREATE TABLE `purchases`(
	purchase_id INT NOT NULL AUTO_INCREMENT,
	purchase_is_payed BOOLEAN NOT NULL DEFAULT FALSE,
	purchase_amount INT NOT NULL DEFAULT 1,
	purchase_user INT NOT NULL 
		REFERENCES users(user_id),
	purchase_product INT NOT NULL 
		REFERENCES products(product_id),
	PRIMARY KEY(purchase_id)
);