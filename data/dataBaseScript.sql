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
    price DECIMAL NOT NULL
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
