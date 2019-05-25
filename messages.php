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

//Отправка сообщения
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = $_POST;
    if (empty($post['text-message'])) {
        $errors = [
            'text-message' => 'Необходимо ввести текст сообщения'];
    }
    else {

    }
}

//Получаем список контактов
$current_user_id = $_SESSION['user']['user_id'];
//$get_contacts_sql = "SELECT
//       m.*,
//       u.user_name AS mes_sen_user_name,
//       u.avatar_path AS mes_sen_avatar_path
//FROM messages m
//JOIN users u ON m.mes_sender_id = u.user_id
//WHERE m.mes_reс_id = $current_user_id
//ORDER BY m.pub_date DESC ";
//$get_contacts_res = mysqli_query($con,$get_contacts_sql);
//$contacts = mysqli_fetch_all($get_contacts_res,MYSQLI_ASSOC);

// Получаем id пользователей из списка контактов
$get_contacts_id_sql = "SELECT m.mes_sender_id FROM messages m
WHERE m.mes_reс_id = $current_user_id
GROUP BY m.mes_sender_id";
$get_contacts_id_res = mysqli_query($con,$get_contacts_id_sql);
$get_contacts_id_array = mysqli_fetch_all($get_contacts_id_res,MYSQLI_ASSOC);
$get_contacts_id = array_column($get_contacts_id_array, 'mes_sender_id');


//Получаем информацию для отображения списка контактов
//foreach ($get_contacts_id as $contact_id) {
//    $mes_sen_id = $contact_id['mes_sender_id'];
//    $get_contacts_sql = "SELECT
//           m.*,
//           u.user_name AS mes_sen_user_name,
//           u.avatar_path AS mes_sen_avatar_path
//    FROM messages m
//    JOIN users u ON m.mes_sender_id = u.user_id
//    WHERE m.mes_reс_id = $current_user_id AND m.mes_sender_id = $mes_sen_id
//    ORDER BY m.pub_date DESC LIMIT 1";
//    $get_contacts_res = mysqli_query($con,$get_contacts_sql);
//    $contacts[] = mysqli_fetch_all($get_contacts_res,MYSQLI_ASSOC);
//}

$session_user_id = 13; //одно значение
//$dialog_user_id = 17;    //много значений
//
//$get_last_message_sql = "SELECT * FROM messages m
//WHERE (m.mes_sender_id = $dialog_user_id AND m.mes_reс_id = $session_user_id)
//   OR (m.mes_sender_id = $session_user_id AND m.mes_reс_id = $dialog_user_id)
//ORDER BY m.pub_date DESC LIMIT 1";
//$get_last_message_res = mysqli_query($con,$get_last_message_sql);
//$get_last_message = mysqli_fetch_array($get_last_message_res,MYSQLI_ASSOC);

foreach ($get_contacts_id as $contact_id) {
    $contacts_sql = "SELECT 
        m.*,
        u.user_name AS dialog_user_name,
       u.avatar_path AS dialog_user_avatar
    FROM messages m
    JOIN users u ON u.user_id = $contact_id
    WHERE (m.mes_sender_id = $contact_id AND m.mes_reс_id = $session_user_id) 
    OR (m.mes_sender_id = $session_user_id AND m.mes_reс_id = $contact_id)
    ORDER BY m.pub_date DESC LIMIT 1";
    $get_last_message_res = mysqli_query($con,$contacts_sql);
    $contacts[] = mysqli_fetch_array($get_last_message_res,MYSQLI_ASSOC);

//    print('<pre>');
//
//    print('$contact_id: ');
//    print_r($contact_id);
//    print('<br>');
//
//    print('<pre>');
//
//    print('$contacts: ');
//    print_r($contacts);
//    print('<br>');
//
//    print('</pre>');
//
//    print('</pre>');

}


//print('<pre>');
//
//print('$get_contacts_id:');
//print_r($get_contacts_id);
//print('<br>');
//
//
//
//print('$get_last_message:');
//print_r($get_last_message);
//print('<br>');
//
//print('</pre>');


$page_content = include_template('messages_template.php',[
    'errors' => $errors,
    'contacts' => $contacts
    ]);

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Личные сообщения']);
print($layout_content);

