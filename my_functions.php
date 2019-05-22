<?php

/**
 * Проверяет переданное изображение на соответсвие расширениям gif, jpeg, img
 **
 * @param string $file_name Имя файла, полученное через $_FILES['tmp_name'] или file_get_contents.
 * @param bool $isFromClient Определяет откуда было получено изображение. Если true из $_FILES['tmp_name'], fales - file_get_contents.
 *
 * @return bool true при совпадении с расширениями gif, jpeg, img, иначе false
 */


function checking_image_type(string $file_name, bool $isFromClient = true) : bool {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    if ($isFromClient) {
        $file_type = finfo_file($finfo,$file_name);
    }
    else {
        $file_type = finfo_buffer($finfo,$file_name);
    }

    if ($file_type !== 'image/gif' and $file_type !== 'image/jpeg' and $file_type !== 'image/png') {
        $result = false;
    }

    else {
        $result = true;
    }

    return $result;

}

/**
 * Получает массив с хэштегами для поста
 **
 * @param $con соединение с БД
 * @param int $current_ct_id Текущий content type id
 * @param array $post Массив с передаваемыми данными методом POST
 *
 * @return array Возвращает массив с хэштегами
 */

function get_hashtags ($con,int $current_ct_id, array $post ) {

    $hashtags_sql = "SELECT field_name FROM required_fields WHERE content_type_id = $current_ct_id AND fd_rus_id = 9";
    $hashtags_result = mysqli_query($con, $hashtags_sql);
    $hashtags_fieldname_array = mysqli_fetch_all($hashtags_result, MYSQLI_ASSOC);
    $hashtags_fieldname = $hashtags_fieldname_array[0]['field_name'];


    if (!empty($post[$hashtags_fieldname])) {
        $hashtags = explode(' ',$post[$hashtags_fieldname]);
        return $hashtags;
    }

}

/**
 * Проверяет и добавляет хэштеги в БД
 **
 * @param $con соединение с БД
 * @param array $hashtags Хэштег, полученный из формы
 * @param int $post_id текущий post id
 *
 * @return
 */

function add_hashtags($con,array $hashtags, int $post_id) {

    foreach ($hashtags as $hashtag) {

        $hashtag = mysqli_real_escape_string($con,$hashtag);
        $get_hashtag_id_sql = "SELECT hashtag_id FROM hashtags h WHERE h.name = '$hashtag'";
        $get_hashtag_id_result = mysqli_query($con,$get_hashtag_id_sql);
        $get_hashtag_id_array = mysqli_fetch_all($get_hashtag_id_result, MYSQLI_ASSOC);


        if (!empty($get_hashtag_id_array)) {
            $get_hashtag_id = $get_hashtag_id_array[0]['hashtag_id'];
        }
        else {

            //Добавляем данные в таблицу hashtags
            $hashtag_add_sql = "INSERT INTO hashtags(name) VALUES (?)";
            $stmt = db_get_prepare_stmt($con,$hashtag_add_sql,[$hashtag]);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                $get_hashtag_id = mysqli_insert_id($con);
            }
            else {
                $error = mysqli_error($con);
                print('Ошибка MySQL: ' . $error . '<br>');
            }
        }

        //Получили id, добавляем в таблицу posts_hashtags
        $hashtags_post_add_sql = 'INSERT INTO posts_hashtags(post_id,hashtag_id) VALUES (?,?)';
        $stmt = db_get_prepare_stmt($con,$hashtags_post_add_sql,[$post_id,$get_hashtag_id]);
        $res = mysqli_stmt_execute($stmt);

        if (!$res) {
            $error = mysqli_error($con);
            print("Ошибка MySQL: " . $error);
        }
    }
    if (empty($error)) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * Добавляет данные, переданные через форму в БД
 **
 * @param $con соединение с БД
 * @param int $content_type_id Тип публикуемого поста
 *@param array $post Данные, передаваемые через форму
 *
 * @return true - если данные добавлены, в ином случае false
 */

function add_data_to_database ($con, int $content_type_id, array $post) {
    if ($content_type_id == 1) {
        $post_text_add_sql = 'INSERT INTO posts(pub_date, title, text, user_id, content_type_id) VALUES (NOW(),?,?,2,1)';
        $stmt = db_get_prepare_stmt($con, $post_text_add_sql, [$post['text-heading'], $post['post-text']]);
    }

    elseif ($content_type_id == 2) {
        $post_quote_add_sql = 'INSERT INTO posts(pub_date,title,text,quote_author,user_id,content_type_id) VALUES (NOW(),?,?,?,1,2)';
        $stmt = db_get_prepare_stmt($con,$post_quote_add_sql,[$post['quote-heading'],$post['quote-text'],$post['quote-author']]);
    }

    elseif ($content_type_id == 3) {
        $post_add_sql = 'INSERT INTO posts (pub_date, title, user_id, img, content_type_id) VALUES (NOW(),?,1,?,3)';
        $stmt = db_get_prepare_stmt($con,$post_add_sql,[$post['photo-heading'], $post['img_path']]);

    }

    elseif ($content_type_id == 4) {
        $post_video_add_sql = 'INSERT INTO posts (pub_date, title, user_id, video, content_type_id)
                                    VALUES (NOW(),?,2,?,4)';
        $stmt = db_get_prepare_stmt($con,$post_video_add_sql,[$post['video-heading'],$post['video-link']]);
    }

    elseif ($content_type_id == 5) {
        $post_link_add_sql = 'INSERT INTO posts(pub_date,title,link,user_id,content_type_id) VALUES (NOW(),?,?,2,5)';
        $stmt = db_get_prepare_stmt($con, $post_link_add_sql, [$post['link-heading'], $post['post-link']]);
    }

    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * Проверяет наличие указанного email в БД
 **
 * @param $con соединение с БД
 * @param string $email Почта, которую нужно проверить
 *
 *
 * @return array Массив с данными из БД
 */

function get_email($con, string $email) {
    $email = mysqli_real_escape_string($con,$email);
    $get_email_sql = "SELECT email FROM users u WHERE u.email = '$email'";
    $get_email_result = mysqli_query($con,$get_email_sql);
    $get_email = mysqli_fetch_all($get_email_result, MYSQLI_ASSOC);

    return $get_email;
}



