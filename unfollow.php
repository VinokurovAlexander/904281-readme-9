<?php

require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

my_session_start();

if (isset($_GET['user_id'])) {
    //Получаем id пользователя от которого нужно отписаться
    $to_unsub_id = $_GET['user_id'];

    if (!is_user($con, $to_unsub_id)) {
        show_error($con, 'Пользователя с таким id не существует');
    }

    //Получаем id пользователя который будет отписываться
    $who_unsub_id = $_SESSION['user']['user_id'];

    //Исключаем случай отписки от пользователя на которого вы не подписаны
    if (!is_follow($con, $who_unsub_id, $to_unsub_id)) {
        show_error($con, 'Вы не подписаны на данного пользователя');
    }

    if (unfollow($con, $who_unsub_id, $to_unsub_id)) {
        $referer_url = $_SERVER['HTTP_REFERER'];
        header("Location: $referer_url");
        exit();
    }

    show_sql_error($con);

}

show_error($con, 'В параметрах GET запроса отсутствует id пользователя');
