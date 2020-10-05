--- INITIALIZATION ---
DROP DATABASE IF EXISTS santa_clara_menus;
CREATE DATABASE santa_clara_menus;
USE santa_clara_menus;

--- ENTITIES ---
CREATE TABLE IF NOT EXISTS user (
	user_id	INT(3) UNSIGNED AUTO_INCREMENT,
    username VARCHAR(64) UNIQUE,
    name VARCHAR(64),
    encrypted_password VARCHAR(64) NOT NULL,
    isRestaurant BOOLEAN,
    PRIMARY KEY (user_id)
);

CREATE TABLE IF NOT EXISTS location (
	user_id INT(3) UNSIGNED,
    address VARCHAR(64),
	PRIMARY KEY (user_id)
);

CREATE TABLE IF NOT EXISTS transaction (
	t_id INT(3) UNSIGNED AUTO_INCREMENT,
	driver_id INT(3) UNSIGNED,
    restaurant_id INT(3) UNSIGNED,
    start_loc VARCHAR(64),
    end_loc VARCHAR(64),
    timestamp TIMESTAMP,
    duration BIGINT, 
    price DECIMAL(5,2),
    PRIMARY KEY (t_id)
);

--- DATA ---
INSERT INTO user (username, name, encrypted_password, isRestaurant) VALUES
	('testDriver1', 'driver1Name', '$1$O3JMY.Tw$AdLnLjQ/5jXF9.MTp3gHv/', FALSE),
    ('testDriver2', 'driver2Name', '$1$O3JMY.Tw$AdLnLjQ/5jXF9.MTp3gHv/', FALSE),
    ('testRest1', 'rest1Name', '$1$O3JMY.Tw$AdLnLjQ/5jXF9.MTp3gHv/', TRUE),
	('testRest2', 'rest2Name', '$1$O3JMY.Tw$AdLnLjQ/5jXF9.MTp3gHv/', TRUE);

INSERT INTO location (user_id, address) VALUES
	(001, '1234 Test Start Address Rd'),
    (002, '1234 Test Start Address Rd'),
    (003, '1234 Test Restaurant Address Rd'),
    (004, '1234 Test Restaurant Address Rd');

INSERT INTO transaction (driver_id, restaurant_id, start_loc, end_loc, timestamp, duration, price) VALUES
	(001, 003, '1234 Test Restaurant Address Rd', '4321 Customer Addr Rd', '1970-01-01 00:00:01', 3600, 24.99);