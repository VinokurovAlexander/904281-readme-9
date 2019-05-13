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

    //Валидация формы
    $errors = [];

    //Получаем обязательные поля для формы
    // rf = required fields
    $rf_sql = "SELECT rf.field_name,rf_rus.field_name_rus FROM required_fields rf 
    JOIN rf_rus ON rf.fd_rus_id = rf_rus.rf_rus_id
    WHERE content_type_id = $get_ct_id";
    $rf_result = mysqli_query($con, $rf_sql);
    $rf = mysqli_fetch_all($rf_result, MYSQLI_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $post = $_POST;

        foreach ($rf as $field) {
            $r_fn = $field['field_name']; // r_fn = required field name
            $r_fn_rus = $field['field_name_rus'];

            //Проверяем заполнены ли поля
            if (empty($_POST[$r_fn])) {
                $errors[$r_fn] = [
                'field_name_rus' => $r_fn_rus,
                'error_title' => 'Заполните это поле',
                'error_desc' => 'Данное поле должно быть обязательно заполнено'
            ];
            }
        }

        //Проверяем корректное заполнение полей

        //Проверяем поля для добавления картинок
        if ($_POST['photo-link'] or $_FILES['userpic-file-photo']['name']) {
            unset($errors['photo-link']);
            unset($errors['userpic-file-photo']);
        }

        elseif ($_POST['photo-link'] and $_FILES['userpic-file-photo']['name']) {
            // Условие для игнорирования поля 'photo-link'
        }

        if (!empty($_FILES['userpic-file-photo']['name'])) {
            $tmp_name = $_FILES['userpic-file-photo']['tmp_name'];
            $path = $_FILES['userpic-file-photo']['name'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($finfo, $tmp_name);

            if ($file_type !== 'image/gif' and $file_type !== 'image/jpeg' and $file_type !== 'image/png') {
                $errors['userpic-file-photo'] = [
                    'field_name_rus' => 'Выбрать фото',
                    'error_title' => 'Формат загружемого изображения должен быть : png, jpeg, gif'
                ];
            }
            else {
                    move_uploaded_file($tmp_name, 'uploads/' . $path);

                    $post_add_sql = 'INSERT INTO posts (pub_date, title, user_id, img, content_type_id)
                    VALUES (NOW(),?,1,?,3)';
                    $stmt = db_get_prepare_stmt($con,$post_add_sql,[$post['photo-heading'], $path]);
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
                    }
            }

        }

    } // Заканчивается if ($_SERVER['REQUEST_METHOD'] == 'POST')

    if (!empty($post_add_sql_error)) {
        $post_add = $post_add_sql_error;
    }

    else {

    $post_add = include_template('add_form_temp.php', [
        'get_ct_id' => $get_ct_id,
        'errors' => $errors
    ]);

    }


    $page_content = include_template('add_post_temp.php',[
        'ct_all_rows' => $ct_all_rows,
        'get_ct_id' => $get_ct_id,
        'post_add' => $post_add
    ]);
}


print($page_content);

// Вывод результатов

print("<pre>");
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
print("</pre>");



