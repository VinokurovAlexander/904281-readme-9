USE readme_db;

-- ДОбавляем список типов контента для поста

INSERT INTO content_type (content_type, icon_class)
VALUES ('Текст','text'), ('Цитата','quote'), ('Картинка','photo'), ('Видео','video'),('Ссылка','link');

-- Добавляем пару пользователей

INSERT INTO users (reg_date, email, user_name, password, avatar_path, contacts)
VALUES ('2018-03-12 15:43:01', 'larisa@mail.ru', 'Лариса', 'qwerty', 'userpic-larisa-small.jpg', 'Телефон: 333-444-555'),
       ('2018-07-22 01:10:55', 'vladik@mail.ru', 'Владик', 'password', 'userpic.jpg', 'Телефон: 111-222-666');

-- Существующие посты

INSERT INTO posts (pub_date, title, text, user_id, img, video, link, view_count, content_type_id)
VALUES ('2019-01-01 01:02:13',
        'Цитата',
        'Мы в жизни любим только раз, а после ищем лишь похожих', '1' , '',  '', '', '50', '2'),
       ('2019-02-13 20:12:45',
        'Игра престолов',
        'Не могу дождаться начала финального сезона своего любимого сериала!',
        '2' , '', '', '', '35', '1'),
       ('2019-02-18 03:23:02',
        'Наконец, обработал фотки!',
        '',
        '2' , 'rock-medium.jpg', '', '', '101', '3'),
       ('2019-03-03 12:22:11',
        'Моя мечта',
        'Не могу дождаться начала финального сезона своего любимого сериала!',
        '1' , 'coast-medium.jpg', '', '', '5', '3'),
       ('2019-03-23 00:34:15',
        'Лучшие курсы',
        '',
        '2' , '', '', 'www.htmlacademy.ru', '17', '5');

-- Добавляем комментарии

INSERT INTO comments (pub_date, content, user_id, post_id)
VALUES  ('2019-04-28 23:00:03','Класс!','2','1'), ('2019-04-25 13:06:22','Супер!','1','2');

-- Получить список постов с сортировкой по популярности и вместе с именами авторов и типом контента

SELECT p.*,u.user_name,ct.content_type FROM posts p
INNER JOIN users u ON p.user_id  = u.user_id
INNER JOIN content_type ct ON p.content_type_id = ct.content_type_id
ORDER BY view_count DESC ;

-- Получить список постов для конкретного пользователя;
SELECT u.user_name, p.* FROM users u
INNER JOIN posts p ON u.user_id = p.user_id
WHERE u.user_name = 'Владик';

-- Получить список комментариев для одного поста, в комментариях должен быть логин пользователя;

SELECT c.*, u.user_name FROM comments c
INNER JOIN users u ON c.user_id = u.user_id
WHERE c.post_id = 1;

-- Добавить лайк к посту;

INSERT INTO likes (who_like_id, post_id)
VALUES (1,1);

-- Подписаться на пользователя

INSERT INTO follow (who_sub_id, to_sub_id)
VALUES (1,2);


