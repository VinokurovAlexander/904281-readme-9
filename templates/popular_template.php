<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <li class="sorting__item sorting__item--popular">
                        <a class="sorting__link <?= get_sorting_link_class('popular', $_GET['sorting']) ?>"
                           href="/popular.php?content_type_id=<?= $_GET['content_type_id'] ?>&sorting=popular_<?= get_sorting_type_link('popular',
                               $_GET['sorting']) ?>&page=1">
                            <span>Популярность</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= get_sorting_link_class('likes', $_GET['sorting']) ?>"
                           href="/popular.php?content_type_id=<?= $_GET['content_type_id'] ?>&sorting=likes_<?= get_sorting_type_link('likes',
                               $_GET['sorting']) ?>&page=1">
                            <span>Лайки</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="sorting__item">
                        <a class="sorting__link <?= get_sorting_link_class('date', $_GET['sorting']) ?>"
                           href="/popular.php?content_type_id=<?= $_GET['content_type_id'] ?>&sorting=date_<?= get_sorting_type_link('date',
                               $_GET['sorting']) ?>&page=1">
                            <span>Дата</span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all <?php if ($_GET['content_type_id'] === 'all') {
                            echo 'filters__button--active';
                        } ?> " href="/popular.php?content_type_id=all&sorting=popular_desc&page=1">
                            <span>Все</span>
                        </a>
                    </li>
                    <?php foreach ($content_types as $content_type): ?>
                        <li class="popular__filters-item filters__item">
                            <a class="filters__button filters__button--photo button
                        <?php if (($_GET['content_type_id'] === $content_type['content_type_id'])) {
                                echo "filters__button--active";
                            } ?>"
                               href="/popular.php?content_type_id=<?= $content_type['content_type_id']; ?>&sorting=popular_desc&page=1">
                                <span class="visually-hidden"><?= $content_type['content_type']; ?></span>
                                <svg class="filters__icon" width="22" height="18">
                                    <use xlink:href="#icon-filter-<?= $content_type['icon_class']; ?>"></use>
                                </svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="popular__posts">
            <?php foreach ($posts as $post): ?>
                <article class="popular__post post post-<?= $post['icon_class']; ?>">
                    <header class="post__header">
                        <h2>
                            <a href="/post.php?post_id=<?= $post['post_id']; ?>">
                                <?= htmlspecialchars($post['title']); ?>
                            </a>
                        </h2>
                    </header>
                    <div class="post__main">
                        <?php if ($post['icon_class'] === 'quote'): ?>
                            <blockquote>
                                <p>
                                    <?= htmlspecialchars($post['text']); ?>
                                </p>
                                <cite><?= htmlspecialchars($post['quote_author']) ?></cite>
                            </blockquote>
                        <?php elseif ($post['icon_class'] === 'text'): ?>
                            <p>
                                <?= (cut_text(htmlspecialchars($post['text']), 300, $post['post_id'])); ?>
                            </p>
                        <?php elseif ($post['icon_class'] === 'photo'): ?>
                            <div class="post-photo__image-wrapper">
                                <img src="<?= $post['img']; ?>" alt="Фото от пользователя" width="360" height="240">
                            </div>
                        <?php elseif ($post['icon_class'] === 'link'): ?>
                            <div class="post-link__wrapper">
                                <a class="post-link__external" href="<?= $post['link']; ?>" title="Перейти по ссылке">
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
                        <?php elseif ($post['icon_class'] === 'video'): ?>
                            <iframe width="360" height="240" src="<?= $post['video'] ?>" frameborder="0"></iframe>
                        <?php endif; ?>
                    </div>
                    <footer class="post__footer">
                        <div class="post__author">
                            <a class="post__author-link" href="/profile.php?user_id=<?= $post['user_id']; ?>"
                               title="Автор">
                                <div class="post__avatar-wrapper">
                                    <img class="post__author-avatar" src="<?= $post['avatar_path']; ?>"
                                         alt="Аватар пользователя">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= htmlspecialchars($post['user_name']); ?></b>
                                    <time class="post__time" datetime="<?= $post['pub_date'] ?>"
                                          title="<?= post_time_title($post['pub_date']) ?>">
                                        <?= rel_time($post['pub_date']) ?> назад
                                    </time>
                                </div>
                            </a>
                        </div>
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes button"
                                   href="/like.php?post_id=<?= $post['post_id'] ?>" title="Лайк">
                                    <?php if (is_like($con, $post['post_id'], $_SESSION['user']['user_id'])) : ?>
                                        <svg class="post__indicator-icon post__indicator-icon--like-active" width="20"
                                             height="17">
                                            <use xlink:href="#icon-heart-active"></use>
                                        </svg>
                                    <?php else : ?>
                                        <svg class="post__indicator-icon" width="20" height="17">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                    <? endif; ?>
                                    <span><?= $post['likes_count'] ?></span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button"
                                   href="/post.php?post_id=<?= $post['post_id'] ?>" title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span><?= get_comments_count($con, $post['post_id']) ?></span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="popular__page-links">
            <?php if ($pages_count > 1) : ?>
                <?php if ($_GET['page'] !== '1') : ?>
                    <a class="popular__page-link popular__page-link--prev button button--gray"
                       href="<?= get_page_link('prev', $_GET['content_type_id'], $_GET['sorting'], $_GET['page']) ?>">
                        Предыдущая страница
                    </a>
                <? endif; ?>
                <?php if (intval($_GET['page']) !== intval($pages_count)) : ?>
                    <a class="popular__page-link popular__page-link--next button button--gray"
                       href="<?= get_page_link('next', $_GET['content_type_id'], $_GET['sorting'], $_GET['page']) ?>">
                        Следующая страница
                    </a>
                <? endif; ?>
            <? endif; ?>
        </div>
    </div>
</section>