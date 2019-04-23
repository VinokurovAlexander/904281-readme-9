<?php

require_once('helpers.php');

$is_auth = rand(0, 1);
$user_name = 'Александр'; // укажите здесь ваше имя

$posts = [
        [
            'headline' => 'Цитата',
            'type' => 'post-quote',
            'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
            'username' => 'Лариса',
            'avatar' => 'userpic-larisa-small.jpg'
        ],
        [
            'headline' => 'Игра престолов',
            'type' => 'post-text',
            'content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
            'username' => 'Владик',
            'avatar' => 'userpic.jpg'
        ],
        [
            'headline' => 'Наконец, обработал фотки!',
            'type' => 'post-photo',
            'content' => 'rock-medium.jpg',
            'username' => 'Владик',
            'avatar' => 'userpic.jpg'
        ],
        [
            'headline' => 'Моя мечта',
            'type' => 'post-photo',
            'content' => 'coast-medium.jpg',
            'username' => 'Лариса',
            'avatar' => 'userpic-larisa-small.jpg'
        ],
        [
            'headline' => 'Лучшие курсы',
            'type' => 'post-link',
            'content' => 'www.htmlacademy.ru',
            'username' => 'Владик',
            'avatar' => 'userpic.jpg'
        ],
];

function cut_text ($text) {
    $num_letters = 40;
    $explode_text = explode(" ",$text);
    $i = 0;
    $sum = 0;
    $new_text = [];
    foreach ($explode_text as $v) {
        if ($sum < $num_letters) {
            $len = mb_strlen($v);
            $sum = $sum + $len;
            array_push($new_text,$v);
            $i++;
        }
    }
    if ($sum > $num_letters) {
        array_pop($new_text);
        $final_text = implode(" ",$new_text) .'...' . "<br>" . "<a class=\"post-text__more-link\" href=\"#\">Читать далее</a>";
    }
    else {
        $final_text = implode(" ",$new_text);
    }
    return $final_text;
}

$page_content = include_template('index.php', ['posts' => $posts]);
$layout_content = include_template('layout.php', ['content' => $page_content ,'title' => 'Тест', 'user_name' => 'Александр']);

print($layout_content);

?>

