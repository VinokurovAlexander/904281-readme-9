<main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--default">
        <div class="profile__user-wrapper">
            <div class="profile__user user container">
                <div class="profile__user-info user__info">
                    <div class="profile__avatar user__avatar">
                        <img class="profile__picture user__picture" src="<?= $user['avatar_path']; ?>"
                             alt="Аватар пользователя">
                    </div>
                    <div class="profile__name-wrapper user__name-wrapper">
                        <span class="profile__name user__name"><?= htmlspecialchars($user['user_name']); ?></span>
                        <time class="profile__user-time user__time"
                              datetime="<?= $user['reg_date'] ?>"><?= rel_time($user['reg_date']) ?> на сайте
                        </time>
                    </div>
                </div>
                <div class="profile__rating user__rating">
                    <p class="profile__rating-item user__rating-item user__rating-item--publications">
                        <span class="user__rating-amount"><?= get_user_posts_count($con, $user['user_id']) ?></span>
                        <span class="profile__rating-text user__rating-text">публикаций</span>
                    </p>
                    <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="user__rating-amount"><?= get_user_followers($con, $user['user_id']) ?></span>
                        <span class="profile__rating-text user__rating-text">подписчиков</span>
                    </p>
                </div>
                <div class="profile__user-buttons user__buttons">
                    <?php if ($user['user_id'] !== $_SESSION['user']['user_id']) : ?>
                        <?php if (is_follow($con, $user['user_id'])) : ?>
                            <a class="profile__user-button user__button user__button--subscription button button--main"
                               href="/unfollow.php/?user_id=<?= $user['user_id'] ?>">Отписаться</a>
                            <a class="profile__user-button user__button user__button--writing button button--green"
                               href="/messages.php/?user_id=<?= $user['user_id'] ?>">Сообщение</a>
                        <?php else : ?>
                            <a class="profile__user-button user__button user__button--subscription button button--main"
                               href="/follow.php/?user_id=<?= $user['user_id'] ?>">Подписаться</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="profile__tabs-wrapper tabs">
            <div class="container">
                <div class="profile__tabs filters">
                    <b class="profile__tabs-caption filters__caption">Показать:</b>
                    <ul class="profile__tabs-list filters__list tabs__list">
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item tabs__item--active button <? if ($_GET['content'] == 'posts') {
                                echo 'filters__button--active';
                            } ?>" href="/profile.php/?user_id=<?= $_GET['user_id'] ?>&content=posts">Посты</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button <? if ($_GET['content'] == 'likes') {
                                echo 'filters__button--active';
                            } ?>" href="/profile.php/?user_id=<?= $_GET['user_id'] ?>&content=likes">Лайки</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button <? if ($_GET['content'] == 'followers') {
                                echo 'filters__button--active';
                            } ?>" href="/profile.php/?user_id=<?= $_GET['user_id'] ?>&content=followers">Подписки</a>
                        </li>
                    </ul>
                </div>
                <div class="profile__tab-content">
                    <section class="profile__posts tabs__content <? if ($_GET['content'] == 'posts') {
                        echo 'tabs__content--active';
                    } ?>">
                        <?php foreach ($posts as $post): ?>
                            <article class="profile__post post post-<?= $post['icon_class']; ?>">
                                <header class="post__header">
                                    <?php if ($post['repost_id'] != null) : ?>
                                        <div class="post__author">
                                            <a class="post__author-link"
                                               href="/profile.php/?user_id=<?= $post['author_id'] ?>" title="Автор">
                                                <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                                                    <img class="post__author-avatar" src="<?= $post['author_avatar'] ?>"
                                                         alt="Аватар пользователя">
                                                </div>
                                                <div class="post__info">
                                                    <b class="post__author-name">Репост: <?= htmlspecialchars($post['author_name']) ?></b>
                                                    <time class="post__time"
                                                          datetime="<?= $post['pub_date'] ?>"><?= rel_time($post['pub_date']) ?>
                                                        назад
                                                    </time>
                                                </div>
                                            </a>
                                        </div>
                                    <? else : ?>
                                        <h2>
                                            <a href="/post.php/?post_id=<?= $post['post_id']; ?>">
                                                <?= htmlspecialchars($post['title']); ?>
                                            </a>
                                        </h2>
                                    <? endif; ?>
                                </header>
                                <div class="post__main">
                                    <?php if ($post['content_type_id'] == 1): ?>
                                        <p>
                                            <?= (cut_text(htmlspecialchars($post['text']), 300, $post['post_id'])); ?>
                                        </p>
                                    <?php elseif ($post['content_type_id'] == 2): ?>
                                        <blockquote>
                                            <p>
                                                <?= htmlspecialchars($post['text']); ?>
                                            </p>
                                            <cite><?= htmlspecialchars($post['quote_author']) ?></cite>
                                        </blockquote>
                                    <?php elseif ($post['content_type_id'] == 3): ?>
                                        <div class="post-photo__image-wrapper">
                                            <img src="<?= ($post['img']); ?>" alt="Фото от пользователя" width="760"
                                                 height="396">
                                        </div>
                                    <?php elseif ($post['content_type_id'] == 4): ?>
                                        <iframe width="760px" height="400px" src="<?= $post['video'] ?>"
                                                frameborder="0"></iframe>
                                    <?php elseif ($post['content_type_id'] == 5): ?>
                                        <div class="post-link__wrapper">
                                            <a class="post-link__external" href="<?= $post['link']; ?>"
                                               title="Перейти по ссылке">
                                                <div class="post-link__info-wrapper">
                                                    <div class="post-link__icon-wrapper">
                                                        <img src="../img/logo-vita.jpg" alt="Иконка">
                                                    </div>
                                                    <div class="post-link__info">
                                                        <h3><?= htmlspecialchars($post['title']); ?></h3>
                                                    </div>
                                                </div>
                                                <span><?= htmlspecialchars($post['link']); ?></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <footer class="post__footer">
                                    <div class="post__indicators">
                                        <div class="post__buttons">
                                            <a class="post__indicator post__indicator--likes button"
                                               href="/like.php/?post_id=<?= $post['post_id'] ?>" title="Лайк">
                                                <?php if (is_like($con, $post['post_id'])) : ?>
                                                    <svg class="post__indicator-icon post__indicator-icon--like-active"
                                                         width="20" height="17">
                                                        <use xlink:href="#icon-heart-active"></use>
                                                    </svg>
                                                <?php else : ?>
                                                    <svg class="post__indicator-icon" width="20" height="17">
                                                        <use xlink:href="#icon-heart"></use>
                                                    </svg>
                                                <? endif; ?>
                                                <span><?= get_post_likes_count($con, $post['post_id']); ?></span>
                                                <span class="visually-hidden">количество лайков</span>
                                            </a>
                                            <a class="post__indicator post__indicator--comments button" href="#"
                                               title="Комментарии">
                                                <svg class="post__indicator-icon" width="19" height="17">
                                                    <use xlink:href="#icon-comment"></use>
                                                </svg>
                                                <span><?= get_comments_count($con, $post['post_id']) ?></span>
                                                <span class="visually-hidden">количество комментариев</span>
                                            </a>
                                            <a class="post__indicator post__indicator--repost button"
                                               href="/repost.php/?post_id=<?= $post['post_id'] ?>" title="Репост">
                                                <svg class="post__indicator-icon" width="19" height="17">
                                                    <use xlink:href="#icon-repost"></use>
                                                </svg>
                                                <span><?= get_repost_count($con, $post['post_id']) ?></span>
                                                <span class="visually-hidden">количество репостов</span>
                                            </a>
                                        </div>
                                        <time class="post__time"
                                              datetime="<?= $post['pub_date']; ?>"><?= rel_time($post['pub_date']); ?>
                                            назад
                                        </time>
                                    </div>
                                    <ul class="post__tags">
                                        <?php foreach (get_hashtags($con, $post['post_id']) as $hashtag): ?>
                                            <li>
                                                <a href="/search.php/?search_text=%23<?= $hashtag ?>">#<?= htmlspecialchars($hashtag); ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </footer>
                                <?php if (isset($_GET['comments_post_id']) && $_GET['comments_post_id'] == $post['post_id']) : ?>
                                <div class="comments">
                                    <div class="comments__list-wrapper">
                                        <ul class="comments__list">
                                            <? foreach (get_comments($con, $post['post_id']) as $comment): ?>
                                                <li class="comments__item user">
                                                    <div class="comments__avatar">
                                                        <a class="user__avatar-link"
                                                           href="/profile.php/?user_id=<?= $comment['user_id'] ?>">
                                                            <img class="comments__picture"
                                                                 src="<?= $comment['avatar_path'] ?>"
                                                                 alt="Аватар пользователя">
                                                        </a>
                                                    </div>
                                                    <div class="comments__info">
                                                        <div class="comments__name-wrapper">
                                                            <a class="comments__user-name"
                                                               href="/profile.php/?user_id=<?= $comment['user_id'] ?>">
                                                                <span><?= htmlspecialchars($comment['user_name']) ?></span>
                                                            </a>
                                                            <time class="comments__time"
                                                                  datetime="<?= $comment['pub_date'] ?>">
                                                                <?= rel_time($comment['pub_date']) ?> назад
                                                            </time>
                                                        </div>
                                                        <p class="comments__text">
                                                            <?= htmlspecialchars(trim($comment['content'])) ?>
                                                        </p>
                                                    </div>
                                                </li>
                                            <? endforeach; ?>
                                        </ul>
                                        <?php if (!isset($_GET['show_all']) && (get_comments_count($con,
                                                    $post['post_id']) > 3)) : ?>
                                            <a class="comments__more-link"
                                               href="<?= get_show_comments_link($post['post_id']) ?>&show_all">
                                                <span>Показать все комментарии</span>
                                                <sup class="comments__amount"><?= get_comments_count($con,
                                                        $post['post_id']) ?></sup>
                                            </a>
                                        <? endif; ?>
                                        <?php if ((isset($_GET['comments'])) && isset($_GET['comments'])) : ?>
                                            <a class="comments__more-link"
                                               href="<?= get_show_comments_link($post['post_id']) ?>">
                                                <span>Оставить 3 последних комментария</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <form class="comments__form form"
                                          action="/profile.php/?user_id=<?= $post['user_id'] ?>&content=posts&comments_post_id=<?= $post['post_id'] ?>"
                                          method="post">
                                        <div class="comments__my-avatar">
                                            <img class="comments__picture" src="<?= $_SESSION['user']['avatar_path'] ?>"
                                                 alt="Аватар пользователя">
                                        </div>
                                        <textarea
                                                class="comments__textarea form__textarea <? if (isset($errors['message-text'])) {
                                                    echo 'message-text-error';
                                                } ?>"
                                                placeholder="<?php if (isset($errors['message-text'])) {
                                                    echo $errors['message-text'];
                                                } else {
                                                    echo 'Введите ваше сообщение';
                                                } ?>" name="message-text"></textarea>
                                        <label class="visually-hidden">Ваш комментарий</label>
                                        <button class="comments__submit button button--green" type="submit">Отправить
                                        </button>
                                    </form>
                                    <?php else : ?>
                                        <div class="comments">
                                            <a class="comments__button button"
                                               href="<?= get_show_comments_link($post['post_id']) ?>">Показать
                                                комментарии</a>
                                        </div>
                                    <? endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </section>
                    <section class="profile__likes tabs__content <?php if ($_GET['content'] == 'likes') {
                        echo 'tabs__content--active';
                    } ?>">
                        <h2 class="visually-hidden">Лайки</h2>
                        <ul class="profile__likes-list">
                            <?php foreach ($likes as $like) : ?>
                                <li class="post-mini post-mini--<?= $like['icon_class'] ?> post user">
                                    <div class="post-mini__user-info user__info">
                                        <div class="post-mini__avatar user__avatar">
                                            <a class="user__avatar-link"
                                               href="/profile.php/?user_id=<?= $like['who_like_id'] ?>">
                                                <img class="post-mini__picture user__picture"
                                                     src="<?= $like['who_like_avatar_path'] ?>"
                                                     alt="Аватар пользователя">
                                            </a>
                                        </div>
                                        <div class="post-mini__name-wrapper user__name-wrapper">
                                            <a class="post-mini__name user__name"
                                               href="/profile.php/?user_id=<?= $like['who_like_id'] ?>">
                                                <span><?= $like['who_like_name'] ?></span>
                                            </a>
                                            <div class="post-mini__action">
                                                <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                                                <time class="post-mini__time user__additional"
                                                      datetime="2014-03-20T20:20"><?= rel_time($like['dt_add']) ?> назад
                                                </time>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="post-mini__preview">
                                        <a class="post-mini__link" href="/post.php/?post_id=<?= $like['post_id'] ?>"
                                           title="Перейти на публикацию">
                                            <?php if ($like['content_type_id'] == 3) : ?>
                                                <img class="post-mini__image" src="<?= $like['img'] ?>" width="109"
                                                     height="109" alt="Превью публикации">
                                                <span class="visually-hidden">Фото</span>
                                            <?php elseif ($like['content_type_id'] == 4) : ?>
                                                <div class="post-mini__image-wrapper">
                                                    <img class="post-mini__image"
                                                         src="https://<?= get_youtube_image_preview($like['video']) ?>"
                                                         width="109" height="109" alt="Превью публикации">
                                                    <span class="post-mini__play-big">
                                                            <svg class="post-mini__play-big-icon" width="12"
                                                                 height="13">
                                                                <use xlink:href="#icon-video-play-big"></use>
                                                            </svg>
                                                        </span>
                                                </div>
                                                <span class="visually-hidden">Видео</span>
                                            <?php elseif ($like['content_type_id'] == 2) : ?>
                                                <span class="visually-hidden">Цитата</span>
                                                <svg class="post-mini__preview-icon" width="21" height="20">
                                                    <use xlink:href="#icon-filter-quote"></use>
                                                </svg>
                                            <?php elseif ($like['content_type_id'] == 1) : ?>
                                                <span class="visually-hidden">Текст</span>
                                                <svg class="post-mini__preview-icon" width="20" height="21">
                                                    <use xlink:href="#icon-filter-text"></use>
                                                </svg>
                                            <?php elseif ($like['content_type_id'] == 5) : ?>
                                                <span class="visually-hidden">Ссылка</span>
                                                <svg class="post-mini__preview-icon" width="21" height="18">
                                                    <use xlink:href="#icon-filter-link"></use>
                                                </svg>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    </section>
                    <section class="profile__subscriptions tabs__content <?php if ($_GET['content'] == 'followers') {
                        echo 'tabs__content--active';
                    } ?>">
                        <h2 class="visually-hidden">Подписки</h2>
                        <ul class="profile__subscriptions-list">
                            <?php foreach ($followers as $follower) : ?>
                                <li class="post-mini post-mini--photo post user">
                                    <div class="post-mini__user-info user__info">
                                        <div class="post-mini__avatar user__avatar">
                                            <a class="user__avatar-link"
                                               href="/profile.php/?user_id=<?= $follower['user_id'] ?>">
                                                <img class="post-mini__picture user__picture"
                                                     src="<?= $follower['avatar_path'] ?>" alt="Аватар пользователя">
                                            </a>
                                        </div>
                                        <div class="post-mini__name-wrapper user__name-wrapper">
                                            <a class="post-mini__name user__name"
                                               href="/profile.php/?user_id=<?= $follower['user_id'] ?>">
                                                <span><?= htmlspecialchars($follower['user_name']) ?></span>
                                            </a>
                                            <time class="post-mini__time user__additional"
                                                  datetime="<?= $follower['reg_date'] ?>"><?= rel_time($follower['reg_date']) ?>
                                                на сайте
                                            </time>
                                        </div>
                                    </div>
                                    <div class="post-mini__rating user__rating">
                                        <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                                            <span class="post-mini__rating-amount user__rating-amount"><?= get_user_posts_count($con,
                                                    $follower['user_id']) ?></span>
                                            <span class="post-mini__rating-text user__rating-text">публикаций</span>
                                        </p>
                                        <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                                            <span class="post-mini__rating-amount user__rating-amount"><?= get_user_followers($con,
                                                    $follower['user_id']) ?></span>
                                            <span class="post-mini__rating-text user__rating-text">подписчиков</span>
                                        </p>
                                    </div>
                                    <div class="post-mini__user-buttons user__buttons">
                                        <?php if ($follower['user_id'] !== $_SESSION['user']['user_id']) : ?>
                                            <?php if (is_follow($con, $follower['user_id'])) : ?>
                                                <a class="post-mini__user-button user__button user__button--subscription button button--main"
                                                   href="/unfollow.php/?user_id=<?= $follower['user_id'] ?>">Отписаться</a>
                                            <?php else : ?>
                                                <a class="post-mini__user-button user__button user__button--subscription button button--main"
                                                   href="/follow.php/?user_id=<?= $follower['user_id'] ?>">Подписаться</a>
                                            <? endif; ?>
                                        <? endif; ?>
                                    </div>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>
