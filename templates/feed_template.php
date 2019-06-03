<main class="page__main page__main--feed">
    <div class="container">
        <h1 class="page__title page__title--feed">Моя лента</h1>
    </div>
    <div class="page__main-wrapper container">
        <section class="feed">
            <h2 class="visually-hidden">Лента</h2>
            <div class="feed__main-wrapper">
                <div class="feed__wrapper">
                    <? foreach ($posts as $post) : ?>
                        <article class="feed__post post post-<?= $post['icon_class'] ?>">
                            <header class="post__header post__author">
                                <a class="post__author-link" href="/profile.php/?user_id=<?= $post['user_id'] ?>"
                                   title="Автор">
                                    <div class="post__avatar-wrapper">
                                        <img class="post__author-avatar" src="<?= $post['avatar_path'] ?>"
                                             alt="Аватар пользователя" width="60" height="60">
                                    </div>
                                    <div class="post__info">
                                        <b class="post__author-name"><?= htmlspecialchars($post['user_name']) ?></b>
                                        <span class="post__time"><?= rel_time($post['pub_date']) ?> назад</span>
                                    </div>
                                </a>
                            </header>
                            <div class="post__main">
                                <h2><a href="/post.php/?post_id=<?= $post['post_id'] ?>"><?= $post['title'] ?></a></h2>
                                <?php if ($post['content_type_id'] == '3') : ?>
                                    <div class="post-details__image-wrapper post-photo__image-wrapper">
                                        <img src="/<?= $post['img'] ?>" alt="Фото от пользователя" width="760"
                                             height="507">
                                    </div>
                                <?php elseif ($post['content_type_id'] == '2') : ?>
                                    <div class="post-details__image-wrapper post-quote">
                                        <div class="post__main">
                                            <blockquote>
                                                <p>
                                                    <?= htmlspecialchars($post['text']) ?>
                                                </p>
                                                <cite><?= htmlspecialchars($post['quote_author']) ?></cite>
                                            </blockquote>
                                        </div>
                                    </div>
                                <?php elseif ($post['content_type_id'] == '5') : ?>
                                    <div class="post-link__wrapper">
                                        <a class="post-link__external" href="<?= htmlspecialchars($post['link']) ?>"
                                           title="Перейти по ссылке">
                                            <div class="post-link__info-wrapper">
                                                <div class="post-link__icon-wrapper">
                                                    <img src="../img/logo-vita.jpg" alt="Иконка">
                                                </div>
                                                <div class="post-link__info">
                                                    <h3><?= htmlspecialchars($post['title']) ?></h3>
                                                    <p><?= htmlspecialchars($post['text']) ?></p>
                                                </div>
                                            </div>
                                            <span><?= htmlspecialchars($post['link']) ?></span>
                                        </a>
                                    </div>
                                <?php elseif ($post['content_type_id'] == '1') : ?>
                                    <div class="post-details__image-wrapper post-text">
                                        <div class="post__main">
                                            <p>
                                                <?= htmlspecialchars($post['text']) ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php elseif ($post['content_type_id'] == '4') : ?>
                                    <iframe width="760" height="400" src="<?= $post['video'] ?>"
                                            frameborder="0"></iframe>
                                <?php endif; ?>
                            </div>
                            <footer class="post__footer post__indicators">
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
                                        <span><?= get_post_likes_count($con, $post['post_id']) ?></span>
                                        <span class="visually-hidden">количество лайков</span>
                                    </a>
                                    <a class="post__indicator post__indicator--comments button"
                                       href="/post.php/?post_id=<?= $post['post_id'] ?>" title="Комментарии">
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
                            </footer>
                            <ul class="post__tags">
                                <?php foreach (get_hashtags($con, $post['post_id']) as $hashtag): ?>
                                    <li>
                                        <a href="/search.php/?search_text=%23<?= $hashtag ?>">#<?= htmlspecialchars($hashtag); ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <? endforeach; ?>
                </div>
            </div>
            <ul class="feed__filters filters">
                <li class="feed__filters-item filters__item">
                    <a class="filters__button <?php if ($_GET['content_type_id'] == 'all') {
                        echo 'filters__button--active';
                    } ?>" href="/feed.php/?content_type_id=all">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($content_types as $ct) : ?>
                    <li class="feed__filters-item filters__item">
                        <a class="filters__button filters__button--<?= $ct['icon_class'] ?> button <?php if (($_GET['content_type_id'] == $ct['content_type_id'])) {
                            echo "filters__button--active";
                        } ?>"
                           href="/feed.php/?content_type_id=<?= $ct['content_type_id'] ?>">
                            <span class="visually-hidden"><?= $ct['content_type'] ?></span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-<?= $ct['icon_class'] ?>"></use>
                            </svg>
                        </a>
                    </li>
                <? endforeach; ?>
            </ul>
        </section>
        <aside class="promo">
            <article class="promo__block promo__block--barbershop">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе!
                </p>
                <a class="promo__link" href="#">
                    Подробнее
                </a>
            </article>
            <article class="promo__block promo__block--technomart">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Товары будущего уже сегодня в онлайн-сторе Техномарт!
                </p>
                <a class="promo__link" href="#">
                    Перейти в магазин
                </a>
            </article>
            <article class="promo__block">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Здесь<br> могла быть<br> ваша реклама
                </p>
                <a class="promo__link" href="#">
                    Разместить
                </a>
            </article>
        </aside>
    </div>
</main>