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


//------------------------------------------------------------------------------------------------------




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

print('<pre>');

//    print('$likes:');
//    print_r($likes);
//    print('<br>');

print('</pre>');







//------------------------------------------------------------------------------------------------------

//Получаем массив с необходимой информацией для отображения лайков пользователя
//$current_post_id = 232;
//$likes_sql = "SELECT l.*,u.user_name,u.avatar_path,p.post_id,p.content_type_id,p.img,p.video FROM likes l
//JOIN users u ON l.who_like_id = u.user_id
//JOIN posts p ON l.post_id = p.post_id
//WHERE l.post_id = $current_post_id";
//$likes_res = mysqli_query($con, $likes_sql);
//$likes = mysqli_fetch_all($likes_res, MYSQLI_ASSOC);


//    $likes = get_likes($con,$posts);

//    $post_id = 5;
//    $likes_sql = "SELECT l.*,u.user_name,u.avatar_path,p.post_id,p.content_type_id,p.img,p.video,ct.icon_class FROM likes l
//            JOIN users u ON l.who_like_id = u.user_id
//            JOIN posts p ON l.post_id = p.post_id
//            JOIN content_type ct ON p.content_type_id = ct.content_type_id
//            WHERE l.post_id = $post_id
//            ORDER BY dt_add DESC";
//    $likes_res = mysqli_query($con, $likes_sql);
//    $likes = mysqli_fetch_all($likes_res, MYSQLI_ASSOC);


    print('<pre>');
//
//    print('$k:');
//    print_r($k);
//    print('<br>');


//        print('$v:');
//        print_r($v);
//        print('<br>');

    print('</pre>');





print('<pre>');

//    print('$likes:');
//    print_r($likes);
//    print('<br>');

print('</pre>');




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

//print('<pre>');
//
//print('$likes:');
//print_r($likes);
//print('<br>');
//
//print('</pre>');



