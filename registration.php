<?php
require_once ('helpers.php');
require_once ('sql_connect.php');
require_once ('my_functions.php');


if ($con == false) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
}
else {

    $post = $_POST;
    $required_fields = [
        'Электронная почта' => 'email',
        'Логин' => 'login',
        'Пароль' =>'password',
        'Повтор пароля' =>'password-repeat'
    ];
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
        if($email) {
            //Проверяем существование почтового ящика
            if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = [
                    'field-rus' => 'Электронная почта',
                    'error-title' => 'Недействительный mail',
                    'error-desc' => 'Вы указали недействительный почтовый ящик'
                ];
            }
            else {
                //Сравниваем c почтовыми ящиками из БД
                $get_email = get_email($con,$email);

                if(!empty($get_email)) {
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
            }
            else {
                $password_hash = password_hash($password,PASSWORD_DEFAULT);
            }
        }

        $path='';
        $avatar = $_FILES['userpic-file']['name'];
        if ($avatar) {
            $tmp_name = $_FILES['userpic-file']['tmp_name'];

            //Проверка типа загружаемой картинки
            if (checking_image_type($tmp_name)) {

                //Загружаем картинку в публичную директорию
                $path = 'uploads/avatars/'. uniqid();
                move_uploaded_file($tmp_name, $path);

            } else {
                $errors['userpic-file'] = [
                    'field-rus' => 'Выбрать фото',
                    'error-title' => 'Формат загружемого изображения должен быть : png, jpeg, gif'
                ];

            }
        }

        //Добавляем данные в БД
        if (empty($errors)) {
            $add_user_sql = 'INSERT INTO users(reg_date, email, user_name, password, avatar_path,contacts) VALUES (NOW(),?,?,?,?,?)';
            $stmt = db_get_prepare_stmt($con, $add_user_sql, [$post['email'], $post['login'], $password_hash,$path,'Здесь должны быть контакты']);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                header("Location: /");
                exit;
            }
            else {
                $page_content = include_template('error.php', ['error' => mysqli_error($con)]);
                print($page_content);
                exit;
            }
        }
    }

    $page_content = include_template('registration_template.php', [
        'errors' => $errors
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content ,
    'title' => 'Тест',
    'user_name' => 'Александр',
    'is_auth' => 0,
]);

print($layout_content);