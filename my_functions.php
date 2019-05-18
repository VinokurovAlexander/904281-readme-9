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
 * @param bool $isFromClient Определяет откуда было получено изображение. Если true из $_FILES['tmp_name'], fales - file_get_contents.
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
 * Добавляет хэштеги в БД
 **
 * @param $con соединение с БД
 * @param array $hashtags Массив с хэштегами
 * @param int $post_id текущий post id
 *
 * @return
 */


//function add_hashtags ($con,array $hashtags, int $post_id) {
//    foreach ($hashtags as $hashtag) {
//        //Добавляем данные в таблицу hashtags
//        $hashtag_add_sql = "INSERT INTO hashtags(name) VALUES (?)";
//        $stmt = db_get_prepare_stmt($con,$hashtag_add_sql,[$hashtag]);
//        $res = mysqli_stmt_execute($stmt);
//
//        if ($res) {
//            //Получем id хэштегов
//            $hashtag_id_sql = "SELECT hashtag_id FROM hashtags h WHERE h.name = '$hashtag'";
//            $hashtag_id_result = mysqli_query($con,$hashtag_id_sql);
//            $hashtag_id_array = mysqli_fetch_all($hashtag_id_result, MYSQLI_ASSOC);
//            $hashtag_id = $hashtag_id_array[0]['hashtag_id'];
//
//            //Добавляем данные в таблицу posts-hashtags
//            $hashtags_post_add_sql = 'INSERT INTO posts_hashtags(post_id,hashtag_id) VALUES (?,?)';
//            $stmt = db_get_prepare_stmt($con,$hashtags_post_add_sql,[$post_id,$hashtag_id]);
//            $res = mysqli_stmt_execute($stmt);
//
//            if($res) {
//                $result = true;
//            }
//            else {
//                $result = include_template('error.php',[
//                    'error' => mysqli_error($con)
//                ]);
//            }
//        }
//        else {
//            $result = include_template('error.php',[
//                'error' => mysqli_error($con)
//            ]);
//        }
//        return $result;
//    }
//}

function add_hashtags ($con,array $hashtags, int $post_id) {
    foreach ($hashtags as $hashtag) {
        //Добавляем данные в таблицу hashtags
        $hashtag_add_sql = "INSERT INTO hashtags(name) VALUES (?)";
        $stmt = db_get_prepare_stmt($con, $hashtag_add_sql, [$hashtag]);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            //Получем id хэштегов
            $hashtag_id_sql = "SELECT hashtag_id FROM hashtags h WHERE h.name = '$hashtag'";
            $hashtag_id_result = mysqli_query($con,$hashtag_id_sql);
            $hashtag_id_array = mysqli_fetch_all($hashtag_id_result, MYSQLI_ASSOC);
            $hashtag_id = $hashtag_id_array[0]['hashtag_id'];

            //Добавляем данные в таблицу posts-hashtags
            $hashtags_post_add_sql = 'INSERT INTO posts_hashtags(post_id,hashtag_id) VALUES (?,?)';
            $stmt = db_get_prepare_stmt($con, $hashtags_post_add_sql, [$post_id, $hashtag_id]);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                return $result = true;
            } else {
                $error = mysqli_error($con);
                print("Ошибка MySQL: " . $error);
            }
        } else {
            $error = mysqli_error($con);
            print("Ошибка MySQL: " . $error);
        }

    }
}