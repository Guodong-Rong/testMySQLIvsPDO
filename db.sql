-- Check if the database exists, if not create it
CREATE DATABASE IF NOT EXISTS mydatabase;

-- Use the database
USE mydatabase;

-- Check if the product table exists, if not create it
CREATE TABLE IF NOT EXISTS product (
    ID INT(11) NOT NULL,
    Name VARCHAR(50) NOT NULL,
    Price DECIMAL(6,2) NOT NULL,
    PRIMARY KEY (ID)
);
