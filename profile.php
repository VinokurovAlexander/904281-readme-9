<?php

require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');

my_session_start();

$title = 'Профиль пользователя';

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header('HTTP/1.0 404 not found');
    show_error($con, 'Параметр запроса отсутствует, либо по этому id не нашли ни одной записи');
} else {
    $current_user_id = $_GET['user_id'];
    if (!is_user($con, $current_user_id)) {
        show_error($con, 'Пользователя с таким id не существует');
    }
}

if (empty($_GET['content'])) {
    $url = '/profile.php?user_id=' . $current_user_id . '&content=posts';
    header("Location: $url");
    exit();
} else {
    $content = $_GET['content'];
    if ($content !== 'posts' && $content !== 'likes' && $content !== 'followers') {
        show_error($con, 'Указан неверный тип контента');
    }
}


$errors = [];
$comments = [];
$user = get_user_info($con, $current_user_id);
$posts = get_profile_posts($con, $current_user_id);
$likes = get_profile_likes($con, $current_user_id);
$followers = get_profile_followers($con, $current_user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['message-text'])) {
        $errors = [
            'message-text' => 'Это поле необходимо заполнить'
        ];
    } else {
        $message_text = $_POST['message-text'];
        if (strlen($message_text) < 4) {
            $errors = [
                'message-text' => 'Длина комментария не дожна быть меньше 4 символов'
            ];
        }
    }
    if (empty($errors)) {
        $post_id = $_GET['comments_post_id'];
        add_comment($con, $_POST['message-text'], $_SESSION['user']['user_id'], $post_id);
    }
}

if (isset($_GET['comments_post_id'])) {
    $post_id = $_GET['comments_post_id'];
    if (isset($_GET['show_all'])) {
        $comments = get_comments($con,$post_id,false);
    }
    else {
        $comments = get_comments($con,$post_id,true);
    }
}

$page_content = include_template('profile_template.php', [
    'user' => $user,
    'posts' => $posts,
    'likes' => $likes,
    'con' => $con,
    'followers' => $followers,
    'errors' => $errors,
    'comments' => $comments
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'con' => $con
]);

print($layout_content);




