<?php
require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

my_session_start();
check_get_popular();

$title = 'Популярное';

$cur_page = $_GET['page'] ?? 1;
$page_items = 6;
$pages_count = get_pages_count($con,$page_items);
$offset = ($cur_page - 1) * $page_items;

if ($_GET['page'] > $pages_count || $_GET['page'] == 0) {
    show_error('Такой страницы не существует');
}

$content_types = get_content_types($con);
$posts = get_posts($con,$page_items,$offset);

$page_content = include_template('popular_template.php', [
    'content_types' => $content_types,
    'posts' => $posts,
    'con' => $con,
    'pages_count' => $pages_count
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content ,
    'title' => $title,

]);

print($layout_content);

