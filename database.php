-- database setup
CREATE DATABASE IF NOT EXISTS techforge_db;
USE techforge_db;

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
-- personal timestamp and comments: eh easy to get with but unsure if i need more stuff into the users,
-- so i'm leaving this comment here as a reminder and placeholder just in case because 25/11/25

CREATE TABLE categories (
    categoryid INT PRIMARY KEY AUTO_INCREMENT,
    categoryname VARCHAR(100) NOT NULL
);

-- we did another oopsie so heres part of the foreignkey table because lol 

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
-- same day.