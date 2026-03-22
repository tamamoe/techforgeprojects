-- database setup
CREATE DATABASE IF NOT EXISTS cs2team61_db;
USE cs2team61_db;

CREATE TABLE users (
    userid INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    isadmin TINYINT(1) DEFAULT 0,
    communicationpreference TINYINT(1) DEFAULT 1,
    darkmode TINYINT(1) DEFAULT 1,
    createdat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    categoryid INT PRIMARY KEY AUTO_INCREMENT,
    categoryname VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    createdat TIMESTAMP CURRENT_TIMESTAMP
);

CREATE TABLE products (
    productid INT PRIMARY KEY AUTO_INCREMENT,
    productname VARCHAR(200) NOT NULL,
    categoryid INT NOT NULL,
    stock INT DEFAULT 0,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 0.0,
    imageurl VARCHAR(255),
    createdat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoryid) REFERENCES categories(categoryid)
);

CREATE TABLE orders (
    orderid INT PRIMARY KEY AUTO_INCREMENT,
    userid INT NOT NULL,
    totalamount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    shippingaddress VARCHAR(500),
    orderdate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES users(userid)
);

CREATE TABLE cart (
    cartid INT PRIMARY KEY AUTO_INCREMENT,
    userid INT NOT NULL,
    productid INT NOT NULL,
    quantity INT DEFAULT 1,
    addedat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userid) REFERENCES users(userid) ON DELETE CASCADE,
    FOREIGN KEY (productid) REFERENCES products(productid) ON DELETE CASCADE
);

CREATE TABLE payments (
    paymentid INT PRIMARY KEY AUTO_INCREMENT,
    orderid INT NOT NULL,
    paymentmethod VARCHAR(50) NOT NULL,
    paymentstatus VARCHAR(20) DEFAULT 'pending',
    amount DECIMAL(10,2) NOT NULL,
    paymentdate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (orderid) REFERENCES orders(orderid)
);

CREATE TABLE contacts (
    contactid INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'unread',
    submittedat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
