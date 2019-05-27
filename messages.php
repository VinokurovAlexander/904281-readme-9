<?php

require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}
$errors = [];

//Получаем id пользователя с которым в рамках всех диалогов есть самое свежее сообщение
//Необходимо для задания id по умолчанию для данной страницы
if (!isset($_GET['user_id'])) {
    $_GET['user_id'] = get_deafult_id_for_messages($con);
}


//---------------------------Отправка сообщения---------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Проверяем существование отправителя
    $recipient_user_id = $_GET['user_id'];
    $recipient_user_id_sql = "SELECT u.user_id FROM users u WHERE u.user_id = $recipient_user_id";
    $recipient_user_id_res = mysqli_query($con,$recipient_user_id_sql);
    $recipient_user_id_array = mysqli_fetch_all($recipient_user_id_res,MYSQLI_ASSOC);

    if (empty($recipient_user_id_array)) {
        show_error('Пользователя с таким id не существует');
    }

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
        $isDialog_sql = "SELECT m.dialog_id FROM messages m
                         WHERE (m.sen_id = $sender_id AND m.rec_id = $recipient_id) 
                         OR (m.sen_id = $recipient_id AND m.rec_id = $sender_id)";
        $isDialog_res = mysqli_query($con,$isDialog_sql);
        $isDialog = mysqli_fetch_array($isDialog_res, MYSQLI_ASSOC);
        if (empty($isDialog)) {
            //диалога нет, нужно создавать
            $dialog_id = uniqid();
        }
        else {
            //диалог есть
            $dialog_id = $isDialog['dialog_id'];
        }
        //Добавляем данные в таблицу
        $add_message = "INSERT INTO messages(sen_id, rec_id, pub_date, content, dialog_id) VALUES (?,?,NOW(),?,?)";
        $stmt = db_get_prepare_stmt($con,$add_message,[$sender_id,$recipient_id,$post['message-text'],$dialog_id]);
        $res = mysqli_stmt_execute($stmt);
        if (!$res) {
            show_sql_error($con);
        }
    }
}

//Получаем список диалогов для пользователя
$current_user_id = $_SESSION['user']['user_id'];
$dialogs_sql = "SELECT pub_date, content, sen_id, rec_id,  dialog_id
                FROM messages
                WHERE mes_id
                      IN(SELECT max(mes_id)
                      FROM messages
                      WHERE sen_id = $current_user_id OR rec_id = $current_user_id
                      GROUP BY dialog_id)
                ORDER BY pub_date DESC";
$dialogs_res = mysqli_query($con,$dialogs_sql);
$dialogs = mysqli_fetch_all($dialogs_res,MYSQLI_ASSOC);

//Получаем все сообщения из диалога
$dialog_user_id = $_GET['user_id'];
$messages_sql = "SELECT m.pub_date,m.content,m.sen_id,m.rec_id, u.user_name,u.avatar_path FROM messages m
JOIN users u ON m.sen_id = u.user_id
WHERE (m.sen_id = $current_user_id AND m.rec_id = $dialog_user_id) 
   OR (m.sen_id = $dialog_user_id AND m.rec_id = $current_user_id)
ORDER BY m.pub_date";
$messages_res = mysqli_query($con,$messages_sql);
$messages = mysqli_fetch_all($messages_res, MYSQLI_ASSOC);







//print('<pre>');
//
//print('$messages');
//print_r($messages);
//
//print('$session user ');
//print_r($_SESSION);
//
//print('</pre>');


//Получаем список сообщений в диалоге
//$dialog_messages_sql = "SELECT * FROM messages m
//WHERE (m.mes_reс_id = $recipient_id AND m.mes_sender_id = $sender_id)
//   OR (m.mes_reс_id = $sender_id  AND m.mes_sender_id = $recipient_id)
//ORDER BY m.pub_date";
//$dialog_messages_res = mysqli_query($con,$dialog_messages_sql);
//$dialog_messages = mysqli_fetch_all($dialog_messages_res,MYSQLI_ASSOC);









$page_content = include_template('messages_template.php',[
    'errors' => $errors,
    'con' => $con,
    'dialogs' => $dialogs,
    'messages' => $messages
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Личные сообщения']);

print('<pre>');
//print_r($dialog_messages);
print('</pre>');


print($layout_content);


