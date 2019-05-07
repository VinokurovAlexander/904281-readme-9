<?php
require_once ('helpers.php');
require_once ('sql_connect.php');


if ($con == false) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);
}

else {

    if (isset($_GET['content_type_id'])) {
        $get_content_type_id = intval($_GET['content_type_id']);
    }

    $content_type_sql = "SELECT * FROM content_type ct";
    $content_type_result = mysqli_query($con, $content_type_sql);
    $content_type_rows = mysqli_fetch_all($content_type_result, MYSQLI_ASSOC);

    $page_content = include_template('add_post_temp.php',  [
        'content_type_rows' => $content_type_rows,
        'get_content_type_id' => $get_content_type_id
    ]);

    print($page_content);
}

?>
