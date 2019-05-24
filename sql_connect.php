<?php

if ($con = mysqli_connect("localhost", "root", "", "readme_db")) {
    mysqli_set_charset($con, "utf8");
}

else {
    $sql_error = include_template('error.php', [
        'error' => mysqli_error($con)
    ]);
    print($sql_error);
}
