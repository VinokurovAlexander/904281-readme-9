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
    //Получаем id пользователя на которого будет осуществлена подписка
    $to_sub_id = $_GET['user_id'];

    //Проверяем есть пользователь с таким id
    $get_user_sql = "SELECT u.user_id FROM users u WHERE u.user_id = $to_sub_id";
    $get_user_res = mysqli_query($con,$get_user_sql);
    $get_user = mysqli_fetch_all($get_user_res, MYSQLI_ASSOC);

    if (empty($get_user)) {
        show_error('Пользователя с таким id не существует');
    }
    else {
        //Получаем id пользователя который будет осущетсвлять подписку
        $who_sub_id = $_SESSION['user']['user_id'];

        //Исключаем случай подписки на одного и того же пользователя
        if (is_follow($con,$to_sub_id)) {
            show_error('На этого пользователя вы уже подписаны');
        }

        //Добавляем данные в таблицу
        $followers_add_sql = "INSERT INTO follow(who_sub_id, to_sub_id) VALUES (?,?)";
        $stmt = db_get_prepare_stmt($con,$followers_add_sql,[$who_sub_id,$to_sub_id]);
        $res = mysqli_stmt_execute($stmt);
        if($res) {
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




