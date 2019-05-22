<?php

require_once('helpers.php');
require_once('sql_connect.php');
require_once('my_functions.php');


session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

print('<pre>');
print_r($_SESSION);
print('</pre>');



$page_content = include_template('profile_template.php',[
    'time_on_site' => '10 лет'
]);

$layout_content = include_template('layout.php',[
    'content' => $page_content ,
    'title' => 'Мой профиль',


]);

print($layout_content);





