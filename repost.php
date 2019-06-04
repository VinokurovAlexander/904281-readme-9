<?php

require_once('sql_connect.php');
require_once('helpers.php');
require_once('my_functions.php');

my_session_start();

$repost_post_id = intval($_GET['post_id']);

if (is_post($con, $repost_post_id)) {
    $repost_post = get_post($con, $repost_post_id);
    $new_user_id = $_SESSION['user']['user_id'];
    $original_post_user_id = $repost_post['user_id'];

    if ($new_user_id == $original_post_user_id) {
        show_error($con, 'Вы не можете делать репосты своих публикаций');
    }

    //Добавляем репост в БД
    $res = add_repost($con, $repost_post);
    if (!$res) {
        show_sql_error($con);
    }

    $url = '/profile.php?user_id=' . $_SESSION['user']['user_id'];
    header("Location: $url");
    exit();

}

show_error($con, 'Поста с таким id не существует');

