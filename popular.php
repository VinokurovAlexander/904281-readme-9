<?php
require_once('helpers.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

//Функция, обрезающая текст в постах
function cut_text ($text) {
    $num_letters = 40;
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


//Дата и время публикации поста

date_default_timezone_set("Europe/Moscow");

// Функция, отображающая ОТНОСИТЕЛЬНОЕ время публикации поста
function rel_post_time ($pub_date) {
    $cur_date = time(); // текущее время
//    $gen_date = generate_random_date($k); //время поста
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

    return("$diff $decl назад <br>");
}

// Время поста в формате дд.мм.гггг чч:мм
function post_time_title ($post_date) {
//    $post_date = generate_random_date($k);
    $ts_post_date = strtotime($post_date);
    $post_date_title = date('j-m-Y G:i', $ts_post_date);
    return $post_date_title;
}

// Работаем с БД
$con = mysqli_connect("localhost", "root", "", "readme_db");
mysqli_set_charset($con, "utf8");

if ($con == false) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
}

else {
// Выгружаем список типов контента
$con_type = "SELECT content_type_id,content_type,icon_class FROM content_type";
$con_type_res = mysqli_query($con,$con_type);
$con_type_rows = mysqli_fetch_all($con_type_res, MYSQLI_ASSOC);

// Выгружаем список постов
$posts = "SELECT p.*,u.user_name,ct.content_type,ct.icon_class,u.avatar_path FROM posts p
INNER JOIN users u ON p.user_id  = u.user_id
INNER JOIN content_type ct ON p.content_type_id = ct.content_type_id
ORDER BY view_count DESC";


// Определяем если ли в параметрах запроса id контента
    $all_content = ""; // класс для оторажения всего контента
    $get_con_id= "";
    if (isset($_GET['content_type_id'])) {
        $get_con_id = $_GET['content_type_id'];
        $posts = "SELECT p.*,u.user_name,ct.content_type,ct.icon_class,u.avatar_path FROM posts p
                  INNER JOIN users u ON p.user_id  = u.user_id
                  INNER JOIN content_type ct ON p.content_type_id = ct.content_type_id
                  WHERE p.content_type_id = $get_con_id
                  ORDER BY view_count DESC";

    } else {
        $all_content = "filters__button--active";
    };

$posts_res = mysqli_query($con,$posts);
$posts_rows = mysqli_fetch_all($posts_res, MYSQLI_ASSOC);



//Загружаем шаблоны
$page_content = include_template('popular_template.php', [
    'posts_rows' => $posts_rows,
    'con_type_rows' => $con_type_rows,
    'get_con_id' => $get_con_id,
    'all_content' => $all_content
]);

}

$layout_content = include_template('layout.php', [
    'content' => $page_content ,
    'title' => 'Популярный контент',
    'user_name' => $_SESSION['user']['user_name'],
]);

//Выводим результат
print($layout_content);



