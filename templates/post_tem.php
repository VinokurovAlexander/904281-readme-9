<main class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?=$post['title'];?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <div class="post-details__wrapper post-photo">
                <div class="post-details__main-block post post--details">
                    <?php if ($post['content_type_id'] == '3' ) :?>
                    <div class="post-details__image-wrapper post-photo__image-wrapper">
                        <img src="/<?=$post['img']?>" alt="Фото от пользователя" width="760" height="507">
                    </div>
                    <?php elseif ($post['content_type_id'] == '2' ) :?>
                    <div class="post-details__image-wrapper post-quote">
                        <div class="post__main">
                            <blockquote>
                                <p>
                                    <?=htmlspecialchars($post['text'])?>
                                </p>
                                <cite><?=htmlspecialchars($post['quote_author'])?></cite>
                            </blockquote>
                        </div>
                    </div>
                    <?php elseif ($post['content_type_id'] == '5' ) :?>
                    <div class="post__main">
                        <div class="post-link__wrapper">
                            <a class="post-link__external" href="<?=htmlspecialchars($post['link'])?>" title="Перейти по ссылке">
                                <div class="post-link__info-wrapper">
                                    <div class="post-link__icon-wrapper">
                                        <img src="../img/logo-vita.jpg" alt="Иконка">
                                    </div>
                                    <div class="post-link__info">
                                        <h3><?=htmlspecialchars($post['title'])?></h3>
                                        <p><?=htmlspecialchars($post['text'])?></p>
                                    </div>
                                </div>
                                <span><?=htmlspecialchars($post['link'])?></span>
                            </a>
                        </div>
                    </div>
                    <?php elseif ($post['content_type_id'] == '1' ) :?>
                        <div class="post-details__image-wrapper post-text">
                            <div class="post__main">
                                <p>
                                    <?=htmlspecialchars($post['text'])?>
                                </p>
                            </div>
                        </div>
                    <?php elseif ($post['content_type_id'] == '4' ) :?>
                        <iframe width="760" height="400" src="<?=$post['video']?>" frameborder="0"></iframe>
                    <?php endif; ?>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="/like.php/?post_id=<?=$post['post_id'] ?>" title="Лайк">
                                <?php if(is_like($con,$post['post_id'])) : ?>
                                    <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                        <use xlink:href="#icon-heart-active"></use>
                                    </svg>
                                <?php else : ?>
                                    <svg class="post__indicator-icon" width="20" height="17">
                                        <use xlink:href="#icon-heart"></use>
                                    </svg>
                                <?endif;?>
                                <span><?=$post['likes_count']?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="/post.php/?post_id=<?=$post['post_id']?>" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?=get_comments_count($con,$post['post_id']) ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                            <a class="post__indicator post__indicator--repost button" href="/repost.php/?post_id=<?=$post['post_id']?>" title="Репост">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-repost"></use>
                                </svg>
                                <span><?=get_repost_count($con,$post['post_id'])?></span>
                                <span class="visually-hidden">количество репостов</span>
                            </a>
                        </div>
                        <span class="post__view"><?=get_view_count($con,$post['post_id'])?> просмотров</span>
                    </div>
                    <div class="comments">
                        <form class="comments__form form" action="/post.php/?post_id=<?=$post['post_id']?>" method="post">
                            <div class="comments__my-avatar">
                                <img class="comments__picture" src="<?=$_SESSION['user']['avatar_path']?>" alt="Аватар пользователя">
                            </div>
                            <textarea class="comments__textarea form__textarea <? if (isset($errors['message-text'])) {echo 'message-text-error';}?>"
                                      placeholder="<? if (isset($errors['message-text'])) {echo $errors['message-text'];} else {echo 'Введите ваше сообщение';}?>" name="message-text"></textarea>
                            <label class="visually-hidden">Ваш комментарий</label>
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                        </form>
                        <div class="comments__list-wrapper">
                            <ul class="comments__list">
                                <?php foreach ($comments as $comment) : ?>
                                <li class="comments__item user">
                                    <div class="comments__avatar">
                                        <a class="user__avatar-link" href="/profile.php/?user_id=<?=$comment['user_id']?>">
                                            <img class="comments__picture" src="<?=$comment['avatar_path']?>" alt="Аватар пользователя">
                                        </a>
                                    </div>
                                    <div class="comments__info">
                                        <div class="comments__name-wrapper">
                                            <a class="comments__user-name" href="/profile.php/?user_id=<?=$comment['user_id']?>">
                                                <span><?=htmlspecialchars($comment['user_name'])?></span>
                                            </a>
                                            <time class="comments__time" datetime="<?=$comment['pub_date']?>">
                                                <?=rel_time($comment['pub_date'])?> назад
                                            </time>
                                        </div>
                                        <p class="comments__text">
                                            <?=htmlspecialchars(trim($comment['content']))?>
                                        </p>
                                    </div>
                                </li>
                                <?endforeach;?>
                            </ul>
                            <?php if ((get_comments_count($con,$post['post_id']) > 3) && (!isset($_GET['comments']) || $_GET['comments'] !== 'full')) : ?>
                                <a class="comments__more-link" href="/post.php/?post_id=<?=$post['post_id']?>&comments=full">
                                    <span>Показать все комментарии</span>
                                    <sup class="comments__amount"><?=get_comments_count($con,$post['post_id']) ?></sup>
                                </a>
                            <?endif;?>
                            <?php if ((isset($_GET['comments'])) && $_GET['comments'] == 'full') : ?>
                                <a class="comments__more-link" href="/post.php/?post_id=<?=$post['post_id']?>">
                                    <span>Оставить 3 последних комментария</span>
                                </a>
                            <?endif;?>
                        </div>
                    </div>
                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link" href="/profile.php/?user_id=<?=$post['user_id']?>">
                                <img class="post-details__picture user__picture" src="<?=$post['avatar_path']?>" alt="Аватар пользователя">
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name" href="/profile.php/?user_id=<?=$post['user_id']?>">
                                <span><?=htmlspecialchars($post['user_name'])?></span>
                            </a>
                            <time class="post-details__time user__time" datetime="2014-03-20"><?=rel_time($_SESSION['user']['reg_date'])?> на сайте</time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span class="post-details__rating-amount user__rating-amount">
                                <?=get_user_followers($con,$post['user_id'])?>
                            </span>
                            <span class="post-details__rating-text user__rating-text">подписчиков</span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span class="post-details__rating-amount user__rating-amount">
                                <?=get_user_posts_count($con,$post['user_id'])?>
                            </span>
                            <span class="post-details__rating-text user__rating-text">публикаций</span>
                        </p>
                    </div>
                    <div class="post-details__user-buttons user__buttons">
                        <?php if ($post['user_id'] !== $_SESSION['user']['user_id']) :?>
                            <?php if (is_follow($con,$post['user_id'])) :?>
                                <a class="user__button user__button--subscription button button--main" href="/unfollow.php/?user_id=<?=$post['user_id']?>">Отписаться</a>
                                <a class="user__button user__button--writing button button--green" href="/messages.php/?user_id=<?=$post['user_id']?>">Сообщение</a>
                            <?php else : ?>
                                <a class="user__button user__button--subscription button button--main" href="/follow.php/?user_id=<?=$post['user_id']?>">Подписаться</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
