<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
        <h2 class="visually-hidden">Сообщения</h2>
        <div class="messages__contacts">
            <ul class="messages__contacts-list tabs__list">
                <li class="messages__contacts-item">
                    <a class="messages__contacts-tab messages__contacts-tab--active tabs__item tabs__item--active" href="#">
                        <div class="messages__avatar-wrapper">
                            <img class="messages__avatar" src="img/userpic-larisa.jpg" alt="Аватар пользователя">
                        </div>
                        <div class="messages__info">
                  <span class="messages__contact-name">
                    Лариса Роговая
                  </span>
                            <div class="messages__preview">
                                <p class="messages__preview-text">
                                    Озеро Байкал – огромное
                                </p>
                                <time class="messages__preview-time" datetime="2019-05-01T14:40">
                                    14:40
                                </time>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="messages__contacts-item messages__contacts-item--new">
                    <a class="messages__contacts-tab tabs__item" href="#">
                        <div class="messages__avatar-wrapper">
                            <img class="messages__avatar" src="img/userpic-petro.jpg" alt="Аватар пользователя">
                            <i class="messages__indicator">2</i>
                        </div>
                        <div class="messages__info">
                  <span class="messages__contact-name">
                    Петр Демин
                  </span>
                            <div class="messages__preview">
                                <p class="messages__preview-text">
                                    Ок, бро! По рукам
                                </p>
                                <time class="messages__preview-time" datetime="2019-05-01T00:15">
                                    00:15
                                </time>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="messages__contacts-item">
                    <a class="messages__contacts-tab tabs__item" href="#">
                        <div class="messages__avatar-wrapper">
                            <img class="messages__avatar" src="img/userpic-mark.jpg" alt="Аватар пользователя">
                        </div>
                        <div class="messages__info">
                  <span class="messages__contact-name">
                    Марк Смолов
                  </span>
                            <div class="messages__preview">
                                <p class="messages__preview-text">
                                    Вы: Марк, ждем тебя
                                </p>
                                <time class="messages__preview-time" datetime="2019-01-02T14:40">
                                    2 янв
                                </time>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="messages__contacts-item">
                    <a class="messages__contacts-tab tabs__item" href="#">
                        <div class="messages__avatar-wrapper">
                            <img class="messages__avatar" src="img/userpic-tanya.jpg" alt="Аватар пользователя">
                        </div>
                        <div class="messages__info">
                  <span class="messages__contact-name">
                    Таня Фирсова
                  </span>
                            <div class="messages__preview">
                                <p class="messages__preview-text">
                                    Вы: Девушка не
                                </p>
                                <time class="messages__preview-time" datetime="2018-09-30T14:40">
                                    31 сент
                                </time>
                            </div>
                        </div>
                    </a>
                </li>
                <?php foreach ($dialogs as $dialog) :?>
                    <li class="messages__contacts-item">
                        <a class="messages__contacts-tab tabs__item" href="#">
                            <div class="messages__avatar-wrapper">
                                <img class="messages__avatar" src="<?=get_dialog_avatar($con,$dialog)?>" alt="Аватар пользователя">
                            </div>
                            <div class="messages__info">
                                <span class="messages__contact-name"><?=get_dialog_username($con,$dialog)?></span>
                                <div class="messages__preview">
                                    <p class="messages__preview-text">
                                        <?php if ($dialog['sen_id'] == $_SESSION['user']['user_id']) {echo 'Вы: ';} ?>
                                        <?=$dialog['content']?>
                                    </p>
                                    <time class="messages__preview-time" datetime="<?=$dialog['pub_date']?>">
                                        <?=get_message_time($dialog['pub_date'])?>
                                    </time>
                                </div>
                            </div>
                        </a>
                    </li>
                <?endforeach;?>
            </ul>
        </div>
        <div class="messages__chat">
            <div class="messages__chat-wrapper">
                <ul class="messages__list tabs__content tabs__content--active">

                        <li class="messages__item">
                            <div class="messages__info-wrapper">
                                <div class="messages__item-avatar">
                                    <a class="messages__author-link" href="#">
                                        <img class="messages__avatar" src="img/userpic-larisa-small.jpg" alt="Аватар пользователя">
                                    </a>
                                </div>
                                <div class="messages__item-info">
                                    <a class="messages__author" href="#">
                                        Лариса Роговая
                                    </a>
                                    <time class="messages__time" datetime="2019-05-01T14:40">
                                        1 ч назад
                                    </time>
                                </div>
                            </div>
                            <p class="messages__text">
                                Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                            </p>
                        </li>

                    <li class="messages__item">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic-larisa-small.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Лариса Роговая
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:40">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                        </p>
                    </li>
                    <li class="messages__item messages__item--my">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Антон Глуханько
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:39">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы.
                        </p>
                    </li>
                    <li class="messages__item">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic-larisa-small.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Лариса Роговая
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:39">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы.
                        </p>
                    </li>
                </ul>

                <ul class="messages__list tabs__content">
                    <li class="messages__item">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic-larisa-small.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Лариса Роговая
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:40">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы.
                        </p>
                    </li>
                    <li class="messages__item messages__item--my">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Антон Глуханько
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:39">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                        </p>
                    </li>
                    <li class="messages__item">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic-larisa-small.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Лариса Роговая
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:39">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                        </p>
                    </li>
                </ul>

                <ul class="messages__list tabs__content">
                    <li class="messages__item">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic-larisa-small.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Лариса Роговая
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:40">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                        </p>
                    </li>
                    <li class="messages__item messages__item--my">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Антон Глуханько
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:39">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                        </p>
                    </li>
                    <li class="messages__item">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic-larisa-small.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Лариса Роговая
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:39">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                        </p>
                    </li>
                </ul>

                <ul class="messages__list tabs__content">
                    <li class="messages__item">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic-larisa-small.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Лариса Роговая
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:40">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                        </p>
                    </li>
                    <li class="messages__item messages__item--my">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Антон Глуханько
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:39">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                        </p>
                    </li>
                    <li class="messages__item">
                        <div class="messages__info-wrapper">
                            <div class="messages__item-avatar">
                                <a class="messages__author-link" href="#">
                                    <img class="messages__avatar" src="img/userpic-larisa-small.jpg" alt="Аватар пользователя">
                                </a>
                            </div>
                            <div class="messages__item-info">
                                <a class="messages__author" href="#">
                                    Лариса Роговая
                                </a>
                                <time class="messages__time" datetime="2019-05-01T14:39">
                                    1 ч назад
                                </time>
                            </div>
                        </div>
                        <p class="messages__text">
                            Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается самым глубоким озером в мире. Он окружен сетью пешеходных маршрутов, называемых Большой байкальской тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для летних экскурсий. Зимой здесь можно кататься на коньках и собачьих упряжках.
                        </p>
                    </li>
                </ul>
            </div>
            <div class="comments">
                <form class="comments__form form" action="#" method="post">
                    <div class="comments__my-avatar">
                        <img class="comments__picture" src="img/userpic.jpg" alt="Аватар пользователя">
                    </div>
                    <textarea class="comments__textarea form__textarea <? if (isset($errors['message-text'])) {echo 'message-text-error';}?>"
                              placeholder="<? if (isset($errors['message-text'])) {echo $errors['message-text'];} else {echo 'Введите ваше сообщение';}?>" name="message-text"></textarea>
                    <label class="visually-hidden">Ваше сообщение</label>
                    <button class="comments__submit button button--green" type="submit">Отправить</button>
                </form>
            </div>
        </div>
    </section>
</main>
