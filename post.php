<?php
require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

if (!isset($_GET['post_id'])) {
    header('HTTP/1.0 404 not found');
    show_error('Параметр запроса отсутствует, либо по этому id не нашли ни одной записи');
}

$post_id = intval($_GET['post_id']);
$title = 'Просмотр поста';
$errors = [];

$view_count = get_view_count($con,$post_id);
$view_count = $view_count + 1;
add_view_count($con,$post_id,$view_count);


if (get_post($con,$post_id)) {
 $post = get_post($con,$post_id);
}
else {
 show_sql_error();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['message-text'])) {
        $errors = [
            'message-text' => 'Это поле необходимо заполнить'
        ];
    }
    else {
        $message_text = $_POST['message-text'];
        if (strlen($message_text) < 4) {
            $errors = [
                'message-text' => 'Длина комментария не дожна быть меньше 4 символов'
            ];
        }
    }
    if (empty($errors)) {
    add_comment($con,$_POST['message-text'],$_SESSION['user']['user_id'],$post_id);
    }
}


if (isset($_GET['comments']) && $_GET['comments'] == 'full') {
    $comments = get_comments($con,$post_id);
}
else {
    $comments = get_comments($con,$post_id,true);
}


//-------------------------------------------------

//$test = "SELECT c.pub_date,c.content,c.user_id,u.avatar_path,u.user_name FROM comments c
//                        JOIN users u ON u.user_id = c.user_id
//                        WHERE c.post_id = $post_id
//                        ORDER BY c.pub_date DESC";
//$test = $test . 'LIMIT 3';
//print($test);


//-------------------------------------------------


$page_content = include_template('post_tem.php', [
    'post' => $post,
    'con' => $con,
    'errors' => $errors,
    'comments' => $comments
]);

 $layout_content = include_template('layout.php',[
     'content' => $page_content ,
     'title' => $title
 ]);

print($layout_content);











