<?php
require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

my_session_start();
$title = 'Популярное';

$page_items = 6;
check_get_popular($_GET,$page_items,$con);

$cur_page = $_GET['page'] ?? 1;
$offset = ($cur_page - 1) * $page_items;

$content_types = get_content_types($con);

if (isset($_GET['sorting']) && isset($_GET['content_type_id'])) {
    $sorting = $_GET['sorting'];
    $content_type_id = $_GET['content_type_id'];
}
$pages_count = get_pages_count($con, $page_items, $content_type_id);

$posts = get_posts($con, $page_items, $offset,$sorting,$content_type_id);

$page_content = include_template('popular_template.php', [
    'content_types' => $content_types,
    'posts' => $posts,
    'con' => $con,
    'pages_count' => $pages_count
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'con' => $con,

]);

print($layout_content);



