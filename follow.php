<?php

require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');
require_once('mail_connect.php');

my_session_start();

if (isset($_GET['user_id'])) {
    //Получаем id пользователя на которого будет осуществлена подписка
    $to_sub_id = intval($_GET['user_id']);

    if (!is_user($con, $to_sub_id)) {
        show_error($con, 'Пользователя с таким id не существует');
    } else {
        //Получаем id пользователя который будет осущетсвлять подписку
        $who_sub_id = $_SESSION['user']['user_id'];

        //Исключаем случай подписки на одного и того же пользователя
        if (is_follow($con, $to_sub_id)) {
            show_error($con, 'На этого пользователя вы уже подписаны');
        }

        //Добавляем данные в таблицу
        if (add_followes($con, $who_sub_id, $to_sub_id)) {

            //Отправляем уведомление на почту
            $user_to_sub = get_user_info($con, $to_sub_id);
            $user_who_sub = get_user_info($con, $who_sub_id);

            if (!send_notification_new_follower($mailer,$user_to_sub,$user_who_sub)) {
                $error = "Не удалось отправить рассылку: " . $logger->dump();
                show_error($con, $error);
            }

            $referer_url = $_SERVER['HTTP_REFERER'];
            header("Location: $referer_url");
            exit;
        }

        show_sql_error($con);

    }
}

show_error($con, 'В параметрах GET запроса отсутствует id пользователя');





