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

            //Проверяем заполнены ли обязательные поля
            if (empty($_POST[$r_fn])) {
                $errors[$r_fn] = [
                    'field_name_rus' => $r_fn_rus,
                    'error_title' => 'Заполните это поле',
                    'error_desc' => 'Данное поле должно быть обязательно заполнено'
                ];
            }
        }


//------Проверяем Форму заполнения поста "Текст"------------------------------------------------------------------------------------
        if ($get_ct_id == 1) {
            if(empty($errors)) {

                //Добавляем данные поста в БД
                $post_text_add_sql = 'INSERT INTO posts(pub_date, title, text, user_id, content_type_id) VALUES (NOW(),?,?,2,1)';
                $stmt = db_get_prepare_stmt($con, $post_text_add_sql, [$post['text-heading'], $post['post-text']]);
                $res = mysqli_stmt_execute($stmt);

                if ($res) {
                    //Публикация поста
                    $post_id = mysqli_insert_id($con);
                    $hashtags = get_hashtags($con, $get_ct_id, $post);

                    foreach ($hashtags as $hashtag) {
                        //Добавление хэштегов
                        if (add_hashtags($con,$hashtag,$post_id)) {
                            header("Location: /post.php/?post_id=" . $post_id);
                        }
                        else {
                            $error = mysqli_error($con);
                            print("Ошибка MySQL: " . $error . '<br>');
                        }
                    }
                }
                else {
                    $post_add_sql_error = include_template('error.php', [
                        'error' => mysqli_error($con)
                    ]);
                }
            }
        }

//------Проверяем Форму заполнения поста "Цитата"------------------------------------------------------------------------------------
        if ($get_ct_id == 2) {
            if(empty($errors)) {
                //Добавляем данные поста в БД
                $post_quote_add_sql = 'INSERT INTO posts(pub_date,title,text,quote_author,user_id,content_type_id) VALUES (NOW(),?,?,?,1,2)';
                $stmt = db_get_prepare_stmt($con,$post_quote_add_sql,[$post['quote-heading'],$post['quote-text'],$post['quote-author']]);
                $res = mysqli_stmt_execute($stmt);

                if($res) {
                    //Публикация поста
                    $post_id = mysqli_insert_id($con);
                    $hashtags = get_hashtags($con,$get_ct_id,$post);

                    foreach ($hashtags as $hashtag) {
                        //Добавление хэштегов
                        if (add_hashtags($con,$hashtag,$post_id)) {
                            header("Location: /post.php/?post_id=" . $post_id);
                        }
                        else {
                            $error = mysqli_error($con);
                            print("Ошибка MySQL: " . $error . '<br>');
                        }
                    }
                }
                else {
                    $post_add_sql_error = include_template('error.php',[
                        'error' => mysqli_error($con)
                    ]);
                }
            }
        }


//------Проверяем Форму заполнения поста "Картинка"------------------------------------------------------------------------------------
        if ($get_ct_id == 3) {

            if (empty($_FILES['userpic-file-photo']['name'])) {
                $errors['userpic-file-photo'] = [
                    'field_name_rus' => 'Выбрать фото',
                    'error_title' => 'Заполните это поле',
                    'error_desc' => 'Данное поле должно быть обязательно заполнено'
                ];
            }

            $photo_link_from_internet = $_POST['photo-link'];
            $photo_from_user = $_FILES['userpic-file-photo']['name'];
            $path = 'uploads/' . uniqid();

//----------Изображение загружено через поле "Выбор файла"  или оба поля "Выбор файла" и "Ссылка из интернета"-------
            if ($photo_from_user or ($photo_link_from_internet and $photo_from_user)) {
                unset($errors['photo-link']);
                $tmp_name = $_FILES['userpic-file-photo']['tmp_name'];

                //Проверка типа загружаемой картинки
                if (checking_image_type($tmp_name)) {
                    if (empty($errors)) {

                        //Добавляем пост
                        move_uploaded_file($tmp_name, $path);

                        $post_add_sql = 'INSERT INTO posts (pub_date, title, user_id, img, content_type_id)
                        VALUES (NOW(),?,1,?,3)';
                        $stmt = db_get_prepare_stmt($con,$post_add_sql,[$post['photo-heading'], $path]);
                        $res = mysqli_stmt_execute($stmt);

                        if ($res) {
                            $post_id = mysqli_insert_id($con);

                            //Добавляем хэштеги
                            $hashtags = get_hashtags($con,$get_ct_id,$post);

                            foreach ($hashtags as $hashtag) {
                                if (add_hashtags($con,$hashtag,$post_id)) {
                                    header("Location: /post.php/?post_id=" . $post_id);
                                }
                                else {
                                    $error = mysqli_error($con);
                                    print("Ошибка MySQL: " . $error . '<br>');
                                }
                            }
                        }
                        else {
                            $post_add_sql_error = include_template('error.php', [
                                'error' => mysqli_error($con)
                            ]);
                        }
                    }
                }
                else {
                    $errors['userpic-file-photo'] = [
                        'field_name_rus' => 'Выбрать фото',
                        'error_title' => 'Формат загружемого изображения должен быть : png, jpeg, gif'
                    ];
                }
            }

//----------Изображение загружено через поле "Ссылка из интернета" ----------------------------------------------------
            if ($photo_link_from_internet) {
                unset($errors['userpic-file-photo']);

                //Проверяем корректно ли указана ссылка на изображение
                if (!filter_var($photo_link_from_internet, FILTER_VALIDATE_URL)) {
                    $errors['photo-link'] = [
                        'field_name_rus' => 'Ссылка из интернета',
                        'error_title' => 'Неверно указана ссылка на изображение',
                        'error_desc' => 'Просьба указать ссылку на изображение в виде: "https://site.com"'
                    ];

                }
                else {
                    //Загружаем изображение в переменную
                    $get_image = file_get_contents($photo_link_from_internet);

                    if ($get_image !== FALSE) {

                        //Проверка типа загружаемой картинки
                        if (checking_image_type($get_image,false)) {
                            if (empty($errors)) {

                                //Публикуем пост
                                file_put_contents($path,$get_image);

                                $post_add_sql = 'INSERT INTO posts (pub_date, title, user_id, img, content_type_id)
                        VALUES (NOW(),?,1,?,3)';
                                $stmt = db_get_prepare_stmt($con,$post_add_sql,[$post['photo-heading'], $path]);
                                $res = mysqli_stmt_execute($stmt);

                                if ($res) {
                                    $post_id = mysqli_insert_id($con);

                                    //Добавляем хэштеги
                                    $hashtags = get_hashtags($con,$get_ct_id,$post);

                                    foreach ($hashtags as $hashtag) {
                                        if (add_hashtags($con,$hashtag,$post_id)) {
                                        header("Location: /post.php/?post_id=" . $post_id);
                                        }
                                        else {
                                            $error = mysqli_error($con);
                                            print("Ошибка MySQL: " . $error . '<br>');
                                        }
                                    }
                                }
                                else {
                                    $post_add_sql_error = include_template('error.php', [
                                        'error' => mysqli_error($con)
                                    ]);
                                }
                            }
                        }
                        else {
                            $errors['photo-link'] = [
                                'field_name_rus' => 'Ссылка из интернета',
                                'error_title' => 'Недопустимый формат изображения',
                                'error_desc' => 'Формат загружемого изображения должен быть : png, jpeg, gif'
                            ];
                        }
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
//------Проверяем Форму заполнения поста "Видео"------------------------------------------------------------------------------------
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

                    //Формируем итоговый URL для видео
                    $youtube_video_id = extract_youtube_id($video_link);

                    if ($youtube_video_id) {
                        $video_link = "https://www.youtube.com/embed/" . $youtube_video_id;
                    }

                        if (empty($errors)) {
                            //Добавляем данные поста в БД
                            $post_video_add_sql = 'INSERT INTO posts (pub_date, title, user_id, video, content_type_id)
                                    VALUES (NOW(),?,2,?,4)';
                            $stmt = db_get_prepare_stmt($con,$post_video_add_sql,[$post['video-heading'],$video_link]);
                            $res = mysqli_stmt_execute($stmt);

                            if ($res) {
                                //Публикация поста
                                $post_id = mysqli_insert_id($con);
                                $hashtags = get_hashtags($con,$get_ct_id,$post);

                                //Добавление хэштегов
                                foreach ($hashtags as $hashtag) {
                                    if (add_hashtags($con,$hashtag,$post_id)) {
                                        header("Location: /post.php/?post_id=" . $post_id);
                                    }
                                    else {
                                        $error = mysqli_error($con);
                                        print("Ошибка MySQL: " . $error . '<br>');
                                    }
                                }
                            }
                            else {
                                $post_add_sql_error = include_template('error.php',[
                                    'error' => mysqli_error($con)
                                ]);
                            }
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

//------Проверяем Форму заполнения поста "Ссылка"------------------------------------------------------------------------------------
        if ($get_ct_id == 5) {
            $link = $post['post-link'];

            if($link) {
                if (!filter_var($link, FILTER_VALIDATE_URL)) {
                    $errors['post-link'] = [
                        'field_name_rus' => 'Ссылка',
                        'error_title' => 'Неверно указана ссылка',
                        'error_desc' => 'Просьба указать ссылку в виде: "https://site.com"'
                    ];
                } else {
                    if (empty($errors)) {
                        //Добавляем данные поста в БД
                        $post_link_add_sql = 'INSERT INTO posts(pub_date,title,link,user_id,content_type_id) VALUES (NOW(),?,?,2,5)';
                        $stmt = db_get_prepare_stmt($con, $post_link_add_sql, [$post['link-heading'], $post['post-link']]);
                        $res = mysqli_stmt_execute($stmt);

                        if ($res) {
                            //Публикация поста
                            $post_id = mysqli_insert_id($con);
                            $hashtags = get_hashtags($con,$get_ct_id,$post);

                            //Добавление хэштегов
                            foreach ($hashtags as $hashtag) {
                                if (add_hashtags($con,$hashtag,$post_id)) {
                                    header("Location: /post.php/?post_id=" . $post_id);
                                }
                                else {
                                    $error = mysqli_error($con);
                                    print("Ошибка MySQL: " . $error . '<br>');
                                }
                            }

                        } else {
                            $post_add_sql_error = include_template('error.php', [
                                'error' => mysqli_error($con)
                            ]);
                        }
                    }
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


