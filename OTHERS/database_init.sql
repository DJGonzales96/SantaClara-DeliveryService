/* INITIALIZATION */
DROP DATABASE IF EXISTS santa_clara_menus;
CREATE DATABASE santa_clara_menus;
USE santa_clara_menus;

/* DCL*/
CREATE USER 'scmuser'@'localhost' IDENTIFIED BY 'p123456d';
GRANT ALL PRIVILEGES ON santa_clara_menus . * TO 'scmuser'@'localhost';
FLUSH PRIVILEGES;

/* ENTITIES */
CREATE TABLE IF NOT EXISTS user (
	user_id	INT(10) UNSIGNED AUTO_INCREMENT,
    username VARCHAR(64) UNIQUE,
    name VARCHAR(64),
    encrypted_password VARCHAR(64) NOT NULL,
    isRestaurant BOOLEAN,
    t_id INT(10) UNSIGNED,
    PRIMARY KEY (user_id)
);

CREATE TABLE IF NOT EXISTS location (
	loc_id INT(10) UNSIGNED AUTO_INCREMENT,
    lat FLOAT( 10, 6 ) NOT NULL,
    lon FLOAT( 10, 6 ) NOT NULL,
    address VARCHAR(80) NOT NULL,
	PRIMARY KEY (loc_id)
);

CREATE TABLE IF NOT EXISTS transaction (
	t_id INT(10) UNSIGNED AUTO_INCREMENT,
	t_type VARCHAR(30),
	primary_user_id INT(10) UNSIGNED NOT NULL,
    secondary_user_id INT(10) UNSIGNED,
    start_loc INT(10) UNSIGNED,
    end_loc INT(10) UNSIGNED,
    timestamp TIMESTAMP,
    food VARCHAR(30),
    price DECIMAL(5,2),
    duration DECIMAL,
    t_status VARCHAR(30),
    PRIMARY KEY (t_id)
);

/* POPULATE DEFAULT DATA
INSERT INTO Location(lat,lon,address) VALUES (0,0,'None');
INSERT INTO transaction(t_type,start_loc) VALUES ('loc_update',1);
 */