<?php
require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

session_start();

if (isset($_SESSION['user'])) {
    header("Location: /feed.php");
    exit();
}

$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = $_POST;
    $required_fields = ['email', 'password'];

    //Проверяем заполнение обязательных полей
    foreach ($required_fields as $field) {
        if (empty($post[$field])) {
            $errors[$field] = 'Это поле нужно заполнить';
        }
    }

    $email = mysqli_real_escape_string($con, $post['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($con, $sql);
    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (!count($errors) && $user) {
        if (password_verify($post['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    } elseif (!count($errors) && !$user) {
        $errors['email'] = 'Такой пользователь не найден';
    }

    if (!count($errors)) {
        header("Location: /feed.php");
        exit;
    }
}

$page_content = include_template('main_template.php', [
    'errors' => $errors
]);


print($page_content);



