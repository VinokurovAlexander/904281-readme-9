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

    //Получаем обязательные поля для формы
    $required_fields_sql = "SELECT rf.field_name,rf_rus.field_name_rus FROM required_fields rf 
    JOIN rf_rus ON rf.fd_rus_id = rf_rus.rf_rus_id
    WHERE content_type_id = $get_ct_id";
    $required_fields_result = mysqli_query($con, $required_fields_sql);
    $required_fields = mysqli_fetch_all($required_fields_result, MYSQLI_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        foreach ($required_fields as $field => $value) {
            $field_name = $value['field_name'];
            $field_name_rus = $value['field_name_rus'];

//            print("<pre>");
//            print("Прохождение по массиву: ");
//            print_r($required_fields);
//            print("<br>");
//
//            print('$field: ' . $field);
//            print("<br>");
//
//            print('$value: ');
//            print_r($value);
//            print("<br>");
//
//            print('$field_name: ' . $field_name . '<br>');
//            print('$field_name_rus: ' . $field_name_rus . '<br>');
//
//            print("</pre>");

            if (empty($_POST[$field])) {
                $errors[$field_name] = [
                'field_name_rus' => $field_name_rus,
                'error_text' => 'Заполните это поле'
            ];
            }

//            print("<pre>");
//
//            print('Массив с ошибками: ');
//            print_r($errors);
//
//            print("</pre>");
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
        'post_add' => $post_add
    ]);

}

print($page_content);

// Вывод результатов
//print("<pre>");
//
//print("Полученные данные: ");
//print_r($_POST);
//print("<br>");
//
//print("Полученные файлы: ");
//print_r($_FILES);
//print("<br>");
//
//print("Результаты проверки заполнения данных: ");
//print_r($errors);
//print("<br>");
//
//print("Итоговый массив с данными");
//print_r($ct_rows);
//print("<br>");
//
//print("Обязательные поля: ");
//print_r($required_fields);
//print("<br>");
//
//print("</pre>");



