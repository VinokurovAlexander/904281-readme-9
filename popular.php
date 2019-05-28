<?php
require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

my_session_start();
check_get();

$title = 'Популярное';

$content_types = get_content_types($con);
$posts = get_posts($con);

$page_content = include_template('popular_template.php', [
    'content_types' => $content_types,
    'posts' => $posts,
    'con' => $con
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content ,
    'title' => $title,

]);

print($layout_content);

