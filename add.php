<?php
require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');
require_once('mail_connect.php');

my_session_start();
$title = 'Добавление поста';


if (!isset($_GET['content_type_id']) || empty($_GET['content_type_id'])) {
    header("Location: /add.php?content_type_id=1");
    exit();
} else {
    $content_types = get_content_types($con);
    $current_content_type_id = intval($_GET['content_type_id']);
    if ($current_content_type_id > count($content_types)) {
        show_error($con, 'Типа публикации с таким id не существует');
    }
}


//Валидация формы
$errors = [];

//Получаем обязательные поля для формы
$rf = get_required_fields($con, $current_content_type_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = $_POST;

    //Проверяем заполнены ли обязательные поля
    foreach ($rf as $field) {
        $r_fn = $field['field_name']; // r_fn = required field name
        $r_fn_rus = $field['field_name_rus'];

        if (empty($_POST[$r_fn])) {
            $errors[$r_fn] = [
                'field_name_rus' => $r_fn_rus,
                'error_title' => 'Заполните это поле',
                'error_desc' => 'Данное поле должно быть обязательно заполнено'
            ];
        }
    }

    //Проверяем корректность заполнения полей
    if ($current_content_type_id === 3) {

        //Проверяем добавлена ли картинка через поле 'Выбрать фото'
        if (empty($_FILES['userpic-file-photo']['name'])) {

            $errors['userpic-file-photo'] = [
                'field_name_rus' => 'Выбрать фото',
                'error_title' => 'Заполните это поле',
                'error_desc' => 'Данное поле должно быть обязательно заполнено'
            ];

        }

        $photo_link_from_internet = $_POST['photo-link'];
        $photo_from_user = $_FILES['userpic-file-photo']['name'];
        $path = 'uploads/' . uniqid(); //Для перемещения изображения в указанную директорию
        $post['img_path'] = '/' . $path; //Путь для добавления его в БД


        //Изображение загружено через поле "Выбор файла" или через оба поля "Выбор файла" и "Ссылка из интернета"
        if ($photo_from_user || ($photo_link_from_internet && $photo_from_user)) {

            unset($errors['photo-link']);
            $tmp_name = $_FILES['userpic-file-photo']['tmp_name'];

            if (check_image_type($tmp_name) !== null) {
                $errors['userpic-file-photo'] = check_image_type($tmp_name);
            } else {
                move_uploaded_file($tmp_name, $path);
            }

        } //Изображение загружено только через поле "Ссылка из интернета"
        elseif ($photo_link_from_internet) {
            unset($errors['userpic-file-photo']);

            if (check_image_link($photo_link_from_internet) !== null) {
                $errors['photo-link'] = check_image_link($photo_link_from_internet);
            } else {
                $get_image = file_get_contents($photo_link_from_internet);
                file_put_contents($path, $get_image);
            }
        }
    }

    if ($current_content_type_id === 4) {
        if (!empty($_POST['video-link'])) {
            $video_link = $_POST['video-link'];
            if (check_video_link_error($video_link) !== null) {
                $errors['video-link'] = check_video_link_error($video_link);
            }
            $youtube_video_id = extract_youtube_id($video_link);
            $post['video-link'] = "https://www.youtube.com/embed/" . $youtube_video_id;
        }
    }

    if ($current_content_type_id === 5) {
        //Проверяем корректно ли указан URL
        $link = $post['post-link'];

        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            $errors['post-link'] = [
                'field_name_rus' => 'Ссылка',
                'error_title' => 'Неверно указана ссылка',
                'error_desc' => 'Просьба указать ссылку в виде: "https://site.com"'
            ];
        }
    }

    //Публикуем пост и при необходимости отправляем уведомление
    if (empty($errors)) {
        $post['user_id'] = $_SESSION['user']['user_id'];
        add_post($con, $current_content_type_id, $post);

        $post_id = mysqli_insert_id($con);

        //Добавление хэштегов
        $hashtags = get_add_hashtags($con, $current_content_type_id, $post);
        add_hashtags($con, $hashtags, $post_id);

        //Отправляем уведомления
        $current_user_id = intval($_SESSION['user']['user_id']);
        $followers = get_profile_followers($con, $current_user_id);

        foreach ($followers as $user) {
            $msg_content = "Здравствуйте," . $user['user_name'] .
                ". Пользователь " . $_SESSION['user']['user_name'] . " только что опубликовал новую запись " . get_post_title($con,
                    $post_id) .
                ". Посмотрите её на странице пользователя: https://readme/profile.php?user_id=" . $_SESSION['user']['user_id'];
            $subject = "Новая публикация от пользователя " . $_SESSION['user']['user_name'];
            if (!send_notification($mailer, $msg_content, $user['email'], $subject)) {
                $error = "Не удалось отправить рассылку: " . $logger->dump();
                show_error($con, $error);
            }
        }

        header("Location: /post.php?post_id=" . $post_id);
        exit;

    }
}

$post_add = include_template('add_form_temp.php', [
    'errors' => $errors
]);


$page_content = include_template('add_post_temp.php', [
    'content_types' => $content_types,
    'post_add' => $post_add
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => $title,
    'con' => $con
]);


print($layout_content);




