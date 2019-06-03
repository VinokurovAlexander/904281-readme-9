<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">
                        <?php foreach ($content_types as $k => $v): ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a class="adding-post__tabs-link filters__button filters__button--<?= $v['icon_class']; ?>  tabs__item tabs__item--active button
                               <?php if ($_GET['content_type_id'] == $v['content_type_id']) {
                                    echo "filters__button--active";
                                } ?>"
                                   href="/add.php/?content_type_id=<?= $v['content_type_id']; ?>">
                                    <svg class="filters__icon" width="22" height="18">
                                        <use xlink:href="#icon-filter-<?= $v['icon_class']; ?>"></use>
                                    </svg>
                                    <span><?= $v['content_type']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?= $post_add; ?>
            </div>
        </div>
    </div>
</main>


<div class="modal modal--adding">
    <div class="modal__wrapper">
        <button class="modal__close-button button" type="button">
            <svg class="modal__close-icon" width="18" height="18">
                <use xlink:href="#icon-close"></use>
            </svg>
            <span class="visually-hidden">Закрыть модальное окно</span></button>
        <div class="modal__content">
            <h1 class="modal__title">Пост добавлен</h1>
            <p class="modal__desc">
                Озеро Байкал – огромное древнее озеро в горах Сибири к северу от монгольской границы. Байкал считается
                самым глубоким озером в мире. Он окружен сефтью пешеходных маршрутов, называемых Большой байкальской
                тропой. Деревня Листвянка, расположенная на западном берегу озера, – популярная отправная точка для
                летних экскурсий.
            </p>
            <div class="modal__buttons">
                <a class="modal__button button button--main" href="#">Синяя кнопка</a>
                <a class="modal__button button button--gray" href="#">Серая кнопка</a>
            </div>
        </div>
    </div>
</div>

<!--<script src="../libs/dropzone.js"></script>-->
<!--<script src="../js/dropzone-settings.js"></script>-->
<!--<script src="../js/main.js"></script>-->
</body>
</html>
