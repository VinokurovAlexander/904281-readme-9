<?php

require_once('sql_connect.php');
require_once('helpers.php');
require_once('my_functions.php');

my_session_start();

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $current_post_id = $_GET['post_id'];

    //Проверяем наличие поста с указанным id
    if (is_post($con, $current_post_id)) {

        //Проверяем наличие лайка
        if (is_like($con, $post_id)) {

            //Лайк есть, нужно его удалить
            delete_like($con, $post_id);
        } else {

            //Добавляем данные в таблицу
            add_like($con, $post_id);
        }
    } else {
        show_error($con, 'Не существует поста с таким id');
    }
} else {
    show_error($con, 'Нет post_id в GET запросе');
}
