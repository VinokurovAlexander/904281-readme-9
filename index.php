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

//Функция, обрезающая текст в постах
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


//Дата и время публикации поста

date_default_timezone_set("Europe/Moscow");

// Функция, отображающая ОТНОСИТЕЛЬНОЕ время публикации поста
function rel_post_time ($k) {
    $cur_date = time(); // текущее время
    $gen_date = generate_random_date($k); //время поста
    $post_date= strtotime($gen_date);  // метка для времени поста
    $diff = floor($cur_date - $post_date); //разница между временем поста и текущим временем в секундах
    if ($diff < 3600) {
        $diff = floor($diff / 60);
        $decl = get_noun_plural_form($diff, 'минута', 'минуты','минут'); //узнаем необходимое склонение
    }
    elseif ($diff >= 60 and $diff < 86400) {
        $diff = floor($diff / 3600);
        $decl = get_noun_plural_form($diff, 'час', 'часа','часов');
    }
    elseif ($diff >= 86400 and $diff < 604800) {
        $diff = floor($diff / 86400);
        $decl = get_noun_plural_form($diff, 'день', 'дня','дней');
    }
    elseif ($diff >= 604800 and $diff < 3024000) {
        $diff = floor($diff / 604800);
        $decl = get_noun_plural_form($diff, 'неделя', 'недели','недель');
    }
    elseif ($diff >= 3024000) {
        $diff = floor($diff / 2592000);
        $decl = get_noun_plural_form($diff, 'месяц', 'месяца','месяцев');
    }

    return("$diff $decl назад <br>");
}

// Время поста в формате дд.мм.гггг чч:мм
function post_time_title ($k) {
    $post_date = generate_random_date($k);
    $ts_post_date = strtotime($post_date);
    $post_date_title = date('j-m-Y G:i', $ts_post_date);
    return $post_date_title;
}

//Загружаем шаблоны
$page_content = include_template('index.php', [
    'posts' => $posts
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content ,
    'title' => 'Тест',
    'user_name' => 'Александр',
    'is_auth' => $is_auth,
]);

//Выводим результат
print($layout_content);

?>

