CREATE DATABASE things_fine
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;
USE things_fine;

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY, 
    date_reg TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Редакция 15.02.2019 11:33
    email CHAR(255) NOT NULL,
    name CHAR(128),
    pwd VARCHAR(255)  NOT NULL 
);
CREATE UNIQUE INDEX uindex_email ON users(email);
CREATE UNIQUE INDEX uindex_pwd ON users(pwd);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT, 
    name CHAR(128) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)  -- Внешний ключ для добавления связи с таблицей users
);
CREATE INDEX index_name ON projects(name);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT, 
    project_id INT, 
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_done DATE,
    done TINYINT DEFAULT 0,
    name CHAR(255) NOT NULL,
    file CHAR(255),
    date_planned DATE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id)	
);
CREATE INDEX index_user_id ON tasks(user_id);
CREATE INDEX index_project_id ON tasks(project_id);
CREATE INDEX index_name ON tasks(name);
