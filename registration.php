<?php
require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');

$title = 'Регистрация';

$required_fields = [
    'Электронная почта' => 'email',
    'Логин' => 'login',
    'Пароль' => 'password',
    'Повтор пароля' => 'password-repeat'
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = $_POST;

    //Проверяем заполнены ли обязательные поля
    foreach ($required_fields as $field_rus => $field) {
        if (empty($post[$field])) {
            $errors[$field] = [
                'field_name_rus' => $field_rus,
                'error_title' => 'Заполните это поле',
                'error_desc' => 'Данное поле должно быть обязательно заполнено'
            ];
        }
    }

    //Валидация полей
    $email = $post['email'];
    if ($email) {
        if (validation_email($con, $email) !== null) {
            $errors['email'] = validation_email($con, $email);
        }
    }

    $password = $post['password'];
    $password_repeat = $post['password-repeat'];

    if ($password && $password_repeat) {
        if ($password !== $password_repeat) {
            $errors['password-repeat'] = [
                'field_name_rus' => 'Повтор пароля',
                'error_title' => 'Пароль не совпадает',
                'error_desc' => 'Пароль в данном поле не совпадает с предыдущим'
            ];
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $post['password_hash'] = $password_hash;
        }
    }

    $path = '';
    if (!empty($_FILES['userpic-file']['name'])) {
        $avatar = $_FILES['userpic-file']['name'];
        $tmp_name = $_FILES['userpic-file']['tmp_name'];

        //Проверка типа загружаемой картинки
        if (check_image_type($tmp_name) !== null) {
            $errors['userpic-file'] = check_image_type($tmp_name);
        } else {
            //Загружаем картинку в публичную директорию
            $path = 'uploads/avatars/' . uniqid();
            move_uploaded_file($tmp_name, $path);
            $path = '/' . $path;
        }

    } //Если пользователь не загрузил аватар используем заглушку
    else {
        $path = '/img/avatar.jpg';
    }

    $post['path'] = $path;

    //Добавляем данные в БД
    if (empty($errors)) {
        if (add_user($con, $post)) {
            header("Location: /");
            exit;
        }
        show_sql_error($con);
    }
}

$page_content = include_template('registration_template.php', [
    'errors' => $errors
]);


$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'con' => $con
]);

print($layout_content);
