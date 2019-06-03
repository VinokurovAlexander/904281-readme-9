<?php

require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

my_session_start();
$title = "Поиск";

if (!isset($_GET['search_text']) || empty($_GET['search_text'])) {
    show_error($con, 'Параметры запроса неверные или отсутствуют');
}

$posts = search($con);

if (empty($posts)) {
    $page_content = include_template('no_result_teamplate.php', []);
} else {
    $page_content = include_template('search_template.php', [
        'posts' => $posts,
        'con' => $con
    ]);
}

$layout_content = include_template('layout.php', [
    'title' => $title,
    'content' => $page_content,
    'con' => $con
]);

print($layout_content);


