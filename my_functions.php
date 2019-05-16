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
 * Добавляет пост с типом "Картинка"
 **
 * @param $con соединение с БД
 * @param string $file_name Имя файла, полученное через $_FILES['tmp_name'] или file_get_contents.
 * @param string $photo_heading Заголовок поста
 * *@param bool $isFromClient Определяет откуда было получено изображение. Если true из $_FILES['tmp_name'], fales - file_get_contents.
 *
 * @return string Перенаправляет на страницу с постом, иначе показывает ошибку связанную с загрузкой данных в БД
 */

function add_img_post ($con, string $file_name,string $photo_heading,bool $isFromClient = true ) :string  {
    $path = 'uploads/' . uniqid();

    if ($isFromClient) {
        move_uploaded_file($file_name, $path);
    }

    else {
        file_put_contents($path,$file_name);
    }


    $post_add_sql = 'INSERT INTO posts (pub_date, title, user_id, img, content_type_id)
                        VALUES (NOW(),?,1,?,3)';
    $stmt = db_get_prepare_stmt($con,$post_add_sql,[$photo_heading, $path]);
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

        $result = $post_add_sql_error;
        return $result;
    }

}

