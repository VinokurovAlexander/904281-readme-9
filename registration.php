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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = $_POST;

    //Проверяем заполнены ли обязательные поля
    foreach ($required_fields as $field_rus => $field) {
        if (empty($post[$field])) {
            $errors[$field] = [
                'field-rus' => $field_rus,
                'error-title' => 'Заполните это поле',
                'error-desc' => 'Данное поле должно быть обязательно заполнено'
            ];
        }
    }

    //Валидация полей
    $email = $post['email'];
    if ($email) {
        //Проверяем существование почтового ящика
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = [
                'field-rus' => 'Электронная почта',
                'error-title' => 'Недействительный mail',
                'error-desc' => 'Вы указали недействительный почтовый ящик'
            ];
        } else {
            //Сравниваем c почтовыми ящиками из БД
            if (is_email($con, $email)) {
                $errors['email'] = [
                    'field-rus' => 'Электронная почта',
                    'error-title' => 'Почтовый ящик уже существует',
                    'error-desc' => 'Вы указали уже существующий почтовый ящик'
                ];
            }
        }
    }

    $password = $post['password'];
    $password_repeat = $post['password-repeat'];

    if ($password && $password_repeat) {
        if ($password !== $password_repeat) {
            $errors['password-repeat'] = [
                'field-rus' => 'Повтор пароля',
                'error-title' => 'Пароль не совпадает',
                'error-desc' => 'Пароль в данном поле не совпадает с предыдущим'
            ];
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $post['password_hash'] = $password_hash;
        }
    }

    $path = '';
    $avatar = $_FILES['userpic-file']['name'];
    if ($avatar) {
        $tmp_name = $_FILES['userpic-file']['tmp_name'];

        //Проверка типа загружаемой картинки
        if (checking_image_type($tmp_name)) {

            //Загружаем картинку в публичную директорию
            $path = 'uploads/avatars/' . uniqid();
            move_uploaded_file($tmp_name, $path);
            $path = '/' . $path;

        } else {
            $errors['userpic-file'] = [
                'field-rus' => 'Выбрать фото',
                'error-title' => 'Формат загружемого изображения должен быть : png, jpeg, gif'
            ];

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
        } else {
            $page_content = include_template('error.php', ['error' => mysqli_error($con)]);
            print($page_content);
            exit;
        }
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