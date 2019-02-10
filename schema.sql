CREATE DATABASE things_fine;
USE things_fine;

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT, 
    name CHAR(128) NOT NULL 
);
CREATE UNIQUE INDEX uindex_name ON projects(name);
CREATE INDEX index_name ON projects(name);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT, 
    project_id INT, 
    date_creat DATE NOT NULL,
    date_done DATE,
    done TINYINT DEFAULT 0,
    name CHAR(255) NOT NULL,
    file CHAR(255),
    date_planned DATE
);
CREATE INDEX index_name ON tasks(name);

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    date_reg DATE,
    email CHAR(255) NOT NULL,
    name CHAR(128),
    pwd VARCHAR(255)  NOT NULL 
);

CREATE UNIQUE INDEX uindex_email ON users(email);

CREATE UNIQUE INDEX uindex_pwd ON users(pwd);
