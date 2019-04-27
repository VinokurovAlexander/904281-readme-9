DROP DATABASE IF EXISTS readme_db;

CREATE DATABASE readme_db CHARACTER SET utf8 COLLATE utf8_general_ci;

USE readme_db;

CREATE TABLE content_type (
    content_type_id INT AUTO_INCREMENT PRIMARY KEY,
    name CHAR(128),
    icon_class CHAR(128)
);

CREATE TABLE hashtags (
    hashtag_id INT AUTO_INCREMENT PRIMARY KEY,
    name CHAR(128)
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    reg_date DATETIME,
    email CHAR(128),
    name CHAR(64),
    password CHAR(64),
    avatar_path CHAR(128),
    contacts CHAR(128)
);

CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    pub_date DATETIME,
    title CHAR(64),
    text TEXT,
    author CHAR(128),
    img CHAR(128),
    video CHAR(128),
    link CHAR(128),
    view_count INT,
    FOREIGN KEY (post_id) REFERENCES users(user_id),
    FOREIGN KEY (post_id) REFERENCES content_type(content_type_id)
);

CREATE TABLE posts_hashtags (
    post_id INT,
    hashtag_id INT,
    CONSTRAINT posts_hashtag PRIMARY KEY (post_id,hashtag_id)
);

CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    pub_date DATETIME,
    content TEXT,
    FOREIGN KEY (comment_id) REFERENCES users(user_id),
    FOREIGN KEY (comment_id) REFERENCES posts(post_id)
);

CREATE TABLE likes (
    like_id INT AUTO_INCREMENT PRIMARY KEY,
    FOREIGN KEY (like_id) REFERENCES users(user_id),
    FOREIGN KEY (like_id) REFERENCES posts(post_id)
);

CREATE TABLE follow (
    who_sub_id INT,
    to_sub_id INT,
    CONSTRAINT follow_users PRIMARY KEY (who_sub_id,to_sub_id)
);

CREATE TABLE messages (
    pub_date DATETIME,
    content TEXT,
    mes_sender_id INT,
    mes_res_id INT,
    CONSTRAINT messages_users PRIMARY KEY (mes_sender_id,mes_res_id)
);

CREATE TABLE users_roles(
    roles_id INT AUTO_INCREMENT PRIMARY KEY,
    roles_name char(64)
);

CREATE UNIQUE INDEX email ON users(email);
CREATE UNIQUE INDEX user_name ON users(name);
CREATE UNIQUE INDEX roles ON users_roles(roles_name);
CREATE UNIQUE INDEX content_type ON content_type(name);
CREATE UNIQUE INDEX icon_class ON content_type(icon_class);
CREATE UNIQUE INDEX hashtag ON hashtags(name);
