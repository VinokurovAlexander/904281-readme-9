<?php

require_once('helpers.php');

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /");
    exit();
}

$page_content = include_template('feed_template.php', []);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'user_name' => $_SESSION['user']['user_name'],
    'is_auth' => 1,
    'title' => 'Моя лента'
]);

print($layout_content);