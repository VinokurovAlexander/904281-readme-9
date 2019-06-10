<?php

/**
 * Получает массив с хэштегами при публикации поста
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $current_ct_id Текущий дентификатор типа публикации
 * @param array $post Массив с передаваемыми данными методом POST
 *
 * @return array Возвращает массив с хэштегами
 */

function get_add_hashtags($con, int $current_ct_id, array $post)
{

    $hashtags_sql = "SELECT field_name FROM required_fields WHERE content_type_id = $current_ct_id AND fd_rus_id = 9";
    $hashtags_result = mysqli_query($con, $hashtags_sql);
    $hashtags_fieldname_array = mysqli_fetch_all($hashtags_result, MYSQLI_ASSOC);
    $hashtags_fieldname = $hashtags_fieldname_array[0]['field_name'];

    if (!empty($post[$hashtags_fieldname])) {
        $hashtags = explode(' ', $post[$hashtags_fieldname]);
    } else {
        $hashtags = [];
    }
    return $hashtags;
}

/**
 * Проверяет и добавляет хэштеги в БД
 **
 * @param mysqli $con Реусрс соединения с БД
 * @param array $hashtags Хэштег, полученный из формы
 * @param int $post_id Идентификатор текущего поста
 *
 * @return bool Если хэштеги добавлены - true, иначе false
 */

function add_hashtags($con, array $hashtags, int $post_id)
{

    foreach ($hashtags as $hashtag) {

        $hashtag = mysqli_real_escape_string($con, $hashtag);
        $get_hashtag_id_sql = "SELECT hashtag_id FROM hashtags h WHERE h.name = '$hashtag'";
        $get_hashtag_id_result = mysqli_query($con, $get_hashtag_id_sql);
        $get_hashtag_id_array = mysqli_fetch_all($get_hashtag_id_result, MYSQLI_ASSOC);


        if (!empty($get_hashtag_id_array)) {
            $get_hashtag_id = $get_hashtag_id_array[0]['hashtag_id'];
        } else {

            //Добавляем данные в таблицу hashtags
            $hashtag_add_sql = "INSERT INTO hashtags(name) VALUES (?)";
            $stmt = db_get_prepare_stmt($con, $hashtag_add_sql, [$hashtag]);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                $get_hashtag_id = mysqli_insert_id($con);
            } else {
                return false;
            }
        }

        //Получили id, добавляем в таблицу posts_hashtags
        $hashtags_post_add_sql = 'INSERT INTO posts_hashtags(post_id,hashtag_id) VALUES (?,?)';
        $stmt = db_get_prepare_stmt($con, $hashtags_post_add_sql, [$post_id, $get_hashtag_id]);
        $res = mysqli_stmt_execute($stmt);

        if (!$res) {
            $error = mysqli_error($con);
            print("Ошибка MySQL: " . $error);
        }
    }
    if (empty($error)) {
        return true;
    }

    return false;

}

/**
 * Добавляет данные, переданные через форму добавления поста, в БД
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param string $content_type_id Идентификатор типа публикуемого поста
 * @param array $post Данные, передаваемые через форму
 *
 * @return true - если данные добавлены, в ином случае false
 */

function add_post($con, string $content_type_id, array $post)
{
    if ($content_type_id === '1') {
        $post_text_add_sql = 'INSERT INTO posts(pub_date, title, text, user_id, content_type_id) VALUES (NOW(),?,?,?,?)';
        $stmt = db_get_prepare_stmt($con, $post_text_add_sql,
            [$post['text-heading'], $post['post-text'], $post['user_id'], $content_type_id]);
    } elseif ($content_type_id === '2') {
        $post_quote_add_sql = 'INSERT INTO posts(pub_date,title,text,quote_author,user_id,content_type_id) VALUES (NOW(),?,?,?,?,?)';
        $stmt = db_get_prepare_stmt($con, $post_quote_add_sql,
            [$post['quote-heading'], $post['quote-text'], $post['quote-author'], $post['user_id'], $content_type_id]);
    } elseif ($content_type_id === '3') {
        $post_add_sql = 'INSERT INTO posts (pub_date, title, user_id, img, content_type_id) VALUES (NOW(),?,?,?,?)';
        $stmt = db_get_prepare_stmt($con, $post_add_sql,
            [$post['photo-heading'], $post['user_id'], $post['img_path'], $content_type_id]);
    } elseif ($content_type_id === '4') {
        $post_video_add_sql = 'INSERT INTO posts (pub_date, title, user_id, video, content_type_id) VALUES (NOW(),?,?,?,?)';
        $stmt = db_get_prepare_stmt($con, $post_video_add_sql,
            [$post['video-heading'], $post['user_id'], $post['video-link'], $content_type_id]);
    } //
    elseif ($content_type_id === '5') {
        $post_link_add_sql = 'INSERT INTO posts(pub_date,title,link,user_id,content_type_id) VALUES (NOW(),?,?,?,?)';
        $stmt = db_get_prepare_stmt($con, $post_link_add_sql,
            [$post['link-heading'], $post['post-link'], $post['user_id'], $content_type_id]);
    } else {
        return false;
    }

    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        return true;
    }

    return false;

}

/**
 * Проверяет наличие указанного email в БД
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param string $email Почта, которую нужно проверить
 *
 *
 * @return bool Если указанный почтовый ящик существует в БД - true, иначе false
 */

function is_email($con, string $email)
{
    $email = mysqli_real_escape_string($con, $email);
    $is_email_sql = "SELECT email FROM users u WHERE u.email = '$email'";
    $is_email_result = mysqli_query($con, $is_email_sql);
    $is_email = mysqli_fetch_all($is_email_result, MYSQLI_ASSOC);

    if (empty($is_email)) {
        return false;
    }

    return true;

}


/**
 * Функция, отображающая время относительно даты, указанной в перемиенной $pub_date
 **
 * @param string $pub_date Дата, относительно которой нужно рассчитать время
 *
 * @return string Время в относительном формате
 */

//
function rel_time($pub_date)
{
    $cur_date = time(); // текущее время
    $post_date = strtotime($pub_date);  // метка для времени поста
    $diff = floor($cur_date - $post_date); //разница между временем поста и текущим временем в секундах
    if ($diff < 3600) {
        $diff = floor($diff / 60);
        $decl = get_noun_plural_form($diff, 'минута', 'минуты', 'минут'); //узнаем необходимое склонение
    } elseif ($diff >= 60 && $diff < 86400) {
        $diff = floor($diff / 3600);
        $decl = get_noun_plural_form($diff, 'час', 'часа', 'часов');
    } elseif ($diff >= 86400 && $diff < 604800) {
        $diff = floor($diff / 86400);
        $decl = get_noun_plural_form($diff, 'день', 'дня', 'дней');
    } elseif ($diff >= 604800 && $diff < 3024000) {
        $diff = floor($diff / 604800);
        $decl = get_noun_plural_form($diff, 'неделя', 'недели', 'недель');
    } elseif ($diff >= 3024000) {
        $diff = floor($diff / 2592000);
        $decl = get_noun_plural_form($diff, 'месяц', 'месяца', 'месяцев');
    } else {
        return false;
    }


    return ("$diff $decl");
}

/**
 * Считаем количество публикаций пользователя
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя для которого нужно рассчитать количество публикаций
 *
 * @return int Количество публикаций
 */

function get_user_posts_count($con, int $user_id)
{
    $post_count_sql = "SELECT p.post_id FROM posts p
    JOIN users u ON p.user_id = u.user_id
    WHERE u.user_id = $user_id";
    $post_count_result = mysqli_query($con, $post_count_sql);
    $user_post_count = mysqli_num_rows($post_count_result);
    return $user_post_count;
}

/**
 * Считаем количество подписчиков пользователя
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя для которого нужно рассчитать количество подписчиков
 *
 * @return int Количество подписчиков
 */

function get_user_followers($con, int $user_id)
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
 * @param int $post_id Идентификатор поста для формирования ссылки
 *
 *
 * @return string Оригинальный текст, если его длина меньше заданного числа символов.
 * В противном случае урезанный текст с прибавленной к нему ссылкой.
 */

function cut_text(string $text, int $num_letters, int $post_id)
{
    $explode_text = explode(" ", $text);
    $i = 0;
    $sum = 0;
    $new_text = [];
    foreach ($explode_text as $v) {
        if ($sum < $num_letters) {
            $len = mb_strlen($v);
            $sum = $sum + $len;
            array_push($new_text, $v);
            $i++;
        }
    }
    if ($sum > $num_letters) {
        array_pop($new_text);
        $final_text = implode(" ",
                $new_text) . "..." . "<br>" . "<a class=\"post-text__more-link\" href=\"/post.php?post_id=" . $post_id . "\">Читать далее</a>";
    } else {
        $final_text = implode(" ", $new_text);
    }
    return $final_text;
}

/**
 * Функция, возвращающая массив с хэштегами для поста, указанного в переменной $post_id
 **
 * @param $con Соединение с БД
 * @param int $post_id Идентификатор поста для которого нужно получить хэштеги
 *
 *
 * @return array Массив с хэштегами для поста, указанного в переменной $post_id
 *
 */

function get_hashtags($con, int $post_id)
{
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
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста для которой нужно отобразить информацию по лайкам
 *
 *
 * @return array массив с информацией о постах и лайках для отображения во вкладке "Лайки" на личной странице пользователя
 *
 */

function get_likes($con, int $post_id)
{
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

function get_youtube_image_preview(string $youtube_url)
{
    $video_url_explode = explode('/', $youtube_url);
    $img_name = array_pop($video_url_explode);
    $youtube_image_preview = 'img.youtube.com/vi/' . $img_name . '/sddefault.jpg';
    return $youtube_image_preview;
}

/**
 * Функция, отображает шаблон с ошибкой и заканчивает выполнения всего остального сценария
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param string $error Текст ошибки
 * @param bool $is_404 Параметр, отвечающий за показ кода ответа 404
 *
 * @return string Отображает шаблон с ошибкой и заканчивает выполнения всего остального сценария
 *
 */

function show_error($con, string $error, bool $is_404 = false)
{
    $page_content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'Ошибка',
        'con' => $con
    ]);

    if ($is_404) {
        header('HTTP/1.0 404 not found');
    }

    print($layout_content);
    exit();
}

/**
 * Функция, отображает шаблон с ошибкой SQL и заканчивает выполнения всего остального сценария
 **
 * @param $con Соединение с БД
 *
 * @return string Отображает шаблон с ошибкой и заканчивает выполнения всего остального сценария
 *
 */
function show_sql_error($con)
{
    $error = mysqli_error($con);
    $page_content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Ошибка']);
    print($layout_content);
    exit;
}


/**
 * Функция, которая проверяет наличие подписки между пользователями
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $who_sub_id Идентификатор пользователя инициатора подписки
 * @param int $to_sub_id Идентификатор пользователя на которого осуществляется подписка
 *
 * @return bool Если подписка существует - true, иначе false
 *
 */

function is_follow($con, int $who_sub_id, int $to_sub_id)
{

    $is_follow_sql = "SELECT * FROM follow WHERE who_sub_id = $who_sub_id AND to_sub_id = $to_sub_id";
    $is_follow_res = mysqli_query($con, $is_follow_sql);
    $is_follow = mysqli_fetch_all($is_follow_res, MYSQLI_ASSOC);

    if (empty($is_follow)) {
        return false;
    }

    return true;

}

/**
 * Функция, которая отображает время последнего сообщения в рамках диалога
 **
 * @param string $message_date Дата публикации сообщения
 *
 * @return string Время в отформатированном формате.
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

    if ($dt_mes_format === $dt_now_format) {
        $message_date_format = date_format($dt_mes, "G:H");
    } else {
        $message_date_format = date_format($dt_mes, "j n");
        $message_explode = explode(' ', $message_date_format);
        foreach ($months as $key => $month) {
            if ($key === $message_explode[1]) {
                $message_explode[1] = $month;
                $message_date_format = implode(' ', $message_explode);
            }
        }
    }
    return $message_date_format;
}

/**
 * Получаем имя собеседника в диалоге
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param array $dialog Массив с диалогами пользователя, который получается в результате работы функции get_dialogs()
 * @param string $current_user_id Идентификатор пользователя у которого открыт диалог
 *
 * @return string Имя пользователя
 *
 */

function get_dialog_username($con, array $dialog, string $current_user_id)
{
    $user_id = $dialog['sen_id'];
    if ($user_id === $current_user_id) {
        $user_id = $dialog['rec_id'];
    }
    $get_username_sql = "SELECT user_name FROM users u WHERE u.user_id = $user_id";
    $get_username_res = mysqli_query($con, $get_username_sql);
    $username = mysqli_fetch_row($get_username_res);
    return $username = $username[0];
}

/**
 * Получаем аватар пользователя в диалоге
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param array $dialog Массив с диалогами пользователя, который получается в результате работы функции get_dialogs()
 * @param string $current_user_id Идентификатор пользователя у которого открыт диалог
 *
 * @return string Путь к аватару
 *
 */

function get_dialog_avatar($con, array $dialog, string $current_user_id)
{
    $user_id = $dialog['sen_id'];
    if ($user_id === $current_user_id) {
        $user_id = $dialog['rec_id'];
    }
    $get_avatar_sql = "SELECT avatar_path FROM users u WHERE u.user_id = $user_id";
    $get_avatar_res = mysqli_query($con, $get_avatar_sql);
    $avatar = mysqli_fetch_row($get_avatar_res);
    return $avatar = $avatar[0];
}

/**
 * Возвращает id собеседника в диалоге
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param array $dialog Массив с диалогами пользователя, который получается в результате работы функции get_dialogs()
 * @param string $current_user_id Идентификатор пользователя у которого открыт диалог
 *
 * @return int Идентификатор собеседника в диалоге
 *
 */

function get_dialog_user_id($con, array $dialog, string $current_user_id)
{
    $user_id = $dialog['sen_id'];
    if ($user_id === $current_user_id) {
        $user_id = $dialog['rec_id'];
    }
    $get_user_id_sql = "SELECT user_id FROM users u WHERE u.user_id = $user_id";
    $get_avatar_res = mysqli_query($con, $get_user_id_sql);
    $user_id = mysqli_fetch_row($get_avatar_res);
    return $user_id = $user_id[0];
}

/**
 * Получает список диалогов для пользователя
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя для которого мы хотим получить массив с активными диалогами
 *
 * @return array Массив с активными диалогами
 *
 */
function get_dialogs($con, int $user_id)
{
    $dialogs_sql = "SELECT pub_date, content, sen_id, rec_id,  dialog_name
                    FROM messages
                    WHERE mes_id
                          IN(SELECT max(mes_id)
                          FROM messages
                          WHERE sen_id = $user_id OR rec_id = $user_id
                          GROUP BY dialog_name)
                    ORDER BY pub_date DESC";
    $dialogs_res = mysqli_query($con, $dialogs_sql);
    $dialogs = mysqli_fetch_all($dialogs_res, MYSQLI_ASSOC);

    return $dialogs;

}

/**
 * Загружает все сообщения для диалога
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $current_user_id Идентификатора пользователя из сессии
 * @param int $dialog_user_id Идентификатор собеседника в диалоге
 *
 * @return array Массив с сообщениями в рамках диалога
 *
 */

function get_dialog_messages($con, int $current_user_id, int $dialog_user_id)
{
    $messages_sql = "SELECT m.pub_date,m.content,m.sen_id,m.rec_id, u.user_name,u.avatar_path FROM messages m
JOIN users u ON m.sen_id = u.user_id
WHERE (m.sen_id = $current_user_id AND m.rec_id = $dialog_user_id)
   OR (m.sen_id = $dialog_user_id AND m.rec_id = $current_user_id)
ORDER BY m.pub_date";
    $messages_res = mysqli_query($con, $messages_sql);
    $messages = mysqli_fetch_all($messages_res, MYSQLI_ASSOC);

    return $messages;
}


/**
 * //Проверяем существование пользователя в БД
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя существование которого необходимо проверить
 *
 * @return bool true если пользователь существует, иначе false
 *
 */

function is_user($con, int $user_id)
{
    $user_id_sql = "SELECT u.user_id FROM users u WHERE u.user_id = $user_id";
    $user_id_res = mysqli_query($con, $user_id_sql);
    $user_id_array = mysqli_fetch_all($user_id_res, MYSQLI_ASSOC);
    if (empty($user_id_array)) {
        return false;
    }

    return true;

}

/**
 * Проверяет существование диалога у указанных пользователей
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id_1 Идентификатор пользователя одного из участников диалога
 * @param int $user_id_2 Идентификатор пользователя одного из участников диалога
 *
 * @return string Возвращает идентификатор диалога, если он существует, иначе - null
 *
 */

function is_dialog($con, int $user_id_1, int $user_id_2)
{
    $is_dialog_sql = "SELECT m.dialog_name FROM messages m
                         WHERE (m.sen_id = $user_id_1 AND m.rec_id = $user_id_2) 
                         OR (m.sen_id = $user_id_2 AND m.rec_id = $user_id_1)";
    $is_dialog_res = mysqli_query($con, $is_dialog_sql);
    $is_dialog = mysqli_fetch_array($is_dialog_res, MYSQLI_ASSOC);
    if (empty($is_dialog)) {
        return null;
    }
    return $is_dialog = $is_dialog['dialog_name'];

}

/**
 * Добавление данных в таблицу messages
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $sender_id Идентификатор пользователя отправителя сообщения
 * @param int $recipient_id Идентификатор пользователя принимающего сообщения
 * @param string $message_text Текст сообщения
 * @param string $dialog_name Уникальный идентификатор диалога
 *
 * @return bool Если данные добавлены в БД - true, иначе - false
 *
 */
function add_message($con, int $sender_id, int $recipient_id, string $message_text, string $dialog_name)
{
    $add_message = "INSERT INTO messages(sen_id, rec_id, pub_date, content, dialog_name) VALUES (?,?,NOW(),?,?)";
    $stmt = db_get_prepare_stmt($con, $add_message, [$sender_id, $recipient_id, $message_text, $dialog_name]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        return true;
    }

    return false;

}

/**
 * Возвращает количество комментариев для поста
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста
 *
 * @return int Количество комментариев
 *
 */

function get_comments_count($con, int $post_id)
{
    $get_comments_count_sql = "SELECT count(c.comment_id) AS comments_count 
                               FROM comments c 
                               WHERE c.post_id = $post_id";
    $get_comments_count_res = mysqli_query($con, $get_comments_count_sql);
    $comments_count_array = mysqli_fetch_array($get_comments_count_res, MYSQLI_ASSOC);
    $comments_count = $comments_count_array['comments_count'];
    return $comments_count;
}

/**
 * Возвращает количество просмотров поста
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста
 *
 * @return int Количество просмотров
 *
 */

function get_view_count($con, int $post_id)
{
    $get_view_count_sql = "SELECT p.view_count FROM posts p WHERE p.post_id = $post_id";
    $get_view_count_res = mysqli_query($con, $get_view_count_sql);
    $get_view_count_array = mysqli_fetch_array($get_view_count_res, MYSQLI_ASSOC);
    $get_view_count = $get_view_count_array['view_count'];
    return $get_view_count;
}

/**
 * Обновляет данные о количестве просмотров поста
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста
 * @param int $view_count кол-во просмотров
 *
 * @return bool True если данные добавлены, если иначе false
 *
 */

function add_view_count($con, int $post_id, int $view_count)
{
    $add_view_count_sql = "UPDATE posts p SET p.view_count = $view_count WHERE p.post_id = $post_id";
    $add_view_count_res = mysqli_query($con, $add_view_count_sql);
    if ($add_view_count_res) {
        return true;
    }

    return false;

}

/**
 * Получает данные для отображения поста на странице просмотра поста
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста
 *
 *
 * @return array $post Массив с данными поста.
 *
 */

function get_post($con, $post_id)
{
    $post_sql = "SELECT p.*,ct.content_type,u.user_name,u.avatar_path,COUNT(l.like_id) AS likes_count FROM posts p 
JOIN content_type ct ON  p.content_type_id = ct.content_type_id
JOIN users u ON p.user_id = u.user_id
LEFT JOIN likes l ON p.post_id = l.post_id
WHERE p.post_id = $post_id";
    $posts_res = mysqli_query($con, $post_sql);
    $post = mysqli_fetch_array($posts_res, MYSQLI_ASSOC);
    return $post;

}

/**
 * Добавляем комментарий в БД
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param string $text Текст комментария
 * @param int $user_id Идентификатор автора комментария
 * @param int $post_id Идентификатор поста к которому оставляется комментарий
 *
 * @return bool Если комментарий добавлен true, иначе false
 *
 */

function add_comment($con, string $text, int $user_id, int $post_id)
{
    $add_comment_sql = "INSERT INTO comments(pub_date,content,user_id,post_id) VALUES (NOW(),?,?,?)";
    $stmt = db_get_prepare_stmt($con, $add_comment_sql, [$text, $user_id, $post_id]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        return true;
    }

    return false;

}

/**
 * Функция проверяет наличие лайка на публикации
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста для которого нужно проверить наличие лайка
 * @param int $user_id Идентификатор пользователя инициатора лайка
 *
 * @return bool True если лайк поставлен, в ином случае false
 *
 */

function is_like($con, int $post_id, int $user_id)
{

    $is_like_sql = "SELECT l.like_id FROM likes l
                    WHERE l.post_id = $post_id AND l.who_like_id = $user_id";
    $is_like_res = mysqli_query($con, $is_like_sql);
    $is_like = mysqli_fetch_array($is_like_res);
    if (empty($is_like)) {
        return false;
    }

    return true;

}

/**
 * Функция проверяет наличие поста с указанным id
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста наличие которого нужно проверить
 *
 * @return bool True если пост существует, иначе false
 *
 */

function is_post($con, int $post_id)
{
    $is_post_sql = "SELECT p.post_id FROM posts p WHERE p.post_id = $post_id";
    $is_post_res = mysqli_query($con, $is_post_sql);
    $is_post = mysqli_fetch_all($is_post_res, MYSQLI_ASSOC);
    if (empty($is_post)) {
        return false;
    }

    return true;

}

/**
 * Функция добавляет лайк в таблицу БД
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста которому ставится лайк
 * @param int $who_like_id Идентификатор пользователя инциатора лайка
 *
 * @return string Добавляются данные в БД и возвращает на страницу с которой был произведен запрос, в ином случае
 * перенаправляет на страницу с описанием ошибки
 *
 */

function add_like($con, int $post_id, int $who_like_id)
{

    $add_like_sql = 'INSERT INTO likes(who_like_id, post_id,dt_add) VALUES (?,?,NOW())';
    $stmt = db_get_prepare_stmt($con, $add_like_sql, [$who_like_id, $post_id]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        $referer_url = $_SERVER['HTTP_REFERER'];
        header("Location: $referer_url");
        exit;
    }
    show_sql_error($con);

}

/**
 * Функция удаляет лайк из таблицы
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста у которого удаляется лайк
 * @param int $who_like_id Идентификатор пользователя инциатора лайка
 *
 * @return string Удаляет данные из бд и возвращает на страницу с которой был произведен запрос. В ином случае
 * перенаправляет на страницу с описанием ошибки
 *
 */

function delete_like($con, int $post_id, int $who_like_id)
{

    $delete_like_sql = "DELETE FROM likes WHERE post_id = $post_id AND who_like_id = $who_like_id";
    $delete_like_res = mysqli_query($con, $delete_like_sql);
    if ($delete_like_res) {
        $referer_url = $_SERVER['HTTP_REFERER'];
        header("Location: $referer_url");
        exit;
    }
    show_sql_error($con);

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

function post_time_title($time)
{
    $ts_time = strtotime($time);
    $format_time = date('j-m-Y G:i', $ts_time);
    return $format_time;
}

/**
 * Функция проверяет наличие сесии
 *
 **
 *
 *
 * @return void
 *
 */

function my_session_start()
{
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: /");
        exit();
    }
}

/**
 * Возвращает типы контента постов
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 *
 * @return array $content_type Массив с информацией о всех типах контента
 *
 */

function get_content_types($con)
{
    $con_type_sql = "SELECT * FROM content_type";
    $con_type_res = mysqli_query($con, $con_type_sql);
    $content_types = mysqli_fetch_all($con_type_res, MYSQLI_ASSOC);
    return $content_types;
}

/**
 * Функция возвращает названия класса для кнопок сортировки. Класс отвечает за выделение кнопок сортировки.
 *
 **
 * @param string $sorting_link_name Наименование типа сортировки: posts,likes,popular
 * @param string $current_get_link Наименование текущего типа сортировки из GET запроса
 *
 * @return string $result Строка с названием класса если в GET запросе указан тип сортировки такой же как и в $sorting_link_name.
 * В ином случае null.
 *
 */

function get_sorting_link_class(string $sorting_link_name, string $current_get_link)
{
    if ($current_get_link === $sorting_link_name . '_desc') {
        $result = 'sorting__link--active';
    } elseif ($current_get_link === $sorting_link_name . '_asc') {
        $result = 'sorting__link--active sorting__link--reverse';
    } elseif ($current_get_link !== $sorting_link_name) {
        $result = null;
    } else {
        return false;
    }

    return $result;
}


/**
 * Функция вовращает тип сортировки asc или desc для формирования ссылки
 *
 **
 * @param string $sorting_link_name Наименование типа сортировки: posts,likes,popular
 * @param string $current_get_link Наименование текущего типа сортировки из GET запроса
 *
 * @return string $sorting_type Возвращает тип сортировки asc или desc
 *
 */

function get_sorting_type_link(string $sorting_link_name, string $current_get_link)
{

    $current_sorting_name = get_sorting_name($current_get_link);
    $current_sorting_type = get_sorting_type($current_get_link);

    if ($current_sorting_name !== $sorting_link_name) {
        $sorting_type = 'desc';
    } elseif ($current_sorting_name === $sorting_link_name && $current_sorting_type === 'desc') {
        $sorting_type = 'asc';
    } elseif ($current_sorting_name === $sorting_link_name && $current_sorting_type === 'asc') {
        $sorting_type = 'desc';
    } else {
        return false;
    }
    return $sorting_type;
}

/**
 * Функция вовращает посты для отображения на странице "Популярное"
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $pages_items Количество отображаемых элементов на странице
 * @param int $offset Смещение выборки
 * @param string $sorting Наименование типа сортировки: posts,likes,popular
 * @param string $content_type_id Идентификатор типа поста
 *
 * @return array Массив с постами
 *
 */

function get_posts($con, int $pages_items, int $offset, string $sorting, string $content_type_id)
{
    $sorting_explode = explode('_', $sorting);
    $sorting_name = array_shift($sorting_explode);
    $sorting_type = array_pop($sorting_explode);

    if ($sorting_name === 'popular') {
        $sorting_name = 'p.view_count';
    } elseif ($sorting_name === 'likes') {
        $sorting_name = 'likes_count';
    } elseif ($sorting_name === 'date') {
        $sorting_name = 'p.pub_date';
    }


    if ($content_type_id === 'all') {
        $content_type_sql = '';
    } else {
        $content_type_sql = 'AND p.content_type_id=' . $content_type_id;
    }

    $get_posts_sql = "SELECT p.*,
                     u.user_name,u.avatar_path,
                     ct.content_type,ct.icon_class,
                     COUNT(l.like_id) AS likes_count 
                     FROM posts p
                     INNER JOIN users u ON p.user_id  = u.user_id
                     INNER JOIN content_type ct ON p.content_type_id = ct.content_type_id
                     LEFT JOIN likes l ON p.post_id = l.post_id
                     WHERE repost_id IS NULL $content_type_sql
                     GROUP BY p.post_id
                     ORDER BY $sorting_name $sorting_type LIMIT $pages_items OFFSET $offset";
    $get_posts_res = mysqli_query($con, $get_posts_sql);
    $posts = mysqli_fetch_all($get_posts_res, MYSQLI_ASSOC);
    return $posts;
}

/**
 * Функция предотвращает переход на страницу popular.php без всех необходимых данных GET запроса
 *
 **
 * @param array $get Массив $_GET
 * @param int $page_items Количество постов на странице
 * @param mysqli $con Ресурс соединения с БД
 *
 * @return void
 *
 * Переправляет на старницу "Популярное"  с параметрами GET по умолчанию
 * или перенаправляет на страницу с описанием ошибки
 *
 */

function check_get_popular(array $get, int $page_items, $con)
{
    if (empty($get) || empty($get['content_type_id'])) {
        header("Location: /popular.php?content_type_id=all&sorting=popular_desc&page=1");
        exit();
    }
    $content_type_id = $get['content_type_id'];
    if ($content_type_id > get_content_types_count($con) && $content_type_id !== 'all') {
        show_error($con, 'Неверно указан идентификатор типа контента', true);
    }
    if (empty($get['sorting']) || empty($get['page'])) {
        $url = '/popular.php?content_type_id=' . $content_type_id . '&sorting=popular_desc&page=1';
        header("Location: $url");
        exit();
    }

    if ($get['page'] > get_pages_count($con, $page_items, $content_type_id) || intval($get['page']) === 0) {
        show_error($con, 'Страницы с таким номером не существует', true);
    }

    $sorting_name = get_sorting_name($get['sorting']);
    $sorting_type = get_sorting_type($get['sorting']);

    if ((($sorting_name !== 'popular') && ($sorting_name !== 'likes') && ($sorting_name !== 'date')) ||
        (($sorting_type !== 'desc') && ($sorting_type !== 'asc'))) {
        show_error($con, 'Неверно указаны параметры сортировки', true);
    }

}

/**
 * Получает количество страниц для отображения постов
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $page_items Количество постов на странице
 * @param string $content_type_id Идентификатор типа контента
 *
 * @return int $pages_count Количество страниц
 *
 */

function get_pages_count($con, int $page_items, string $content_type_id)
{
    if ($content_type_id === 'all') {
        $content_type = '';
    } else {
        $content_type = 'AND content_type_id=' . $content_type_id;
    }

    $result = mysqli_query($con, "SELECT COUNT(*) as cnt FROM posts p WHERE p.repost_id is NULL $content_type");
    $items_count = mysqli_fetch_assoc($result)['cnt'];
    $pages_count = ceil($items_count / $page_items);
    return $pages_count;
}

/**
 * Получаем ссылку для кнопок "Следующая и предыдущая страница"
 *
 **
 * @param string $link_type 'prev' если нужно ссылка на предыдущую страницу или 'next' если на следующую
 * @param string $content_type_id Идентификатор типа поста
 * @param string $sorting Тип сортировки
 * @param int $page Номер текущей страницы
 *
 * @return string $link возвращает ссылку на необходимую страницу
 *
 */

function get_page_link($link_type, string $content_type_id, string $sorting, int $page)
{

    if ($link_type === 'prev') {
        $page = $page - 1;
    } elseif ($link_type === 'next') {
        $page = $page + 1;
    }
    $link = '/popular.php?content_type_id=' . $content_type_id . '&sorting=' . $sorting . '&page=' . $page;
    return $link;
}

/**
 * Получаем комменатрии для поста
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста для которого нужно получить комментарии
 * @param bool $need_limit Параметр, отвечающий за кол-во выгружаемых комментариев
 *
 *
 * @return array Массив с комментариями
 *
 */

function get_comments($con, int $post_id, bool $need_limit)
{

    if ($need_limit) {
        $limit = 'LIMIT 3';
    } else {
        $limit = '';
    }

    $get_comments_sql = "SELECT c.pub_date,c.content,c.user_id,u.avatar_path,u.user_name FROM comments c
                        JOIN users u ON u.user_id = c.user_id
                        WHERE c.post_id = $post_id
                        ORDER BY c.pub_date DESC $limit";

    $get_comments_res = mysqli_query($con, $get_comments_sql);
    $get_comments = mysqli_fetch_all($get_comments_res, MYSQLI_ASSOC);
    return $get_comments;
}

/**
 * Получаем всю информацию о пользователе из таблицы users
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя
 *
 * @return array $user Массив с информацией о пользователе из таблицы users
 *
 */

function get_user_info($con, int $user_id)
{
    $user_sql = "SELECT * FROM users u WHERE u.user_id = $user_id";
    $user_res = mysqli_query($con, $user_sql);
    $user = mysqli_fetch_array($user_res, MYSQLI_ASSOC);
    return $user;
}

/**
 * Получаем посты для отображения на странице профиля пользователя
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя
 *
 * @return array Массив с постами пользователя
 *
 */

function get_profile_posts($con, int $user_id)
{
    $posts_sql = "SELECT p.*,ct.icon_class,
                  u.user_name AS author_name,
                  u.avatar_path AS author_avatar,
                  u.user_id AS author_id
                  FROM posts p
                  JOIN content_type ct ON p.content_type_id = ct.content_type_id
                  LEFT JOIN posts p2 ON p2.post_id = p.repost_id
                  LEFT JOIN users u ON u.user_id = p2.user_id
                  WHERE p.user_id = $user_id
                  GROUP BY p.post_id
                  ORDER BY pub_date DESC";
    $posts_res = mysqli_query($con, $posts_sql);
    $posts = mysqli_fetch_all($posts_res, MYSQLI_ASSOC);
    return $posts;
}

/**
 * Массив с необходимой информацией для отображения списка лайков на странице профиля пользователя
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя
 *
 * @return array Массив с необходимой информацией для отображения списка лайков на странице профиля пользователя
 *
 */

function get_profile_likes($con, int $user_id)
{
    $likes_sql = "SELECT
                    l.*,
                    ct.icon_class,
                    p.img,p.video,p.content_type_id,
                    u2.user_name as who_like_name, u2.avatar_path as who_like_avatar_path
                FROM likes l
                    JOIN posts p ON l.post_id = p.post_id
                    JOIN users u ON p.user_id = u.user_id
                    JOIN users u2 ON l.who_like_id = u2.user_id
                    JOIN content_type ct ON p.content_type_id = ct.content_type_id
                WHERE u.user_id = $user_id
                ORDER BY dt_add DESC";
    $likes_res = mysqli_query($con, $likes_sql);
    $likes = mysqli_fetch_all($likes_res, MYSQLI_ASSOC);
    return $likes;
}

/**
 * Массив с необходимой информацией для отображения списка подписчиков на странице профиля пользователя
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя
 *
 * @return array Массив с необходимой информацией для отображения списка подписчиков на странице профиля пользователя
 *
 */

function get_profile_followers($con, int $user_id)
{

    $get_followers_sql = "SELECT u.user_id,u.user_name,u.reg_date,u.avatar_path,u.email FROM users u
                          JOIN follow f ON f.who_sub_id = u.user_id
                          WHERE f.to_sub_id = $user_id";
    $get_followers_result = mysqli_query($con, $get_followers_sql);
    $followers = mysqli_fetch_all($get_followers_result, MYSQLI_ASSOC);
    return $followers;
}

/**
 * Возвращает массив с постами для отображения на странице "Моя лента"
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя
 * @param string $content_type_id Идентификатор типа поста
 *
 * @return array Возвращает массив с постами для отображения на странице "Моя лента"
 *
 */

function get_posts_for_feed($con, int $user_id, string $content_type_id)
{

    if ($content_type_id === 'all') {
        $content_type_id_sql = '';
    } else {
        $content_type_id_sql = 'AND p.content_type_id=' . $content_type_id;
    }

    $get_post_sql = "SELECT f.*,p.*,ct.icon_class,u.avatar_path,u.user_name FROM follow f
                     JOIN posts p ON f.to_sub_id = p.user_id
                     JOIN content_type ct ON ct.content_type_id = p.content_type_id 
                     JOIN users u ON u.user_id = p.user_id
                     WHERE f.who_sub_id = $user_id $content_type_id_sql
                     ORDER BY p.pub_date DESC";
    $get_posts_res = mysqli_query($con, $get_post_sql);
    $posts = mysqli_fetch_all($get_posts_res, MYSQLI_ASSOC);
    return $posts;
}

/**
 * Возвращает количество лайков поста
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста
 *
 * @return int $likes_count Возвращает количество лайков поста
 *
 */

function get_post_likes_count($con, int $post_id)
{
    $get_post_likes_count_sql = "SELECT COUNT(l.like_id) AS likes_count FROM likes l WHERE l.post_id = $post_id";
    $get_post_likes_count_res = mysqli_query($con, $get_post_likes_count_sql);
    $get_post_likes_count_array = mysqli_fetch_array($get_post_likes_count_res, MYSQLI_ASSOC);
    $likes_count = $get_post_likes_count_array['likes_count'];
    return $likes_count;
}


/**
 * Возвращает количество типов контента
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 *
 *
 * @return int Возвращает количество типов контента
 *
 */

function get_content_types_count($con)
{
    $get_ct_count_sql = "SELECT COUNT(ct.content_type_id) AS ct_count FROM content_type ct";
    $get_ct_count_res = mysqli_query($con, $get_ct_count_sql);
    $get_ct_count_array = mysqli_fetch_array($get_ct_count_res, MYSQLI_ASSOC);
    $get_ct_count = $get_ct_count_array['ct_count'];
    return $get_ct_count;
}

/**
 * Возвращает количество репостов
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста
 *
 *
 * @return int Возвращает количество репостов
 *
 */

function get_repost_count($con, int $post_id)
{
    $repost_cnt_sql = "SELECT COUNT(p.repost_id) AS repost_cnt FROM posts p WHERE p.repost_id = $post_id";
    $repost_cnt_res = mysqli_query($con, $repost_cnt_sql);
    $repost_cnt_array = mysqli_fetch_array($repost_cnt_res, MYSQLI_ASSOC);
    $repost_cnt = $repost_cnt_array['repost_cnt'];
    return $repost_cnt;
}

/**
 * Добавляет репост в БД
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param array $repost_post Публикация, для которой нужно сделать репост. Массив полученный с помощью функции get_post().
 * @param int $new_user_id Идентификатор пользователя инициатора репоста
 *
 * @return bool Если данные добавлены в БД - true, в ином случае - false
 *
 */

function add_repost($con, array $repost_post, int $new_user_id)
{

    $repost_add_sql = "INSERT INTO posts(pub_date,title,text,user_id,img,video,
                                         link,quote_author,view_count,content_type_id,repost_id)
                       VALUES (NOW(),?,?,?,?,?,?,?,?,?,?)";
    $stmt = db_get_prepare_stmt($con, $repost_add_sql, [
        $repost_post['title'],
        $repost_post['text'],
        $new_user_id,
        $repost_post['img'],
        $repost_post['video'],
        $repost_post['link'],
        $repost_post['quote_author'],
        0,
        $repost_post['content_type_id'],
        $repost_post['post_id']
    ]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        //получаем хэштеги
        $post_id = mysqli_insert_id($con);
        $repost_post_id = $repost_post['post_id'];
        $get_hashtags_id = "SELECT ph.hashtag_id FROM posts_hashtags ph WHERE ph.post_id = $repost_post_id";
        $get_hashtags_res = mysqli_query($con, $get_hashtags_id);
        $hashtags = mysqli_fetch_all($get_hashtags_res, MYSQLI_ASSOC);

        //добавляем в базу
        foreach ($hashtags as $hashtag) {
            $add_hashtag_sql = "INSERT INTO posts_hashtags(post_id, hashtag_id) VALUES ($post_id,?)";
            $stmt = db_get_prepare_stmt($con, $add_hashtag_sql, [$hashtag['hashtag_id']]);
            $res = mysqli_stmt_execute($stmt);
        }
        if ($res) {
            return true;
        }

    }
    return false;
}

/**
 * Возвращает количество всех непрочитанных сообщений
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя
 *
 *
 * @return int $get_mes_cnt Количество всех непрочитанных сообщений
 *
 */

function get_all_unread_mes_cnt($con, int $user_id)
{
    $get_mes_cnt_sql = "SELECT COUNT(m.mes_id) AS unread_msg_cnt 
                        FROM messages m 
                        WHERE m.rec_id = $user_id AND m.is_view IS FALSE";
    $get_mes_cnt_res = mysqli_query($con, $get_mes_cnt_sql);
    $get_mes_cnt_array = mysqli_fetch_array($get_mes_cnt_res, MYSQLI_ASSOC);
    $get_mes_cnt = $get_mes_cnt_array['unread_msg_cnt'];
    return $get_mes_cnt;
}


/**
 * Возвращает количество непрочитанных сообщений в диалоге
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param string $dialog_name Идентификатор диалога
 * @param int $current_user_id Идентификатор пользователя у которого открыт диалог
 *
 *
 * @return int $get_msg Количество непрочитанных сообщений в диалоге
 *
 */

function get_dialog_unread_msg_cnt($con, string $dialog_name, int $current_user_id)
{

    $get_msg_sql = "SELECT COUNT(m.mes_id) AS unread_msg_cnt 
                    FROM messages m 
                    WHERE m.is_view is FALSE and (m.dialog_name = '$dialog_name' AND m.rec_id = $current_user_id)";
    $get_msg_res = mysqli_query($con, $get_msg_sql);
    $get_msg_array = mysqli_fetch_array($get_msg_res, MYSQLI_ASSOC);
    $get_msg = $get_msg_array['unread_msg_cnt'];
    return $get_msg;
}

/**
 * При открытии диалога отмечает непрочитанные сообщения прочитанными
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $sen_user_id Идентификатор пользователя отправителя сообщений
 * @param int $rec_user_id Идентификатор пользователя принимающего сообщения
 *
 *
 * @return bool Если данные о сообщение были успешно обновлены в БД - true, иначе false
 */


function read_msg($con, int $sen_user_id, int $rec_user_id)
{

    $read_msg_sql = "UPDATE messages m SET m.is_view = TRUE 
                         WHERE m.is_view IS FALSE 
                         AND (m.sen_id = $sen_user_id AND m.rec_id = $rec_user_id)";
    $res_msg = mysqli_query($con, $read_msg_sql);
    if ($res_msg) {
        return true;
    }

    return false;

}

/**
 * Возвращает массив с обязательными для заполнения полями для формы добавления публикации
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $content_type_id Идентификатор типа публикации
 *
 * @return array $rf Массив с обязательными для заполнения полями для формы добавления публикации
 */

function get_required_fields($con, int $content_type_id)
{
    $rf_sql = "SELECT rf.field_name,rf_rus.field_name_rus FROM required_fields rf 
JOIN rf_rus ON rf.fd_rus_id = rf_rus.rf_rus_id
WHERE content_type_id = $content_type_id";
    $rf_result = mysqli_query($con, $rf_sql);
    $rf = mysqli_fetch_all($rf_result, MYSQLI_ASSOC);
    return $rf;
}

/**
 * Добавляет запись в БД при оформлении подписки на пользователя
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $who_sub_id Идентификатор пользователя КТО осуществляет подписку
 * @param int $to_sub_id Идентификатор пользователя НА КОГО осуществляется подписка
 *
 * @return bool Если данные добавлены - true, иначе false
 */

function add_followes($con, $who_sub_id, $to_sub_id)
{
    $followers_add_sql = "INSERT INTO follow(who_sub_id, to_sub_id) VALUES (?,?)";
    $stmt = db_get_prepare_stmt($con, $followers_add_sql, [$who_sub_id, $to_sub_id]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        return true;
    }

    return false;

}

/**
 * Добавляет пользователя в БД при регистрации
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param array $post Массив с данными, отправленными через форму регистрации
 *
 *
 * @return bool Если данные добавлены - true, иначе false
 */

function add_user($con, array $post)
{
    $add_user_sql = 'INSERT INTO users(reg_date, email, user_name, password, avatar_path,contacts) 
                     VALUES (NOW(),?,?,?,?,?)';
    $stmt = db_get_prepare_stmt($con, $add_user_sql,
        [$post['email'], $post['login'], $post['password_hash'], $post['path'], $post['about_me']]);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        return true;
    }

    return false;

}

/**
 * Удаляет запись из БД при отписки от пользователя
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $who_unsub_id Идентификатор пользователя КТО осуществляет отписку
 * @param int $to_unsub_id Идентификатор пользователя ОТ КОГО осуществляется подписка
 *
 *
 * @return bool Если данные удалены - true, иначе false
 */

function unfollow($con, int $who_unsub_id, int $to_unsub_id)
{
    $followers_delete_sql = "DELETE FROM follow WHERE who_sub_id = $who_unsub_id AND to_sub_id = $to_unsub_id";
    $followers_delete_result = mysqli_query($con, $followers_delete_sql);
    if ($followers_delete_result) {
        return true;
    }

    return false;

}

/**
 * Выполняет поиск по сайту
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param string $search_text Поисковой запрос
 *
 *
 * @return array $posts Возвращает массив с постами согласно поисковому запросу
 */

function search($con, string $search_text)
{

    if (substr($search_text, 0, 1) === '#') {
        //Поиск по хэштегам
        $search_text_explode = explode(' ', $search_text);
        $hashtags = explode('#', $search_text_explode[0]);
        $hashtag = mysqli_real_escape_string($con, $hashtags[1]);
        $search_sql = "SELECT h.hashtag_id,p.*,u.user_name,u.avatar_path,ct.icon_class FROM hashtags h
                       JOIN posts_hashtags ph ON ph.hashtag_id = h.hashtag_id
                       JOIN posts p ON p.post_id = ph.post_id
                       JOIN users u ON u.user_id = p.user_id
                       JOIN content_type ct ON ct.content_type_id = p.content_type_id
                       WHERE h.name = '$hashtag'
                       ORDER BY p.pub_date DESC";
        $result = mysqli_query($con, $search_sql);

    } else {
        $search_sql = "SELECT p.*,u.user_name,u.avatar_path,ct.icon_class 
                   FROM posts p
                   JOIN users u ON u.user_id = p.user_id
                   JOIN content_type ct ON ct.content_type_id = p.content_type_id
                   WHERE MATCH(title, text) AGAINST(?)";
        $stmt = db_get_prepare_stmt($con, $search_sql, [$search_text]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $posts;
}

/**
 * Возвращает строку, которая была указана при поисковом запросе.
 *
 **
 * @param string $search_text Поисковой запрос из GET
 *
 * @return string Возвращает строку, которая была указана при поисковом запросе.
 * Если было указано больше одного хэштега, возвращает первый хэштег и поиск производиться только по нему.
 */

function get_search(string $search_text)
{
    if (substr($search_text, 0, 1) === '#') {
        $search_explode = explode(' ', $search_text);
        $search_text = $search_explode[0];
    }
    return $search_text;
}

/**
 * Возвращает почтовый ящик пользователя
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $user_id Идентификатор пользователя
 *
 * @return string $email Почтовый ящик пользователя
 *
 */

function get_email($con, int $user_id)
{
    $get_email_sql = "SELECT u.email FROM users u WHERE u.user_id = $user_id";
    $get_email_res = mysqli_query($con, $get_email_sql);
    $get_email_array = mysqli_fetch_array($get_email_res, MYSQLI_ASSOC);
    $email = $get_email_array['email'];
    return $email;
}

/**
 * Возвращает заголовок поста
 *
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param int $post_id Идентификатор поста
 *
 * @return string $title Заголовок поста
 *
 */

function get_post_title($con, int $post_id)
{
    $get_title_sql = "SELECT p.title FROM posts p WHERE p.post_id = $post_id";
    $get_title_res = mysqli_query($con, $get_title_sql);
    $get_title_array = mysqli_fetch_array($get_title_res, MYSQLI_ASSOC);
    $title = $get_title_array['title'];
    return $title;
}


/**
 * Проводит валидацию поля "Ссылка на видео" при публикации поста
 *
 **
 * @param string $video_link Ссылка на видео
 *
 *
 * @return array Если поле не проходит валидацию, в ином случае null
 *
 */

function check_video_link_error(string $video_link)
{
    if (!filter_var($video_link, FILTER_VALIDATE_URL)) {
        return $errors = [
            'field_name_rus' => 'Ссылка youtube',
            'error_title' => 'Неверно указана ссылка на видео',
            'error_desc' => 'Просьба указать ссылку на видео в виде: "https://www.youtube.com/"'
        ];
    } elseif (!check_youtube_url($video_link)) {
        return $errors = [
            'field_name_rus' => 'Ссылка youtube',
            'error_title' => 'Неверно указана ссылка на видео',
            'error_desc' => 'Просьба указать ссылку на существующее видео на youtube'
        ];
    }
    return null;
}

/**
 * Проводит проверку формата изображения, загруженного через поле "Выбрать фото" при публикации поста
 **
 * @param string $file_name Имя файла, полученное через $_FILES['tmp_name']
 *
 *
 * @return array Если тип файла не подходит, то массив с описанием ошибки, в ином случае null.
 *
 */


function check_image_type(string $file_name)
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $file_name);

    if ($file_type !== 'image/gif' && $file_type !== 'image/jpeg' && $file_type !== 'image/png') {
        return $error = [
            'field_name_rus' => 'Выбрать фото',
            'error_title' => 'Формат загружемого изображения должен быть : png, jpeg, gif'
        ];
    }

    return null;
}

/**
 * Проверяет тип изображения, загружаемого по ссылке при публикации поста
 **
 * @param string $file_name Имя файла, полученное через file_get_contents.
 *
 *
 * @return bool Если тип файла подходит, в ином случае false
 *
 */

function check_image_type_link(string $file_name)
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_buffer($finfo, $file_name);
    if ($file_type !== 'image/gif' && $file_type !== 'image/jpeg' && $file_type !== 'image/png') {
        return false;
    }
    return true;
}


/**
 * Функция проводит валидацию поля "Ссылка на изображение"
 **
 * @param string $image_link Ссылка на изображение
 *
 * @return array Если валидация не прошла, то возвращается массив с описание ошибки, в ином случае false
 *
 */

function check_image_link(string $image_link)
{
    if (!filter_var($image_link, FILTER_VALIDATE_URL)) {
        return $errors = [
            'field_name_rus' => 'Ссылка из интернета',
            'error_title' => 'Неверно указана ссылка на изображение',
            'error_desc' => 'Просьба указать ссылку на изображение в виде: "https://site.com"'
        ];
    } elseif (!file_get_contents($image_link)) {
        return $errors = [
            'field_name_rus' => 'Ссылка из интернета',
            'error_title' => 'Не удалось загрузить изображение',
            'error_desc' => 'При загрузке изображения возникла ошибка'
        ];
    } elseif (!check_image_type_link(file_get_contents($image_link))) {
        return $errors = [
            'field_name_rus' => 'Ссылка из интернета',
            'error_title' => 'Недопустимый формат изображения',
            'error_desc' => 'Формат загружемого изображения должен быть : png, jpeg, gif'
        ];
    }
    return null;
}

/**
 * Совершает валидацию поля email при регистрации
 **
 * @param mysqli $con Ресурс соединения с БД
 * @param string $email Почтовый ящик
 *
 * @return array Массив с описанием ошибки, если валидация не пройдена, в ином случае null
 *
 */


function validation_email($con, $email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $errors = [
            'field_name_rus' => 'Электронная почта',
            'error_title' => 'Недействительный mail',
            'error_desc' => 'Вы указали недействительный почтовый ящик'
        ];
    } elseif (is_email($con, $email)) {
        return $errors = [
            'field_name_rus' => 'Электронная почта',
            'error_title' => 'Почтовый ящик уже существует',
            'error_desc' => 'Вы указали уже существующий почтовый ящик'
        ];
    }
    return null;
}


/**
 * Отправляет почтовое сообщение
 **
 * @param $mailer Главный объект библиотеки SwiftMailer
 * @param string $msg_content Текст сообщения
 * @param string $email Почтовый адрес принимающего
 * @param string $subject Тема письма
 *
 * @return bool Если сообщение отправлено - true, в ином случае false.
 *
 */

function send_notification($mailer, string $msg_content, string $email, string $subject)
{
    $message = new Swift_Message();
    $message->setSubject($subject);
    $message->setFrom(['keks@phpdemo.ru' => 'Readme']);
    $message->setBcc($email);


    $message->setBody($msg_content, 'text/html');
    $result = $mailer->send($message);

    if (!$result) {
        return false;
    }
    return true;
}

/**
 * Возвращает название сортировки
 **
 * @param string $current_get_link Строка указанная в GET запросе с ключом 'sorting'
 *
 *
 * @return string $current_sorting_name Возвращает название сортировки, указанное в GET запросе
 *
 */

function get_sorting_name($current_get_link)
{
    $current_link_explode = explode('_', $current_get_link);
    $current_sorting_name = array_shift($current_link_explode);
    return $current_sorting_name;

}

/**
 * Возвращает тип сортировки по возврастанию или по убыванию, указаный в GET запросе
 **
 * @param string $current_get_link Строка указанная в GET запросе с ключом 'sorting'
 *
 * @return string $current_sorting_type Возвращает тип сортировки по возврастанию или по убыванию, указаный в GET запросе
 *
 */

function get_sorting_type($current_get_link)
{
    $current_link_explode = explode('_', $current_get_link);
    $current_sorting_type = array_pop($current_link_explode);
    return $current_sorting_type;
}







