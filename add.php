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

    //Получаем текущий ct по id ct из GET запроса
    $ct_sql = "SELECT content_type FROM content_type WHERE content_type_id = $get_ct_id";
    $ct_result = mysqli_query($con, $ct_sql);
    $ct_rows = mysqli_fetch_all($ct_result, MYSQLI_ASSOC);


    //Проверка заполнения полей
    $errors = [];
    $required_fields_all = [
        1 => ['text-heading','post-text'],
        2 => ['quote-heading','quote-text'],
        3 => ['photo-heading','photo-link'],
        4 => ['video-heading','video-link'],
        5 => ['link-heading', 'post-link']
    ];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $required_fields = $required_fields_all[$get_ct_id];

        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = 'Поле не заполнено';
            }
        }
    }


    // Подключаем шаблоны
    $post_add = include_template('add_form_temp.php', [
        'get_ct_id' => $get_ct_id,
        'errors' => $errors
    ]);

    $page_content = include_template('add_post_temp.php',[
        'ct_all_rows' => $ct_all_rows,
        'get_ct_id' => $get_ct_id,
        'post_add' => $post_add,
    ]);

}

print($page_content);


// Вывод результатов
print("Полученные данные: ");
print_r($_POST);
print("<br>");
print("Полученные файлы: ");
print_r($_FILES);
print("<br>");
print("Результаты проверки заполнения данных: ");
print_r($errors);
print("<br>");
print("Итоговый массив с данными");
print_r($ct_rows);



