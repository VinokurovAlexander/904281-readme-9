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
function rel_time($pub_date) {
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

function get_user_posts_count($con,int $user_id) {
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

function get_user_followers($con,int $user_id)
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

function get_hashtags ($con,int $post_id) {
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

function get_likes ($con,int $post_id) {
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

function get_youtube_image_preview (string $youtube_url) {
    $video_url_explode = explode('/',$youtube_url);
    $img_name = array_pop($video_url_explode);
    $youtube_image_preview = 'img.youtube.com/vi/' . $img_name . '/sddefault.jpg';
    return $youtube_image_preview;
}

/**
 * Функция, возвращает массив с постами для отображения на странице "Популярное"
 **
 * @param
 *
 * @return
 *
 */

//function get_popular_posts ($con, int $content_type_id = null) {
//    $posts_sql = "SELECT p.*,u.user_name,ct.content_type,ct.icon_class,u.avatar_path,COUNT(l.like_id) AS likes_count FROM posts p
//        INNER JOIN users u ON p.user_id  = u.user_id
//        INNER JOIN content_type ct ON p.content_type_id = ct.content_type_id
//        LEFT JOIN likes l ON p.post_id = l.post_id
//        (IF !epmty($content_type_id)) THEN WHERE p.content_type_id = $content_type_id
//        GROUP BY p.post_id
//        ORDER BY view_count DESC";
//    $posts_res = mysqli_query($con,$posts_sql);
//    $posts_rows = mysqli_fetch_all($posts_res, MYSQLI_ASSOC);
//    return $posts_rows;
//}


/**
 * Функция, отображает шаблон с ошибкой и заканчивает выполнения всего остального сценария
 **
 * @param string $error Текст ошибки
 *
 * @return string Отображает шаблон с ошибкой и заканчивает выполнения всего остального сценария
 *
 */

function show_error(string $error) {
    $page_content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Ошибка']);
    print($layout_content);

    exit;
}

/**
 * Функция, отображает шаблон с ошибкой SQL и заканчивает выполнения всего остального сценария
 **
 * @param $con Соединение с БД
 *
 * @return string Отображает шаблон с ошибкой и заканчивает выполнения всего остального сценария
 *
 */
function show_sql_error($con) {
    $error =  mysqli_error($con);
    $page_content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Ошибка']);
    print($layout_content);
    exit;
}


/**
 * Функция, которая проверяет является ли залогиненный пользователем подписчиком пользователя, указанного в $to_sub_id
 **
 * @param $con Соединение с БД
 * @param int $to_sub_id Пользователь для которого осуществляется проверка
 *
 * @return true если залогиненный пользователь подписан на пользователя, указанного в перемнной $to_sub_id, иначе false
 *
 */

function is_follow($con, int $to_sub_id) {
    $who_sub_id = $_SESSION['user']['user_id'];
    $is_follow_sql = "SELECT * FROM follow WHERE who_sub_id = $who_sub_id AND to_sub_id = $to_sub_id";
    $is_follow_res = mysqli_query($con,$is_follow_sql);
    $is_follow = mysqli_fetch_all($is_follow_res,MYSQLI_ASSOC);
    if (empty($is_follow)) {
        return false;
    }
    else {
        return true;
    }
}

/**
 * Функция, которая отображает время последнего сообщения
 **
 * $message_date Дата публикаци сообщения
 *
 * @return string Время в отформатированно формате.
 * Если сообщение было отправлено/принято в течение текущих суток, то время отображается как %H:%i (14:40)
 * В ином случае формат отображения $d $month (31 дек)
 *
 */

function get_message_time($message_date)
{
    $months = [
        1 => 'янв',
        2 => 'фев',
        3 => 'мар',
        4 => 'апр',
        5 => 'май',
        6 => 'июн',
        7 => 'июл',
        8 => 'авг',
        9 => 'сен',
        10 => 'окт',
        11 => 'нояб',
        12 => 'дек',
    ];

    $dt_mes = date_create($message_date);
    $dt_mes_format = date_format($dt_mes, "d.m.Y");
    $dt_now = date_create('now');
    $dt_now_format = date_format($dt_now, "d.m.Y");

    if ($dt_mes_format == $dt_now_format) {
        $message_date_format = date_format($dt_mes, "G:H");
    } else {
        $message_date_format = date_format($dt_mes, "j n");
        $message_explode = explode(' ', $message_date_format);
        foreach ($months as $key => $month) {
            if ($key == $message_explode[1]) {
                $message_explode[1] = $month;
                $message_date_format = implode(' ', $message_explode);
            }
        }
    }
    return $message_date_format;
}

/**
 * Получаем имя пользователя в диалоге
 **
 * @param $con Соединение с БД
 * @param array $dialog Массив с диалогами пользователя, который получается в результате работы функции get_dialogs()
 *
 * @return string Имя пользователя
 *
 */

function get_dialog_username($con,array $dialog) {
    $user_id = $dialog['sen_id'];
    $current_user_id = $_SESSION['user']['user_id'];
    if ($user_id == $current_user_id) {
        $user_id = $dialog['rec_id'];
    }
    $get_username_sql = "SELECT user_name FROM users u WHERE u.user_id = $user_id";
    $get_username_res = mysqli_query($con,$get_username_sql);
    $username = mysqli_fetch_row($get_username_res);
    return $username = $username[0];
}

/**
 * Получаем аватар пользователя в диалоге
 **
 * @param $con Соединение с БД
 * @param array $dialog Массив с диалогами пользователя, который получается в результате работы функции get_dialogs()
 *
 * @return string Путь к аватару
 *
 */

function get_dialog_avatar($con,array $dialog) {
    $user_id = $dialog['sen_id'];
    $current_user_id = $_SESSION['user']['user_id'];
    if ($user_id == $current_user_id) {
        $user_id = $dialog['rec_id'];
    }
    $get_avatar_sql = "SELECT avatar_path FROM users u WHERE u.user_id = $user_id";
    $get_avatar_res = mysqli_query($con,$get_avatar_sql);
    $avatar = mysqli_fetch_row($get_avatar_res);
    return $avatar = $avatar[0];
}

/**
 * Возвращает id собеседника в диалоге
 **
 * @param $con Соединение с БД
 * @param array $dialog Массив с диалогами пользователя, который получается в результате работы функции get_dialogs()
 *
 * @return int id собеседника в диалоге
 *
 */

function get_dialog_id($con,$dialog) {
    $user_id = $dialog['sen_id'];
    $current_user_id = $_SESSION['user']['user_id'];
    if ($user_id == $current_user_id) {
        $user_id = $dialog['rec_id'];
    }
    $get_user_id_sql = "SELECT user_id FROM users u WHERE u.user_id = $user_id";
    $get_avatar_res = mysqli_query($con,$get_user_id_sql);
    $user_id = mysqli_fetch_row($get_avatar_res);
    return $user_id = $user_id[0];
}

/**
 * Получает список диалогов для пользователя
 *
 **
 *  @param $con Соединение с БД
 *  @param int $user_id Пользователь для которого мы хотим получить массив с активными диалогами
 *
 * @return array Массив с активными диалогами, иначе false
 *
 */
function get_dialogs($con, int $user_id) {
    $dialogs_sql = "SELECT pub_date, content, sen_id, rec_id,  dialog_id
                    FROM messages
                    WHERE mes_id
                          IN(SELECT max(mes_id)
                          FROM messages
                          WHERE sen_id = $user_id OR rec_id = $user_id
                          GROUP BY dialog_id)
                    ORDER BY pub_date DESC";
    $dialogs_res = mysqli_query($con,$dialogs_sql);
    $dialogs = mysqli_fetch_all($dialogs_res,MYSQLI_ASSOC);
    if (empty($dialogs)) {
        return false;
    }
    else {
        return $dialogs;
    }
}

/**
 * Загружает все сообщения для диалога
 *
 **
 *  @param $con Соединение с БД
 *  @param int $current_user_id Пользователь из сессии
 * @param int $dialog_user_id Собеседник в диалоге
 *
 * @return array Массив с сообщениями в рамках диалога, иначе false
 *
 */

function get_dialog_messages($con,int $current_user_id, int $dialog_user_id) {
    $messages_sql = "SELECT m.pub_date,m.content,m.sen_id,m.rec_id, u.user_name,u.avatar_path FROM messages m
JOIN users u ON m.sen_id = u.user_id
WHERE (m.sen_id = $current_user_id AND m.rec_id = $dialog_user_id)
   OR (m.sen_id = $dialog_user_id AND m.rec_id = $current_user_id)
ORDER BY m.pub_date";
    $messages_res = mysqli_query($con,$messages_sql);
    $messages = mysqli_fetch_all($messages_res, MYSQLI_ASSOC);
    if (empty($messages)) {
        $messages = [];
    }
    return $messages;
}


/**
 * //Проверяем существование пользователя в БД
 *
 **
 *  @param $con Соединение с БД
 *  @param int $user_id_Пользователь существование которого необходимо проверить
 *
 * @return bool true если пользователь существует, иначе false
 *
 */

function is_user($con,$user_id) {
    $user_id_sql = "SELECT u.user_id FROM users u WHERE u.user_id = $user_id";
    $user_id_res = mysqli_query($con,$user_id_sql);
    $user_id_array = mysqli_fetch_all($user_id_res,MYSQLI_ASSOC);
    if (empty($user_id_array)) {
        return false;
    }
    else {
        return true;
    }
}

/**
 * Проверяет существование диалога у указанных пользователей
 *
 **
 * @param $con Соединение с БД
 * @param int $user_id_1,$user_id_2 Пользователи между которыми нужно проверить существование диалога
 *
 * @return bool Если диалог существует - true, иначе false.
 *
 */

function is_dialog ($con, int $user_id_1,int $user_id_2) {
    $is_dialog_sql = "SELECT m.dialog_id FROM messages m
                         WHERE (m.sen_id = $user_id_1 AND m.rec_id = $user_id_2) 
                         OR (m.sen_id = $user_id_2 AND m.rec_id = $user_id_1)";
    $is_dialog_res = mysqli_query($con,$is_dialog_sql);
    $is_dialog = mysqli_fetch_array($is_dialog_res, MYSQLI_ASSOC);
    if (empty($is_dialog)) {
        return false;
    }
    else {
        return $is_dialog = $is_dialog['dialog_id'];
    }
}

/**
 * Добавление данных в таблицу messages
 *
 **
 * @param $con Соединение с БД
 * @param int $sender_id id пользователя отправителя сообщения
 * @param int $recipient_id id пользователя принимающего сообщения
 * @param string $message_text Текст сообщения
 * @param int $dialog_id Уникальный идентификатор диалога
 *
 * @return
 *
 */
function add_message($con,int $sender_id,int $recipient_id,string $message_text,$dialog_id) {
    $add_message = "INSERT INTO messages(sen_id, rec_id, pub_date, content, dialog_id) VALUES (?,?,NOW(),?,?)";
    $stmt = db_get_prepare_stmt($con,$add_message,[$sender_id,$recipient_id,$message_text,$dialog_id]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * Возвращает количество комментариев для поста
 *
 **
 * @param $con Соединение с БД
 * @param int $post_id id поста
 *
 * @return int Количество комментариев
 *
 */

function get_comments_count($con, string $post_id) {
    $get_comments_count_sql = "SELECT count(c.comment_id) AS comments_count FROM comments c WHERE c.post_id = $post_id";
    $get_comments_count_res = mysqli_query($con,$get_comments_count_sql);
    $comments_count_array = mysqli_fetch_array($get_comments_count_res,MYSQLI_ASSOC);
    $comments_count = $comments_count_array['comments_count'];
    return $comments_count;
}

/**
 * Возвращает количество просмотров поста
 *
 **
 * @param $con Соединение с БД
 * @param int $post_id id поста
 *
 * @return int Количество просмотров
 *
 */

function get_view_count($con, string $post_id) {
    $get_view_count_sql = "SELECT p.view_count FROM posts p WHERE p.post_id = $post_id";
    $get_view_count_res = mysqli_query($con,$get_view_count_sql);
    $get_view_count_array = mysqli_fetch_array($get_view_count_res, MYSQLI_ASSOC);
    $get_view_count = $get_view_count_array['view_count'];
    return $get_view_count;
}

/**
 * Обновляет данные о количестве просмотров поста
 *
 **
 * @param $con Соединение с БД
 * @param int $post_id id поста
 * @param int $view_count кол-во просмотров
 *
 * @return true если данные добавлены, если иначе false
 *
 */

function add_view_count($con,int $post_id, int $view_count) {
    $add_view_count_sql = "UPDATE posts p SET p.view_count = $view_count WHERE p.post_id = $post_id";
    $add_view_count_res = mysqli_query($con,$add_view_count_sql);
    if ($add_view_count_res) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * Получает данные для отображения поста на странице просмотра поста
 *
 **
 * @param $con Соединение с БД
 * @param int $post_id id поста
 *
 *
 * @return array $post Массив с данными поста, если данные не удалось получить из БД - false
 *
 */

function get_post($con,$post_id) {
    $post_sql = "SELECT p.*,ct.content_type,u.user_name,u.avatar_path,COUNT(l.like_id) AS likes_count FROM posts p 
JOIN content_type ct ON  p.content_type_id = ct.content_type_id
JOIN users u ON p.user_id = u.user_id
LEFT JOIN likes l ON p.post_id = l.post_id
WHERE p.post_id = $post_id";
    $posts_res = mysqli_query($con,$post_sql);
    if(!$posts_res) {
        return false;
    }
    else {
        $post = mysqli_fetch_array($posts_res, MYSQLI_ASSOC);
        return $post;
    }
}

/**
 * Добавляем комментарий в БД
 *
 **
 * @param $con Соединение с БД
 * @param string $text Текст комментария
 * @param int $user_id Автор комментария
 * @param int $post_id Пост к которому оставляется комментарий
 *
 * @return bool Если комментарий добавлен true, иначе false
 *
 */

function add_comment($con,string $text,int $user_id,int $post_id) {
    $add_comment_sql = "INSERT INTO comments(pub_date,content,user_id,post_id) VALUES (NOW(),?,?,?)";
    $stmt = db_get_prepare_stmt($con,$add_comment_sql,[$text,$user_id,$post_id]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * Получаем комменатрии для поста
 *
 **
 * @param $con Соединение с БД
 * @param int $post_id Пост для которого нужно получить комментарии
 *
 * @return array Массив с комментариями
 *
 */

function get_comments($con,int $post_id, bool $limit = false) {
    $get_comments_sql = "SELECT c.pub_date,c.content,c.user_id,u.avatar_path,u.user_name FROM comments c 
                        JOIN users u ON u.user_id = c.user_id
                        WHERE c.post_id = $post_id
                        ORDER BY c.pub_date DESC";
    if ($limit) {
        $get_comments_sql = $get_comments_sql . ' LIMIT 3';
    }
    $get_comments_res = mysqli_query($con, $get_comments_sql);
    $get_comments = mysqli_fetch_all($get_comments_res,MYSQLI_ASSOC);
    return $get_comments;
}

/**
 * Функция проверяет наличие лайка на публикации от залогиненного пользователя
 *
 **
 * @param $con Соединение с БД
 * @param int $post_id Пост для которого нужно проверить наличие лайка
 *
 * @return true если лайк поставлен, в ином случае false
 *
 */

function is_like($con, int $post_id) {
    $user_id = $_SESSION['user']['user_id'];
    $is_like_sql = "SELECT l.like_id FROM likes l
                    WHERE l.post_id = $post_id AND l.who_like_id = $user_id";
    $is_like_res = mysqli_query($con,$is_like_sql);
    $is_like = mysqli_fetch_array($is_like_res);
    if (empty($is_like)) {
        return false;
    }
    else {
        return true;
    }
}

/**
 * Функция проверяет наличие поста с указанным id
 *
 **
 * @param $con Соединение с БД
 * @param int $post_id id Поста наличие которого нужно проверить
 *
 * @return true если пост существует, иначе false
 *
 */

function is_post($con, int $post_id) {
    $is_post_sql = "SELECT p.post_id FROM posts p WHERE p.post_id = $post_id";
    $is_post_res = mysqli_query($con,$is_post_sql);
    $is_post = mysqli_fetch_all($is_post_res,MYSQLI_ASSOC);
    if (empty($is_post)) {
        return false;
    }
    else {
        return true;
    }
}

/**
 * Функция добавляет лайк в таблицу БД
 *
 **
 * @param $con Соединение с БД
 * @param int $post_id id Пост которому ставится лайк
 *
 * @return bool false если лайк не добавлен
 *
 */

function add_like($con, int $post_id) {
    $who_like_id = $_SESSION['user']['user_id'];
    $add_like_sql = 'INSERT INTO likes(who_like_id, post_id,dt_add) VALUES (?,?,NOW())';
    $stmt = db_get_prepare_stmt($con,$add_like_sql,[$who_like_id,$post_id]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        $referer_url = $_SERVER['HTTP_REFERER'];
        header("Location: $referer_url");
        exit;
    }
    else {
        return false;
    }
}

/**
 * Функция удаляет лайк из таблицы
 *
 **
 * @param $con Соединение с БД
 * @param int $post_id id Пост у которого удаляется лайк
 *
 * @return false если лайк не удален
 *
 */

function delete_like($con, int $post_id) {
    $who_like_id = $_SESSION['user']['user_id'];
    $delete_like_sql = "DELETE FROM likes WHERE post_id = $post_id AND who_like_id = $who_like_id";
    $delete_like_res = mysqli_query($con,$delete_like_sql);
    if ($delete_like_res) {
        $referer_url = $_SERVER['HTTP_REFERER'];
        header("Location: $referer_url");
        exit;
    }
    else {
        return false;
    }
}


/**
 * Отображает время в формате дд.мм.гггг чч:мм
 *
 **
 * @param string $time Время которое необхоимо оформатировать
 *
 *
 * @return string время в формате дд.мм.гггг чч:мм
 *
 */

function post_time_title ($time) {
    $ts_time = strtotime($time);
    $format_time = date('j-m-Y G:i', $ts_time);
    return $format_time;
}

/**
 * Функция проверяет наличие сесии
 *
 **
 *
 * @return Если сессия есть возвращает true, в ином случае перенаправляет на страницу авторизации
 *
 */

function my_session_start() {
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: /");
        exit();
    }
    else {
        return true;
    }
}

/**
 * Возвращает типы контента постов
 *
 **
 * @param $con Соединение с БД
 *
 * @return array $content_type Массив с информацией о всех типов контента
 *
 */

function get_content_types ($con) {
    $con_type_sql = "SELECT content_type_id,content_type,icon_class FROM content_type";
    $con_type_res = mysqli_query($con,$con_type_sql);
    $content_types = mysqli_fetch_all($con_type_res, MYSQLI_ASSOC);
    return $content_types;
}

/**
 * Функция возвращает названия класса для кнопок сортировки
 *
 **
 * @param string $sorting_link_name сортировки
 *
 * @return string $result Строка с названием класса.
 *
 */

function get_sorting_link_class($sorting_link_name) {
     if (($_GET['sorting']) == $sorting_link_name . '_desc') {
         $result = 'sorting__link--active';
     }
     elseif (($_GET['sorting']) == $sorting_link_name . '_asc') {
         $result = 'sorting__link--active sorting__link--reverse';
     }
     return $result;
}


/**
 * Функция вовращает тип сортировки
 *
 **
 * @param string $sorting_link_name Название сортировки
 *
 * @return string $sorting_type Возвращает тип сортировки asc или desc
 *
 */

function get_sorting_type($sorting_link_name) {
    $current_link = $_GET['sorting'];
    $current_link_explode = explode('_',$current_link);
    $current_sorting = array_shift($current_link_explode);
    $current_sorting_type = array_pop($current_link_explode);
    if ($current_sorting !== $sorting_link_name) {
        $sorting_type = 'desc';
    }
    elseif ($current_sorting == $sorting_link_name && $current_sorting_type == 'desc') {
        $sorting_type = 'asc';
    }
    elseif ($current_sorting == $sorting_link_name && $current_sorting_type == 'asc') {
        $sorting_type = 'desc';
    }
    return $sorting_type;
}

/**
 * Функция вовращает посты для отображения на странице "Популярное"
 *
 **
 * @param $con Соединение с БД
 *
 * @return array Массив с постами
 *
 */

function get_posts($con,int $pages_items,int $offset) {
    $sorting = $_GET['sorting'];
    $sorting_explode = explode('_',$sorting);
    $sorting_name = array_shift($sorting_explode);
    $sorting_type = array_pop($sorting_explode);

    if ($sorting_name == 'popular') {
        $sorting_name = 'p.view_count';
    }
    elseif ($sorting_name == 'likes') {
        $sorting_name = 'likes_count';
    }
    elseif ($sorting_name == 'date') {
        $sorting_name = 'p.pub_date';
    }

    $content_type_id = $_GET['content_type_id'];
    if ($content_type_id == 'all') {
        $content_type_sql = '';
    }
    else {
        $content_type_sql = 'WHERE p.content_type_id=' . $content_type_id;
    }

    $get_posts_sql = "SELECT p.*,
                     u.user_name,u.avatar_path,
                     ct.content_type,ct.icon_class,
                     COUNT(l.like_id) AS likes_count 
                     FROM posts p
                     INNER JOIN users u ON p.user_id  = u.user_id
                     INNER JOIN content_type ct ON p.content_type_id = ct.content_type_id
                     LEFT JOIN likes l ON p.post_id = l.post_id
                     $content_type_sql
                     GROUP BY p.post_id
                     ORDER BY $sorting_name $sorting_type LIMIT $pages_items OFFSET $offset";
    $get_posts_res = mysqli_query($con,$get_posts_sql);
    $posts = mysqli_fetch_all($get_posts_res, MYSQLI_ASSOC);
    return $posts;
}

/**
 * Функция предотвращает переход на страницу popular.php без всех необходимых данных GET запроса
 *
 **
 * @param $con Соединение с БД
 *
 * @return string Переправляет на старницу "Популярное" с параметрами GET по умолчанию
 *
 */

function check_get() {
    if (empty($_GET) || empty($_GET['content_type_id'])) {
        header("Location: /popular.php/?content_type_id=all&sorting=popular_desc&page=1");
        exit();
    }
    else {
        $content_type_id = $_GET['content_type_id'];
        if (empty($_GET['sorting']) || empty($_GET['page'])) {
            $url = '/popular.php/?content_type_id=' . $content_type_id . '&sorting=popular_desc&page=1';
            header("Location: $url");
            exit();
        }
    }
}
/**
 * Получает количество страниц для отображения постов
 *
 **
 * @param $con Соединение с БД
 * @param int $page_items Количество постов на странице
 *
 * @return int $pages_count Количество страниц
 *
 */

function get_pages_count($con,$page_items) {
    $content_type_id = $_GET['content_type_id'];
    if ($content_type_id == 'all') {
        $content_type = '';
    }
    else {
        $content_type = 'WHERE content_type_id=' . $content_type_id;
    }

    $result = mysqli_query($con, "SELECT COUNT(*) as cnt FROM posts $content_type");
    $items_count = mysqli_fetch_assoc($result)['cnt'];
    $pages_count = ceil($items_count / $page_items);
    return $pages_count;
}

/**
 * Получаем ссылку для кнопок "Следующая и предыдущая страница"
 *
 **
 * @param string $link_type 'prev' если нужно ссылка на предыдущую страницу или 'next' если на следующую
 *
 * @return string $link возвращает ссылку на необходимую страницу
 *
 */

function get_page_link($link_type) {
    $content_type_id = $_GET['content_type_id'];
    $sorting = $_GET['sorting'];
    $page = $_GET['page'];
    if ($link_type == 'prev') {
        $page = $page - 1;
    }
    elseif ($link_type == 'next') {
        $page = $page + 1;
    }
    $link = '/popular.php/?content_type_id=' . $content_type_id . '&sorting=' . $sorting . '&page=' . $page;
    return $link;
}







