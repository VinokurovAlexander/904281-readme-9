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
    $post = "SELECT p.*,ct.content_type,u.user_name,u.avatar_path FROM posts p 
    JOIN content_type ct ON  p.content_type_id = ct.content_type_id
    JOIN users u ON p.user_id = u.user_id
    WHERE post_id = $get_post_id";
    $posts_res = mysqli_query($con,$post);
    $posts_rows = mysqli_fetch_all($posts_res, MYSQLI_ASSOC);

    print('<pre>');
    print('posts_rows: ');
    print_r($posts_rows);
    print('</pre>');

    if ($posts_rows == null) {
        header('HTTP/1.0 404 not found');
        print('Параметр запроса отсутствует, либо по этому id не нашли ни одной записи');
    }

    else {
    //Считаем кол-во публикаций у автора поста
    $get_user_id = $posts_rows[0]['user_id'];
//    $post_count_sql = "SELECT p.post_id FROM posts p
//    JOIN users u ON p.user_id = u.user_id
//    WHERE u.user_id = $get_user_id";
//    $post_count_result = mysqli_query($con,$post_count_sql);
//    $user_post_count = mysqli_num_rows($post_count_result); // считаем количество постов пользователя
        $user_post_count = 



    //Считаем количество подписчиков
    $followers_count_sql = "SELECT f.to_sub_id FROM follow f
    JOIN users u ON u.user_id = f.to_sub_id
    WHERE u.user_id = $get_user_id";
    $followers_count_result = mysqli_query($con,$followers_count_sql);
    $user_followers_count =  mysqli_num_rows($followers_count_result);



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

?>



