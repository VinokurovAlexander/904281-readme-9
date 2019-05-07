<?php
require_once ('helpers.php');
require_once ('sql_connect.php');


if ($con == false) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
}

else {
    $content_type_sql = "SELECT content_type, icon_class FROM content_type ct";
    $content_type_result = mysqli_query($con, $content_type_sql);
    $content_type_rows = mysqli_fetch_all($content_type_result, MYSQLI_ASSOC);

    $page_content = include_template('add_post_temp.php',  [
        'content_type_rows' => $content_type_rows
    ]);

    print($page_content);
}

?>
