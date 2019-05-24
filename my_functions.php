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
 * Получает массив с хэштегами при публикации поста
 **
 * @param $con соединение с БД
 * @param int $current_ct_id Текущий content type id
 * @param array $post Массив с передаваемыми данными методом POST
 *
 * @return array Возвращает массив с хэштегами
 */

function get_add_hashtags ($con,int $current_ct_id, array $post ) {

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
 * Добавляет данные, переданные через форму добавления поста, в БД
 **
 * @param $con соединение с БД
 * @param int $content_type_id Тип публикуемого поста
 *@param array $post Данные, передаваемые через форму
 *
 * @return true - если данные добавлены, в ином случае false
 */

function add_data_to_database ($con, int $content_type_id, array $post) {
    if ($content_type_id == 1) {
        $post_text_add_sql = 'INSERT INTO posts(pub_date, title, text, user_id, content_type_id) VALUES (NOW(),?,?,?,?)';
        $stmt = db_get_prepare_stmt($con, $post_text_add_sql, [$post['text-heading'], $post['post-text'],$post['user_id'],$content_type_id]);
    }

    elseif ($content_type_id == 2) {
        $post_quote_add_sql = 'INSERT INTO posts(pub_date,title,text,quote_author,user_id,content_type_id) VALUES (NOW(),?,?,?,?,?)';
        $stmt = db_get_prepare_stmt($con,$post_quote_add_sql,[$post['quote-heading'],$post['quote-text'],$post['quote-author'],$post['user_id'],$content_type_id]);
    }

    elseif ($content_type_id == 3) {
        $post_add_sql = 'INSERT INTO posts (pub_date, title, user_id, img, content_type_id) VALUES (NOW(),?,?,?,?)';
        $stmt = db_get_prepare_stmt($con,$post_add_sql,[$post['photo-heading'],$post['user_id'],$post['img_path'],$content_type_id]);
    }

    elseif ($content_type_id == 4) {
        $post_video_add_sql = 'INSERT INTO posts (pub_date, title, user_id, video, content_type_id) VALUES (NOW(),?,?,?,?)';
        $stmt = db_get_prepare_stmt($con,$post_video_add_sql,[$post['video-heading'],$post['user_id'],$post['video-link'],$content_type_id]);
    }
//
    elseif ($content_type_id == 5) {
        $post_link_add_sql = 'INSERT INTO posts(pub_date,title,link,user_id,content_type_id) VALUES (NOW(),?,?,?,?)';
        $stmt = db_get_prepare_stmt($con, $post_link_add_sql, [$post['link-heading'], $post['post-link'],$post['user_id'],$content_type_id]);
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


/**
 * Функция, отображающая время относительно даты, указанной в перемиенной $pub_date
 **
 * @param string $pub_date Дата, относительно которой нужно рассчитать время
 *
 * @return string
 */

//
function rel_post_time ($pub_date) {
    $cur_date = time(); // текущее время
    $post_date= strtotime($pub_date);  // метка для времени поста
    $diff = floor($cur_date - $post_date); //разница между временем поста и текущим временем в секундах
    if ($diff < 3600) {
        $diff = floor($diff / 60);
        $decl = get_noun_plural_form($diff, 'минута', 'минуты','минут'); //узнаем необходимое склонение
    }
    elseif ($diff >= 60 and $diff < 86400) {
        $diff = floor($diff / 3600);
        $decl = get_noun_plural_form($diff, 'час', 'часа','часов');
    }
    elseif ($diff >= 86400 and $diff < 604800) {
        $diff = floor($diff / 86400);
        $decl = get_noun_plural_form($diff, 'день', 'дня','дней');
    }
    elseif ($diff >= 604800 and $diff < 3024000) {
        $diff = floor($diff / 604800);
        $decl = get_noun_plural_form($diff, 'неделя', 'недели','недель');
    }
    elseif ($diff >= 3024000) {
        $diff = floor($diff / 2592000);
        $decl = get_noun_plural_form($diff, 'месяц', 'месяца','месяцев');
    }

    return("$diff $decl");
}

/**
 * Считаем количество публикаций пользователя
 **
 * @param $con соединение с БД
 * @param $user_id id пользователя для которого нужно рассчитать количество публикаций
 *
 * @return int Количество публикаций
 */

function get_user_posts_count($con,$user_id) {
    $post_count_sql = "SELECT p.post_id FROM posts p
    JOIN users u ON p.user_id = u.user_id
    WHERE u.user_id = $user_id";
    $post_count_result = mysqli_query($con,$post_count_sql);
    $user_post_count = mysqli_num_rows($post_count_result);
    return $user_post_count;
}

/**
 * Считаем количество подписчиков пользователя
 **
 * @param $con соединение с БД
 * @param $user_id id пользователя для которого нужно рассчитать количество подписчиков
 *
 * @return int Количество подписчиков
 */

function get_user_followers($con,$user_id)
{
    $followers_count_sql = "SELECT f.to_sub_id FROM follow f
        JOIN users u ON u.user_id = f.to_sub_id
        WHERE u.user_id = $user_id";
    $followers_count_result = mysqli_query($con, $followers_count_sql);
    $user_followers_count = mysqli_num_rows($followers_count_result);
    return $user_followers_count;
}

/**
 * Функция, обрезающая текст
 **
 * @param string $text Текст
 * @param int $num_letters Лимит на количество символов
 *
 *
 * @return string Оригинальный текст, если его длина меньше заданного числа символов.
 * В противном случае урезанный текст с прибавленной к нему ссылкой.
 */

function cut_text ($text,$num_letters) {
    $explode_text = explode(" ",$text);
    $i = 0;
    $sum = 0;
    $new_text = [];
    foreach ($explode_text as $v) {
        if ($sum < $num_letters) {
            $len = mb_strlen($v);
            $sum = $sum + $len;
            array_push($new_text,$v);
            $i++;
        }
    }
    if ($sum > $num_letters) {
        array_pop($new_text);
        $final_text = implode(" ",$new_text) .'...' . "<br>" . "<a class=\"post-text__more-link\" href=\"#\">Читать далее</a>";
    }
    else {
        $final_text = implode(" ",$new_text);
    }
    return $final_text;
}

/**
 * Функция, возвращающая массив с хэштегами для поста, указанного в переменной $post_id
 **
 * @param $con Соединение с БД
 * @param int $post_id id поста для которого нужно получить хэштеги
 *
 *
 * @return array Массив с хэштегами для поста, указанного в переменной $post_id
 *
 */

function get_hashtags ($con,$post_id) {
    $hashtags = [];
    $hashtags_sql = "SELECT h.name FROM hashtags h
JOIN posts_hashtags ph ON h.hashtag_id = ph.hashtag_id
WHERE ph.post_id = $post_id";
    $hashtags_res = mysqli_query($con, $hashtags_sql);
    $hashtags_array = mysqli_fetch_all($hashtags_res, MYSQLI_ASSOC);
    foreach ($hashtags_array as $k => $v) {
        $hashtags[] = $hashtags_array[$k]['name'];
    }
    return $hashtags;
}


/**
 * Функция, возвращает массив с информацией о постах и лайках для отображения во вкладке "Лайки" на личной странице пользователя
 **
 * @param $con Соединение с БД
 * @param array $post_id посты пользователя, для которых нужно отобразить информацию по лайкам
 *
 *
 * @return array массив с информацией о постах и лайках для отображения во вкладке "Лайки" на личной странице пользователя
 *
 */

//function get_likes ($con,$posts) {
//    $likes = [];
//    foreach ($posts as $post) {
//        $post_id = $post['post_id'];
//        $likes_sql = "SELECT l.*,u.user_name,u.avatar_path,p.post_id,p.content_type_id,p.img,p.video FROM likes l
//            JOIN users u ON l.who_like_id = u.user_id
//            JOIN posts p ON l.post_id = p.post_id
//            WHERE l.post_id = $post_id
//            ORDER BY dt_add";
//        $likes_res = mysqli_query($con, $likes_sql);
//        $likes[] = mysqli_fetch_all($likes_res, MYSQLI_ASSOC);
//        foreach ($likes as $k => $v) {
//            $like[] =
//        }
//    }
//    return $likes;
//}


function get_likes ($con,$post_id) {
    $likes_sql = "SELECT l.*,u.user_name,u.avatar_path,p.post_id,p.content_type_id,p.img,p.video,ct.icon_class FROM likes l 
            JOIN users u ON l.who_like_id = u.user_id   
            JOIN posts p ON l.post_id = p.post_id
            JOIN content_type ct ON p.content_type_id = ct.content_type_id
            WHERE l.post_id = $post_id
            ORDER BY dt_add DESC";
    $likes_res = mysqli_query($con, $likes_sql);
    $likes = mysqli_fetch_all($likes_res, MYSQLI_ASSOC);
    return $likes;
}





/**
 * Функция, возвращает ссылку на картинку-превью к видео на youtube
 **
 * @param string $youtube_url Ссылка на видео с youtube
 *
 * @return string возвращает ссылку на картинку-превью к видео на youtube
 *
 */


function get_youtube_image_preview ($youtube_url) {
    $video_url_explode = explode('/',$youtube_url);
    $img_name = array_pop($video_url_explode);
    $youtube_image_preview = 'img.youtube.com/vi/' . $img_name . '/sddefault.jpg';
    return $youtube_image_preview;
}