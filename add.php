<?php
require_once ('helpers.php');
require_once ('sql_connect.php');


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

//    Получаем текущий ct по id ct из GET запроса
    $ct_sql = "SELECT content_type FROM content_type WHERE content_type_id = $get_ct_id";
    $ct_result = mysqli_query($con, $ct_sql);
    $ct_rows = mysqli_fetch_all($ct_result, MYSQLI_ASSOC);


//    Отображаем необходимый шаблон для добавления поста в зависимости от ct

    if ($get_ct_id == '1') {
        $post_add = include_template('add_post_text_temp.php',[]);
    }

    elseif ($get_ct_id == '2') {
        $post_add = include_template('add_post_quote_temp.php',[]);
    }

    elseif ($get_ct_id == '3') {
        $post_add = include_template('add_post_photo_temp.php',[]);
    }

    elseif ($get_ct_id == '4') {
        $post_add = include_template('add_post_video_temp.php',[]);
    }

    elseif ($get_ct_id == '5') {
        $post_add = include_template('add_post_link_temp.php',[]);
    }

    $page_content = include_template('add_post_temp.php',[
        'ct_all_rows' => $ct_all_rows,
        'get_ct_id' => $get_ct_id,
        'post_add' => $post_add
    ]);

}

print($page_content);


