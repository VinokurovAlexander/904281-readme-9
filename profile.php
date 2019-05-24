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

//print('<pre>');
////
////print('$_SESSION');
////print_r($_SESSION);
////
////print('$_GET');
////print_r($_GET);
////
////print('</pre>');



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

//Получаем список хэштегов

//    $current_post_id = 232;
//    $hashtags_sql = "SELECT h.name FROM hashtags h
//JOIN posts_hashtags ph ON h.hashtag_id = ph.hashtag_id
//WHERE ph.post_id = $current_post_id";
//    $hashtags_res = mysqli_query($con, $hashtags_sql);
//    $hashtags_array = mysqli_fetch_all($hashtags_res, MYSQLI_ASSOC);
//
//foreach ($hashtags_array as $k => $v) {
//    print('<pre>');

//    print('$k:');
//    print_r($k);
//    print('<br>');

//    print('$hashtags:');
//    $hashtags[] = $hashtags_array[$k]['name'];
//    print_r($hashtags);

//    print('</pre>');
//}



//    print('<pre>');
//
//    print('$test:');
//    print_r($test);
//    print('<br>');

//    print('$hashtags:');
//    print_r($hashtags);
//    print('<br>');


//    print_r($hashtags['230'][0]['name']);
//    print('<br>');
//    print_r($hashtags['230'][1]['name']);


//    print('</pre>');




$page_content = include_template('profile_template.php',[
    'user_post_count' => $user_post_count,
    'user_followers_count' => $user_followers_count,
    'user' => $user,
    'posts' => $posts,
    'con' => $con
]);

$layout_content = include_template('layout.php',[
    'content' => $page_content ,
    'title' => 'Мой профиль',


]);

print($layout_content);





