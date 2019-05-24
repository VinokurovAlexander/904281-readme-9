<?php
require_once('helpers.php');
require_once('my_functions.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

// Работаем с БД
$con = mysqli_connect("localhost", "root", "", "readme_db");
mysqli_set_charset($con, "utf8");

if ($con == false) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
}

if (isset($_GET['post_id'])) {
    //Получаем контент поста из запроса GET
    $get_post_id = intval($_GET['post_id']);
    $post = "SELECT p.*,ct.content_type,u.user_name,u.avatar_path,COUNT(l.like_id) AS likes_count FROM posts p 
    JOIN content_type ct ON  p.content_type_id = ct.content_type_id
    JOIN users u ON p.user_id = u.user_id
    LEFT JOIN likes l ON p.post_id = l.post_id
    WHERE p.post_id = $get_post_id";
    $posts_res = mysqli_query($con,$post);
    $posts_rows = mysqli_fetch_all($posts_res, MYSQLI_ASSOC);

    if ($posts_rows == null) {
        header('HTTP/1.0 404 not found');
        print('Параметр запроса отсутствует, либо по этому id не нашли ни одной записи');
    }

    else {
    //Считаем кол-во публикаций у автора поста
    $get_user_id = $posts_rows[0]['user_id'];
    $user_post_count = get_user_posts_count($con,$get_user_id);

    //Считаем количество подписчиков
    $user_followers_count = get_user_followers($con,$get_user_id);

    $page_content = include_template('post_tem.php', [
        'posts_rows' => $posts_rows,
        'user_post_count' => $user_post_count,
        'user_followers_count' => $user_followers_count
    ]);

     $layout_content = include_template('layout.php',[
         'content' => $page_content ,
         'title' => 'Просмотр поста'
     ]);

    print($layout_content);

    }
}

elseif (!isset($_GET['post_id'])) {
        header('HTTP/1.0 404 not found');
        print('Параметр запроса отсутствует, либо если по этому id не нашли ни одной записи');
}

print_r($posts_rows);





