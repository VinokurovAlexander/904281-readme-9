<?php

require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');


my_session_start();
$current_user_id = $_SESSION['user']['user_id'];

$posts = get_posts_for_feed($con,$current_user_id);

$page_content = include_template('feed_template.php', [
    'posts' => $posts,
    'con' => $con
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'user_name' => $_SESSION['user']['user_name'],
    'title' => 'Моя лента'
]);

print($layout_content);