<?php
require_once ('helpers.php');
require_once ('sql_connect.php');
require_once ('my_functions.php');

$sql_error = include_template('error.php', [
    'error' => mysqli_error($con)
]);

if ($con == false) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
}

else {

    if (isset($_GET['content_type_id'])) {
       $get_ct_id = intval($_GET['content_type_id']);  // ct = content type
    }

    $ct_all_sql = "SELECT * FROM content_type ct";
    $ct_all_result = mysqli_query($con, $ct_all_sql);
    $ct_all_rows = mysqli_fetch_all($ct_all_result, MYSQLI_ASSOC);

    //Получаем текущий ct по id ct из GET запроса
    $ct_sql = "SELECT content_type FROM content_type WHERE content_type_id = $get_ct_id";
    $ct_result = mysqli_query($con, $ct_sql);
    $ct_rows = mysqli_fetch_all($ct_result, MYSQLI_ASSOC);

    //Валидация формы
    $errors = [];

    //Получаем обязательные поля для формы
    // rf = required fields
    $rf_sql = "SELECT rf.field_name,rf_rus.field_name_rus FROM required_fields rf 
    JOIN rf_rus ON rf.fd_rus_id = rf_rus.rf_rus_id
    WHERE content_type_id = $get_ct_id";
    $rf_result = mysqli_query($con, $rf_sql);
    $rf = mysqli_fetch_all($rf_result, MYSQLI_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        if ($get_ct_id == 3) {

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
            $post['img_path'] = 'uploads/' . uniqid();

            //Изображение загружено через поле "Выбор файла" или через оба поля "Выбор файла" и "Ссылка из интернета"
            if ($photo_from_user or ($photo_link_from_internet and $photo_from_user)) {
                unset($errors['photo-link']);
                $tmp_name = $_FILES['userpic-file-photo']['tmp_name'];

                //Проверка типа загружаемой картинки
                if (checking_image_type($tmp_name)) {

                    //Загружаем картинку в публичную директорию
                    move_uploaded_file($tmp_name, $post['img_path']);

                } else {

                    $errors['userpic-file-photo'] = [
                        'field_name_rus' => 'Выбрать фото',
                        'error_title' => 'Формат загружемого изображения должен быть : png, jpeg, gif'
                    ];

                }
            }
            //Изображение загружено только через поле "Ссылка из интернета"
            elseif ($photo_link_from_internet) {
                unset($errors['userpic-file-photo']);

                //Проверяем корректно ли указана ссылка на изображение
                if (!filter_var($photo_link_from_internet, FILTER_VALIDATE_URL)) {
                    $errors['photo-link'] = [
                        'field_name_rus' => 'Ссылка из интернета',
                        'error_title' => 'Неверно указана ссылка на изображение',
                        'error_desc' => 'Просьба указать ссылку на изображение в виде: "https://site.com"'
                    ];
                } else {
                    //Загружаем изображение в переменную
                    $get_image = file_get_contents($photo_link_from_internet);
                    if ($get_image) {
                        if (checking_image_type($get_image,false)) {
                            file_put_contents($post['img_path'],$get_image);
                        }
                        else {
                            $errors['photo-link'] = [
                                'field_name_rus' => 'Ссылка из интернета',
                                'error_title' => 'Недопустимый формат изображения',
                                'error_desc' => 'Формат загружемого изображения должен быть : png, jpeg, gif'
                            ];
                        }
                    } else {
                        $errors['photo-link'] = [
                            'field_name_rus' => 'Ссылка из интернета',
                            'error_title' => 'Не удалось загрузить изображение',
                            'error_desc' => 'При загрузке изображения возникла ошибка'
                        ];
                    }
                }
            }
        }

        if ($get_ct_id == 4) {
            //Получаем ссылку на видео из метода POST
            $video_link = $_POST['video-link'];

            //Проверяем кооректно ли задан URL
            if (!filter_var($video_link, FILTER_VALIDATE_URL)) {
                $errors['video-link'] = [
                    'field_name_rus' => 'Ссылка youtube',
                    'error_title' => 'Неверно указана ссылка на видео',
                    'error_desc' => 'Просьба указать ссылку на видео в виде: "https://www.youtube.com/"'
                ];
            }
            else {
                //Проверяем существует ли такое видео на youtube
                if (check_youtube_url($video_link)) {
                    //Формируем итоговый URL для видео
                    $youtube_video_id = extract_youtube_id($video_link);
                    $post['video-link'] = "https://www.youtube.com/embed/" . $youtube_video_id;
                } else {
                    $errors['video-link'] = [
                        'field_name_rus' => 'Ссылка youtube',
                        'error_title' => 'Неверно указана ссылка на видео',
                        'error_desc' => 'Просьба указать ссылку на существующее видео на youtube'
                    ];
                }
            }
        }

        if ($get_ct_id == 5) {
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

        //Добавляем данные в БД и публикуем пост
        if(empty($errors)) {
            if (add_data_to_database($con, $get_ct_id, $post)) {
                $post_id = mysqli_insert_id($con);

                //Добавление хэштегов
                $hashtags = get_hashtags($con, $get_ct_id, $post);
                foreach ($hashtags as $hashtag) {

                    if (add_hashtags($con, $hashtag, $post_id)) {
                        header("Location: /post.php/?post_id=" . $post_id);
                    } else {
                        $post_add_sql_error = $sql_error;
                    }
                }
            } else {
                $post_add_sql_error = $sql_error;
            }
        }
    }

    if (!empty($post_add_sql_error)) {
        $post_add = $post_add_sql_error;
    }

    else {

    $post_add = include_template('add_form_temp.php', [
        'get_ct_id' => $get_ct_id,
        'errors' => $errors
    ]);
    }


    $page_content = include_template('add_post_temp.php',[
        'ct_all_rows' => $ct_all_rows,
        'get_ct_id' => $get_ct_id,
        'post_add' => $post_add
    ]);
}


print($page_content);


