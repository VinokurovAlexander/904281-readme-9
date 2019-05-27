<?php

require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

if (isset($_GET['user_id'])) {
    //Получаем id пользователя от которого нужно отписаться
    $to_unsub_id = $_GET['user_id'];

    //Проверяем есть пользователь с таким id
    $get_user_sql = "SELECT u.user_id FROM users u WHERE u.user_id = $to_unsub_id";
    $get_user_res = mysqli_query($con,$get_user_sql);
    $get_user = mysqli_fetch_all($get_user_res, MYSQLI_ASSOC);

    if (empty($get_user)) {
        show_error('Пользователя с таким id не существует');
    }
    else {
        //Получаем id пользователя который будет отписываться
        $who_unsub_id = $_SESSION['user']['user_id'];

        //Исключаем случай отписки от пользователя на которого вы не подписаны
        if (!is_follow($con,$to_unsub_id)) {
            show_error('Вы уже отписаны от данного пользователя');
        }

        //Удаляем данные из таблицы
        $followers_delete_sql = "DELETE FROM follow WHERE who_sub_id = $who_unsub_id AND to_sub_id = $to_unsub_id";
        $followers_delete_result = mysqli_query($con, $followers_delete_sql);

        if ($followers_delete_result) {
            $referer_url = $_SERVER['HTTP_REFERER'];
            header("Location: $referer_url");
            exit;
        }
        else {
            $sql_error = mysqli_error($con);
            show_error($sql_error);
        }
    }
}
else {
    show_error('В параметрах GET запроса отсутствует id пользователя');
}