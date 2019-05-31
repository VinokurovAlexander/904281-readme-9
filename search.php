<?php

require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

my_session_start();
$title = "Поиск";

if (!isset($_GET['search_text']) || empty($_GET['search_text'])) {
    show_error($con,'Параметры запроса неверные или отсутствуют');
}

else {
    $search = $_GET['search_text'];

    if (substr($search, 0, 1) == '#') {
        //Поиск по хэштегам
        $hashtags= explode('#',$search);
        foreach ($hashtags as $hashtag) {
            $search_sql = "SELECT h.hashtag_id,p.*,u.user_name,u.avatar_path,ct.icon_class FROM hashtags h
                           JOIN posts_hashtags ph ON ph.hashtag_id = h.hashtag_id
                           JOIN posts p ON p.post_id = ph.post_id
                           JOIN users u ON u.user_id = p.user_id
                           JOIN content_type ct ON ct.content_type_id = p.content_type_id
                           WHERE h.name = '$hashtag'";
            $result = mysqli_query($con,$search_sql);
        }

    }
    else {
        $search_sql = "SELECT p.*,u.user_name,u.avatar_path 
                   FROM posts p
                   JOIN users u ON u.user_id = p.user_id
                   WHERE MATCH(title, text) AGAINST(?)";
        $stmt = db_get_prepare_stmt($con, $search_sql, [$search]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

if (empty($posts)) {
    $page_content = include_template('no_result_teamplate.php', []);
}
else {
    $page_content = include_template('search_template.php', [
        'posts' => $posts,
        'con' => $con
    ]);
}

$layout_content = include_template('layout.php',[
    'title' => $title,
    'content' => $page_content,
    'con' => $con
]);

print($layout_content);
