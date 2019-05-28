<?php
require_once('helpers.php');
require_once('my_functions.php');
require_once('sql_connect.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

//Дата и время публикации поста

date_default_timezone_set("Europe/Moscow");


//Постраничный вывод
$cur_page = $_GET['page'] ?? 1;
$page_items = 6;

$posts_count_sql = "SELECT count(*) AS posts_count FROM posts";
$posts_count_result = mysqli_query($con,$posts_count_sql);
$posts_count = mysqli_fetch_assoc($posts_count_result)['posts_count'];

$pages_count = ceil($posts_count / $page_items);
$offset = ($cur_page - 1) * $page_items;
//Заполняем массив номерами всех страниц
$pages = range(1, $pages_count);



// Выгружаем список типов контента
$con_type = "SELECT content_type_id,content_type,icon_class FROM content_type";
$con_type_res = mysqli_query($con,$con_type);
$con_type_rows = mysqli_fetch_all($con_type_res, MYSQLI_ASSOC);

// Выгружаем список постов
$posts_sql = "SELECT p.*,u.user_name,ct.content_type,ct.icon_class,u.avatar_path,COUNT(l.like_id) AS likes_count FROM posts p
INNER JOIN users u ON p.user_id  = u.user_id
INNER JOIN content_type ct ON p.content_type_id = ct.content_type_id
LEFT JOIN likes l ON p.post_id = l.post_id
GROUP BY p.post_id
ORDER BY view_count DESC";


// Определяем если ли в параметрах запроса id контента
$all_content = ""; // класс для отображения всего контента
$get_con_id= "";
if (isset($_GET['content_type_id'])) {
    $get_con_id = $_GET['content_type_id'];
    $posts_sql = "SELECT p.*,u.user_name,ct.content_type,ct.icon_class,u.avatar_path,COUNT(l.like_id) AS likes_count FROM posts p
              INNER JOIN users u ON p.user_id  = u.user_id
              INNER JOIN content_type ct ON p.content_type_id = ct.content_type_id
              LEFT JOIN likes l ON p.post_id = l.post_id
              WHERE p.content_type_id = $get_con_id
              GROUP BY p.post_id
              ORDER BY view_count DESC";

} else {
    $all_content = "filters__button--active";
};


$posts_res = mysqli_query($con,$posts_sql);
$posts_rows = mysqli_fetch_all($posts_res, MYSQLI_ASSOC);

//$content_type_id = null;
//$posts_sql = "SELECT p.*,u.user_name,ct.content_type,ct.icon_class,u.avatar_path,COUNT(l.like_id) AS likes_count FROM posts p
//    INNER JOIN users u ON p.user_id  = u.user_id
//    INNER JOIN content_type ct ON p.content_type_id = ct.content_type_id
//    LEFT JOIN likes l ON p.post_id = l.post_id
//    IF !epmty($content_type_id) THEN WHERE p.content_type_id = $content_type_id
//    GROUP BY p.post_id
//    ORDER BY view_count DESC";
//$posts_res = mysqli_query($con,$posts_sql);
//$posts_rows = mysqli_fetch_all($posts_res, MYSQLI_ASSOC);


//Загружаем шаблоны
$page_content = include_template('popular_template.php', [
    'posts_rows' => $posts_rows,
    'con_type_rows' => $con_type_rows,
    'get_con_id' => $get_con_id,
    'all_content' => $all_content,
    'con' => $con
]);



$layout_content = include_template('layout.php', [
    'content' => $page_content ,
    'title' => 'Популярный контент',
    'user_name' => $_SESSION['user']['user_name'],
]);

//Выводим результат
print($layout_content);

