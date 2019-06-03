<?php

require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');

my_session_start();
$title = 'Мои сообщения';

$current_user_id = $_SESSION['user']['user_id'];
$messages = [];
$errors = [];
$dialogs = [];
$dialog_user_id = null;

if (!empty($_GET['user_id'])) {
    $dialog_user_id = $_GET['user_id'];
    //Проверяем существование пользователя
    if (is_user($con, $dialog_user_id) == false) {
        show_error($con, 'Пользователя с таким id не существует');
    }

    if ($dialog_user_id == $current_user_id) {
        show_error($con, 'Нельзя отправлять сообщения самому себе');
    }

    //Проверяем подписку на пользователя
    if (!is_follow($con, $dialog_user_id)) {
        show_error($con, 'Вы не подписаны на данного пользователя');
    }

    //Проверяем есть ли диалог с указанным пользователем
    if (!is_dialog($con, $current_user_id, $dialog_user_id)) {
        $dialogs[0] = [
            'pub_date' => null,
            'content' => null,
            'sen_id' => $current_user_id,
            'rec_id' => $dialog_user_id,
            'dialog_name' => ''
        ];
    } else {
        //Загружаем сообщения
        if (get_dialog_messages($con, $current_user_id, $dialog_user_id)) {
            $messages = get_dialog_messages($con, $current_user_id, $dialog_user_id);
        } else {
            show_sql_error($con);
        }
    }
    read_msg($con);
}

$dialogs = array_merge($dialogs, get_dialogs($con, $current_user_id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $post = $_POST;
    //Проверяем заполнение полей
    if (empty($post['message-text'])) {
        $errors = [
            'message-text' => 'Это поле необходимо заполнить'
        ];
    } else {
        //Добавляем данные в таблицу
        $sender_id = $_SESSION['user']['user_id'];
        $recipient_id = intval($_GET['user_id']);

        //Проверяем создан ли у данных пользователей диалог
        if (is_dialog($con, $sender_id, $recipient_id) !== null) {
            //диалог есть
            $dialog_name = is_dialog($con, $sender_id, $recipient_id);
        } else {
            //диалога нет, нужно создавать
            $dialog_name = uniqid();
        }
        //Добавляем данные в таблицу
        if (!add_message($con, $sender_id, $recipient_id, $post['message-text'], $dialog_name)) {
            show_sql_error($con);
        } else {
            $url = '/messages.php/?user_id=' . $recipient_id;
            header("Location: $url");
            exit();
        }
    }
}

if (empty($dialogs) && (!isset($_GET['user_id']) || empty($_GET['user_id']))) {
    $page_content = include_template('forever_alone_template.php', []);
} else {
    $page_content = include_template('messages_template.php', [
        'errors' => $errors,
        'con' => $con,
        'dialogs' => $dialogs,
        'messages' => $messages
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'con' => $con
]);

print($layout_content);





