USE readme_db;

-- ДОбавляем список типов контента для поста

INSERT INTO content_type (content_type, icon_class)
VALUES ('Текст','text'), ('Цитата','quote'), ('Картинка','photo'), ('Видео','video'),('Ссылка','link');

-- Добавляем пару пользователей

INSERT INTO users (reg_date, email, user_name, password, avatar_path, contacts)
VALUES ('2018-03-12 15:43:01', 'larisa@mail.ru', 'Лариса', '123', '/img/userpic-larisa.jpg', 'Телефон: 333-444-555'),
       ('2018-07-22 01:10:55', 'vladik@mail.ru', 'Владик', '123', '/img/userpic-medium.jpg', 'Телефон: 111-222-666');

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
        '2' , '/img/rock.jpg', '', '', '101', '3'),
       ('2019-03-03 12:22:11',
        'Моя мечта',
        '',
        '1' , 'img/coast.jpg', '', '', '5', '3'),
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

-- Добавляем обязательные поля на русском

INSERT INTO rf_rus (field_name_rus) VALUES ('Заголовок'),('Текст поста'),('Текст цитаты'), ('Автор'),
                                           ('Ссылка из интернета'), ('Ссылка youtube'), ('Ссылка'), ('Выбрать фото'),
                                           ('Теги');

-- ДОбавляем обязательные поля для форм публикации поста

INSERT INTO required_fields (field_name, content_type_id,fd_rus_id )
VALUES ('text-heading', '1','1'), ('post-text','1','2'), ('quote-heading','2','1'), ('quote-text','2','3'), ('quote-author','2','4'),
       ('photo-heading','3','1'),( 'photo-link','3','5'),('video-heading','4','1'),( 'video-link','4','6'),('link-heading','5','1'),
       ('post-link','5','7'),('post-tags','1','9'),('quote-tags','2','9'),('photo-tags','3','9'),('video-tags','4','9'),('link-tags','5','9');

-- Добавляем хэштеги

INSERT INTO hashtags (name)
VALUES ('quote'),('text'),('photo'),('link');

-- Добавляем хэштеги к постам

INSERT INTO posts_hashtags(post_id, hashtag_id)
VALUES ('1','1'),('2','2'),('3','3'),('4','3'),('5','4');





