<?php

require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

$current_user_id = $_SESSION['user']['user_id'];
$messages = [];
$errors = [];
$dialogs = [];
$dialog_user_id = null;

if (!empty($_GET['user_id'])) {
    $dialog_user_id = $_GET['user_id'];
    //Проверяем существование пользователя
    if (isUser($con, $dialog_user_id) == false) {
        show_error('Пользователя с таким id не существует');
    }

    if($dialog_user_id == $current_user_id) {
        show_error('Нельзя отправлять сообщения самому себе');
    }

    //Проверяем подписку на пользователя
    if (!isFollow($con,$dialog_user_id)) {
        show_error('Вы не подписаны на данного пользователя');
    }
    
    //Проверяем есть ли диалог с указанным пользователем
    if (!isDialog($con, $current_user_id, $dialog_user_id)) {
        $dialogs[0] = [
            'pub_date' => null,
            'content' => null,
            'sen_id' => $current_user_id,
            'rec_id' => $dialog_user_id,
            'dialog_id' => null
        ];
    }
    else {
        //Загружаем сообщения
        if (get_dialog_messages($con,$current_user_id,$dialog_user_id)) {
            $messages = get_dialog_messages($con,$current_user_id,$dialog_user_id);
        }
        else {
            show_sql_error();
        }
    }
}

if (empty($dialogs)) {
    $dialogs = get_dialogs($con,$current_user_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $post = $_POST;
    //Проверяем заполнение полей
    if (empty($post['message-text'])) {
        $errors = [
            'message-text' => 'Это поле необходимо заполнить'
        ];
    }
    else {
        //Добавляем данные в таблицу
        $sender_id = $_SESSION['user']['user_id'];
        $recipient_id = $_GET['user_id'];

        //Проверяем создан ли у данных пользователей диалог
        if (isDialog($con,$sender_id,$recipient_id)) {
            //диалог есть
            $dialog_id = isDialog($con,$sender_id,$recipient_id);
        }
        else {
            //диалога нет, нужно создавать
            $dialog_id = uniqid();
        }
        //Добавляем данные в таблицу
        if (!add_message($con,$sender_id,$recipient_id,$post['message-text'],$dialog_id)) {
            show_sql_error($con);
        }
    }
}

if (empty($dialogs) && (!isset($_GET['user_id']) || empty($_GET['user_id']))) {
    $page_content = include_template('forever_alone_template.php',[]);
}
else {
    $page_content = include_template('messages_template.php',[
    'errors' => $errors,
    'con' => $con,
    'dialogs' => $dialogs,
    'messages' => $messages
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Личные сообщения']);

print($layout_content);


