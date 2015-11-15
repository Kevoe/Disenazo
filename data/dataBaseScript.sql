CREATE DATABASE Disenazo;
USE Disenazo;

CREATE TABLE User (
	fName VARCHAR(50) NOT NULL,
    lName VARCHAR(50) NOT NULL,
    userName VARCHAR(50) NOT NULL PRIMARY KEY,
    email VARCHAR(50) NOT NULL,
    passwrd VARCHAR(250) NOT NULL,
    avatar VARCHAR(50),
    city VARCHAR(50),
    address VARCHAR(200),
    aboutMe VARCHAR(400)
);

CREATE TABLE Design(
	designId VARCHAR(50) NOT NULL PRIMARY KEY,
    userName VARCHAR(50) NOT NULL,
	designName VARCHAR(50) NOT NULL,
	uploadDate DATETIME NOT NULL DEFAULT NOW(),
	description VARCHAR(250),
    price DECIMAL NOT NULL,
    views INT,
    rate TINYINT,
    FOREIGN KEY (userName)
		REFERENCES User (userName)
		ON DELETE CASCADE
);

CREATE TABLE Product(
	productName VARCHAR(30) NOT NULL PRIMARY KEY,
	type VARCHAR(30),
    price DECIMAL NOT NULL,
	divider INT NOT NULL
);

CREATE TABLE Comment(
	commentId VARCHAR(30) NOT NULL PRIMARY KEY,
	userName VARCHAR(50),
    design VARCHAR(50) NOT NULL,
    FOREIGN KEY (design)
		REFERENCES Design (designId)
		ON DELETE CASCADE,
    FOREIGN KEY (userName)
    	REFERENCES User (userName)
    	ON DELETE CASCADE
);

CREATE TABLE Rate(
	rateId VARCHAR(30) NOT NULL PRIMARY KEY,
	rate TINYINT,
    userName VARCHAR(50),
    design VARCHAR(50) NOT NULL,
    FOREIGN KEY (design)
		REFERENCES Design (designId)
		ON DELETE CASCADE,
    FOREIGN KEY (userName)
    	REFERENCES User (userName)
    	ON DELETE CASCADE
);

CREATE TABLE Cart(
	orderId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userName VARCHAR(50) NOT NULL,
	design VARCHAR(50) NOT NULL,
    product VARCHAR(30) NOT NULL,
    color VARCHAR(20) NOT NULL,
    size VARCHAR(20) NOT NULL,
    unitPrice INT NOT NULL,
	quantity INT NOT NULL,
    totalPrice INT NOT NULL,
	status CHAR(1) NOT NULL,
	FOREIGN KEY (userName)
		REFERENCES User (userName)
		ON DELETE CASCADE,
	FOREIGN KEY (design)
		REFERENCES Design (designId)
		ON DELETE CASCADE,
    FOREIGN KEY (product)
    	REFERENCES Product (productName)
    	ON DELETE CASCADE
);

--ALTER TABLE Design add uploadDate DATETIME DEFAULT NOW() AFTER designName;

--Static products
--Divider is to balance the price of design per product
INSERT INTO Product (productName, type, price, divider)
VALUES 	('t-shirt', 'clothing', 200, 1),
		('v-neck', 'clothing', 200, 1),
		('long sleeve', 'clothing', 230, 1),
		('hoodie', 'clothing', 300, 1),
		('poster', 'wall-art', 100, 1),
		('canvas print', 'wall-art', 500, 1),
		('art print', 'wall-art', 120, 1),
		('framed print', 'wall-art', 1000, 1),
		('metal print', 'wall-art', 450, 1),
		('iphone case', 'cases', 200, 2),
		('samsung case', 'cases', 200, 2),
		('ipad case', 'cases', 400, 2),
		('laptop skin', 'cases', 200, 2),
		('sticker', 'stationery', 15, 10),
		('notebook', 'stationery', 70, 3),
		('hardcover journal', 'stationery', 150, 2),
		('cup', 'home', 100, 2),
		('thermo', 'home', 200, 2),
		('popcorn bowl', 'home', 150, 2);

--Estos son solo para probar la base de datos
INSERT INTO User (fName, lName, userName, email, passwrd)
VALUES 	('Matt', 'Murdock', 'Daredevil', 'dare@devil', 'daredevil');

INSERT INTO Design (designId, userName, designName, description, price)
VALUES 	('alien_j3s94hjdolp8x', 'Daredevil', 'alienigena', 'Un diseño espacial de un alien', 70);
