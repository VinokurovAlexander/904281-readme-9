<?php

require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

if (!isset($_GET['content'])) {
    $_GET['content'] = 'posts';
}


$current_user_id = $_GET['user_id'];
$user_post_count = get_user_posts_count($con,$current_user_id);
$user_followers_count = get_user_followers($con,$current_user_id);

//Получаем информацию о пользователе, указанном в GET запросе
$user_sql = "SELECT * FROM users u WHERE u.user_id = $current_user_id";
$user_res = mysqli_query($con, $user_sql);
$user = mysqli_fetch_array($user_res, MYSQLI_ASSOC);

//Получаем список постов пользователя
$posts_sql = "SELECT p.*,ct.icon_class,COUNT(l.like_id) AS likes_count FROM posts p
    JOIN content_type ct ON p.content_type_id = ct.content_type_id
    LEFT JOIN likes l ON p.post_id = l.post_id
    WHERE p.user_id = $current_user_id
    GROUP BY p.post_id
    ORDER BY pub_date DESC";
$posts_res = mysqli_query($con, $posts_sql);
$posts = mysqli_fetch_all($posts_res, MYSQLI_ASSOC);


//Получаем массив с необходимой информацией для отображения лайков пользователя
$likes_sql = "SELECT
                    l.*,
                    ct.icon_class,
                    p.img,p.video,p.content_type_id,
                    u2.user_name as who_like_name, u2.avatar_path as who_like_avatar_path
                FROM likes l
                    JOIN posts p ON l.post_id = p.post_id
                    JOIN users u ON p.user_id = u.user_id
                    JOIN users u2 ON l.who_like_id = u2.user_id
                    JOIN content_type ct ON p.content_type_id = ct.content_type_id
                WHERE u.user_id = $current_user_id
                ORDER BY dt_add DESC";
$likes_res = mysqli_query($con, $likes_sql);
$likes = mysqli_fetch_all($likes_res, MYSQLI_ASSOC);

$page_content = include_template('profile_template.php',[
    'user_post_count' => $user_post_count,
    'user_followers_count' => $user_followers_count,
    'user' => $user,
    'posts' => $posts,
    'likes' => $likes,
    'con' => $con
]);

$layout_content = include_template('layout.php',[
    'content' => $page_content ,
    'title' => 'Мой профиль',


]);

print($layout_content);




