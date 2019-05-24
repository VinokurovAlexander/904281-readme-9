<?php

require_once('sql_connect.php');
require_once('helpers.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

if (isset($_GET['post_id'])) {
    $current_post_id = $_GET['post_id'];
    //Нужно убедиться, что такое пост существует
    $get_post_sql = "SELECT p.post_id FROM posts p WHERE p.post_id = $current_post_id";
    $get_post_result = mysqli_query($con,$get_post_sql);
    $get_post = mysqli_fetch_all($get_post_result,MYSQLI_ASSOC);
    if (!empty($get_post)) {
        //Добавляем данные в таблицу
        $who_like_id = $_SESSION['user']['user_id'];
        $likes_add_sql = 'INSERT INTO likes(who_like_id, post_id,dt_add) VALUES (?,?,NOW())';
        $stmt = db_get_prepare_stmt($con,$likes_add_sql,[$who_like_id,$current_post_id]);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $referer_url = $_SERVER['HTTP_REFERER'];
            header("Location: $referer_url");
            exit;
        }
        else {
            $page_content = include_template('error.php', [
                'error' => mysqli_error($con),
            ]);
            $layout = include_template('layout.php',[
                'content' => $page_content ,
                'title' => 'Ошибка при добавлении лайка'
            ]);
            print($layout);
            exit;
        }
    }
}
else {
    print('Нет post_id в GET запросе');
}
