/* INITIALIZATION */
DROP DATABASE IF EXISTS santa_clara_menus;
CREATE DATABASE santa_clara_menus;
USE santa_clara_menus;

/*
CREATE USER 'scmuser'@'localhost' IDENTIFIED BY 'p123456d';
GRANT ALL PRIVILEGES ON santa_clara_menus . * TO 'scmuser'@'localhost';
FLUSH PRIVILEGES;
*/

/* ENTITIES */
CREATE TABLE IF NOT EXISTS user (
	user_id	INT(3) UNSIGNED AUTO_INCREMENT,
    username VARCHAR(64) UNIQUE,
    name VARCHAR(64),
    encrypted_password VARCHAR(64) NOT NULL,
    isRestaurant BOOLEAN,
    loc INT(3) UNSIGNED,
    PRIMARY KEY (user_id)
);

CREATE TABLE IF NOT EXISTS location (
	loc_id INT(3) UNSIGNED AUTO_INCREMENT,
    latitude VARCHAR(64),
    longitude VARCHAR(64),
    address VARCHAR(64),
	PRIMARY KEY (loc_id)
);

CREATE TABLE IF NOT EXISTS transaction (
	t_id INT(3) UNSIGNED AUTO_INCREMENT,
	driver_id INT(3) UNSIGNED,
    restaurant_id INT(3) UNSIGNED,
    start_loc INT(3) UNSIGNED,
    end_loc INT(3) UNSIGNED,
    timestamp TIMESTAMP,
    duration BIGINT,
    price DECIMAL(5,2),
    active BOOLEAN,
    PRIMARY KEY (t_id)
);


/* TODO: NEW TEST DATA */
