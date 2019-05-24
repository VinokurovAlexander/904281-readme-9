DROP DATABASE IF EXISTS readme_db;

CREATE DATABASE readme_db CHARACTER SET utf8 COLLATE utf8_general_ci;

USE readme_db;

CREATE TABLE content_type (
    content_type_id INT AUTO_INCREMENT PRIMARY KEY,
    content_type CHAR(128),
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
    user_name CHAR(64),
    password CHAR(64),
    avatar_path CHAR(128),
    contacts CHAR(128)
);

CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    pub_date DATETIME,
    title CHAR(64),
    text TEXT,
    user_id INT,
    quote_author CHAR(64),
    img CHAR(128),
    video CHAR(128),
    link CHAR(128),
    view_count INT,
    content_type_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (content_type_id) REFERENCES content_type(content_type_id)

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
    user_id INT,
    post_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (post_id) REFERENCES posts(post_id)
);

CREATE TABLE likes (
    like_id INT AUTO_INCREMENT PRIMARY KEY,
    who_like_id INT,
    post_id INT,
    dt_add DATETIME,
    FOREIGN KEY (who_like_id) REFERENCES users(user_id),
    FOREIGN KEY (post_id) REFERENCES posts(post_id)
);

CREATE TABLE follow (
    who_sub_id INT,
    to_sub_id INT,
    CONSTRAINT follow_users PRIMARY KEY (who_sub_id,to_sub_id),
    FOREIGN KEY (who_sub_id) REFERENCES users(user_id),
    FOREIGN KEY (to_sub_id) REFERENCES users(user_id)
);

CREATE TABLE messages (
    pub_date DATETIME,
    content TEXT,
    mes_sender_id INT,
    mes_res_id INT,
    CONSTRAINT messages_users PRIMARY KEY (mes_sender_id,mes_res_id),
    FOREIGN KEY (mes_sender_id) REFERENCES users(user_id),
    FOREIGN KEY (mes_res_id) REFERENCES users(user_id)
);

CREATE TABLE rf_rus (
    rf_rus_id INT AUTO_INCREMENT PRIMARY KEY,
    field_name_rus CHAR(64)
);


CREATE TABLE required_fields (
    rf_id INT AUTO_INCREMENT PRIMARY KEY,
    field_name CHAR(64),
    content_type_id INT,
    fd_rus_id INT,
    FOREIGN KEY (content_type_id) REFERENCES content_type(content_type_id),
    FOREIGN KEY (fd_rus_id) REFERENCES rf_rus(rf_rus_id)
);

CREATE UNIQUE INDEX email ON users(email);
CREATE UNIQUE INDEX content_type ON content_type(content_type);
CREATE UNIQUE INDEX hashtag ON hashtags(name);
