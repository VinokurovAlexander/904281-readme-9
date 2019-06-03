<?php
require_once('my_functions.php');

if ($con = mysqli_connect("localhost", "root", "", "readme_db")) {
    mysqli_set_charset($con, "utf8");
} else {
    show_sql_error($con);
}
