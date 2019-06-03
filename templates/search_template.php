<main class="page__main page__main--search-results">
    <h1 class="visually-hidden">Страница результатов поиска</h1>
    <section class="search">
        <h2 class="visually-hidden">Результаты поиска</h2>
        <div class="search__query-wrapper">
            <div class="search__query container">
                <span>Вы искали:</span>
                <span class="search__query-text"><?= get_search() ?></span>
            </div>
        </div>
        <div class="search__results-wrapper">
            <div class="container">
                <div class="search__content">
                    <?php foreach ($posts as $post) : ?>
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
        </div>
    </section>
</main>

