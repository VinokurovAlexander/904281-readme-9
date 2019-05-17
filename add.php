<?php
require_once ('helpers.php');
require_once ('sql_connect.php');
require_once ('my_functions.php');




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

        foreach ($rf as $field) {
            $r_fn = $field['field_name']; // r_fn = required field name
            $r_fn_rus = $field['field_name_rus'];

            print('<pre>');
            print('Обязательные поля $field: ');
            print_r($field);

            print('$r_fn: ');
            print($r_fn);
            print('<br>');

            print('$_POST[$r_fn]: ');
            print($_POST[$r_fn]);

            print('</pre>');

            //Проверяем заполнены ли обязательные поля
            if (empty($_POST[$r_fn])) {
                $errors[$r_fn] = [
                    'field_name_rus' => $r_fn_rus,
                    'error_title' => 'Заполните это поле',
                    'error_desc' => 'Данное поле должно быть обязательно заполнено'
                ];
            }
        }

        //Проверяем корректное заполнение полей

        //Проверяем поля для добавления картинок ------------------------------------------------------------------------------------
        if ($get_ct_id == 3) {

            $photo_link_from_internet = $_POST['photo-link'];
            $photo_from_user = $_FILES['userpic-file-photo']['name'];
            $path = 'uploads/' . uniqid();

                //Изображение загружено через поле "Выбор файла"  или оба поля "Выбор файла" и "Ссылка из интернета"-------
                if ($photo_from_user or ($photo_link_from_internet and $photo_from_user)) {
                    unset($errors['photo-link']);
                    $tmp_name = $_FILES['userpic-file-photo']['tmp_name'];

                  //Валидация полей и публикация поста
                    add_post_img($con,$tmp_name,$post['photo-heading'],$errors);

            }

            //Изображение загружено через поле "Ссылка из интернета" ----------------------------------------------------
            if ($photo_link_from_internet) {
                unset($errors['userpic-file-photo']);

                // Получаем ссылку на изображение из метода POST
                $photo_link = $_POST['photo-link'];

                //Проверяем корректно ли указана ссылка на изображение
                if (!filter_var($photo_link, FILTER_VALIDATE_URL)) {
                    $errors['photo-link'] = [
                        'field_name_rus' => 'Ссылка из интернета',
                        'error_title' => 'Неверно указана ссылка на изображение',
                        'error_desc' => 'Просьба указать ссылку на изображение в виде: "https://site.com"'
                    ];

                }
                else {
                    //Загружаем изображение в переменную
                    $get_image = file_get_contents($photo_link);

                    if ($get_image !== FALSE) {

                        add_post_img($con,$get_image,$post['photo-heading'],$errors,false);

                    }
                    else {
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
                if (check_youtube_url($video_link)) {
                    $post_video_add_sql = 'INSERT INTO posts (pub_date, title, user_id, video, content_type_id)
                            VALUES (NOW(),?,1,?,?)';
                    $stmt = db_get_prepare_stmt($con,$post_video_add_sql,[$post['video-heading'],$post['video'],$get_ct_id]);
                    $res = mysqli_stmt_execute($stmt);

                    if ($res) {
                        $post_id = mysqli_insert_id($con);
                        header("Location: /post.php/?post_id=" . $post_id);
                        exit;
                    }

                    else {
                        $post_add_sql_error = include_template('error.php',[
                            'error' => mysqli_error($con)
                        ]);
                    }
                    }
                else {
                    $errors['video-link'] = [
                        'field_name_rus' => 'Ссылка youtube',
                        'error_title' => 'Неверно указана ссылка на видео',
                        'error_desc' => 'Просьба указать ссылку на существующее видео на youtube'
                    ];
                }
                }

            }
    } // Заканчивается if ($_SERVER['REQUEST_METHOD'] == 'POST')

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

// Вывод результатов

print("<pre>");
//
//print("Полученные данные: ");
//print_r($_POST);
//print("<br>");

//print("Полученные файлы: ");
//print_r($_FILES);
//print("<br>");

//print("Результаты проверки заполнения данных: ");
//print_r($errors);
//print("<br>");
//
//print("Итоговый массив с данными");
//print_r($ct_rows);
//print("<br>");

//print("Обязательные поля: ");
//print_r($rf);
//print("<br>");

print("</pre>");


