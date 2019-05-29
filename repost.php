<?php

require_once('sql_connect.php');
require_once('helpers.php');
require_once('my_functions.php');

my_session_start();

$repost_post_id = $_GET['post_id'];

if (is_post($con,$repost_post_id)) {
    $repost_post = get_post($con,$repost_post_id);
    $new_user_id = $_SESSION['user']['user_id'];
    $original_post_user_id = $repost_post['user_id'];

    if ($new_user_id == $original_post_user_id) {
        show_error('Вы не можете делать репосты своих публикаций');
    }

    //Добавляем репост в БД и обновляем поле is_repost у оригинального поста
    $res = add_repost($con,$repost_post);
    if (!$res) {
        show_sql_error($con);
    }
    //Обновляем поле is_repost у оригинального поста
//    $original_post_id = $repost_post['post_id'];
//    $is_repost_update_sql = "UPDATE posts p SET p.is_repost = TRUE WHERE p.post_id = $original_post_id";
//    $is_repost_update_res = mysqli_query($con,$is_repost_update_sql);
//
//    if (!$is_repost_update_res) {
//        show_sql_error($con);
//    }

    $url = '/profile.php/?user_id=' . $_SESSION['user']['user_id'];
    header("Location: $url");
    exit();

}
else {
    show_error('Поста с таким id не существует');
}
