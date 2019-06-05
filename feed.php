<?php

require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');


my_session_start();

$title = 'Моя лента';
$current_user_id = $_SESSION['user']['user_id'];
$content_types = get_content_types($con);

if (!isset($_GET['content_type_id']) || empty($_GET['content_type_id'])) {
    header("Location: /feed.php?content_type_id=all");
    exit();
} else {
    $current_content_type_id = $_GET['content_type_id'];
    if ($current_content_type_id !== 'all' && $current_content_type_id > get_content_types_count($con)) {
        show_error($con, 'Такой страницы не существует');
    }
}

$posts = get_posts_for_feed($con, $current_user_id, $current_content_type_id);

$page_content = include_template('feed_template.php', [
    'posts' => $posts,
    'con' => $con,
    'content_types' => $content_types

]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'con' => $con
]);

print($layout_content);