<?php if ($get_ct_id == '1') : ?>    <!-- ct = 'Текст'-->

<section class="adding-post__text tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления текста</h2>
    <form class="adding-post__form form" action="/add.php/?content_type_id=<?= $get_ct_id; ?>" method="post">
        <div class="form__text-inputs-wrapper">

            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="text-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['text-heading']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="text-heading" type="text" name="text-heading" placeholder="Введите заголовок" value="<? if(isset($_POST['text-heading'])) {print($_POST['text-heading']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['text-heading']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['text-heading']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__textarea-wrapper form__textarea-wrapper ">
                    <label class="adding-post__label form__label" for="post-text">Текст поста <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['post-text']) {echo "form__input-section--error";} ?>">
                        <textarea class="adding-post__textarea form__textarea form__input" id="post-text" placeholder="Введите текст публикации" name="post-text"><? if(isset($_POST['post-text'])) {print($_POST['post-text']);} ?></textarea>
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['post-text']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['post-text']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="post-tags">Теги<span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['post-tags']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="post-tags" type="text" name="post-tags" placeholder="Введите теги" value="<? if(isset($_POST['post-tags'])) {print($_POST['post-tags']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['post-tags']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['post-tags']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (count($errors)) : ?>
            <div class="form__invalid-block">
                <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                <ul class="form__invalid-list">
                    <?php foreach ($errors as $k => $v) : ?>
                        <li class="form__invalid-item"><?=$v['field_name_rus']. '.' . ' ' . $v['error_title'] ;?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
            <a class="adding-post__close" href="#">Закрыть</a>
        </div>
    </form>
</section>

<?php elseif ($get_ct_id == '2') : ?>    <!-- ct = 'Цитата'  -->

<section class="adding-post__quote tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления цитаты</h2>
    <form class="adding-post__form form" action="/add.php/?content_type_id=<?= $get_ct_id; ?>" method="post">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="quote-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['quote-heading']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="quote-heading" type="text" name="quote-heading" placeholder="Введите заголовок" value="<? if(isset($_POST['quote-heading'])) {print($_POST['quote-heading']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['quote-heading']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['quote-heading']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__textarea-wrapper">
                    <label class="adding-post__label form__label" for="cite-text">Текст цитаты <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['quote-text']) {echo "form__input-section--error";} ?>">
                        <textarea class="adding-post__textarea adding-post__textarea--quote form__textarea form__input" id="cite-text" name="quote-text" placeholder="Текст цитаты"><? if(isset($_POST['quote-text'])) {print($_POST['quote-text']);} ?></textarea>
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['quote-text']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['quote-text']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__textarea-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="quote-author">Автор <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['quote-author']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="quote-author" type="text" name="quote-author" value="<? if(isset($_POST['quote-author'])) {print($_POST['quote-author']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['quote-author']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['quote-author']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="cite-tags">Теги<span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['quote-tags']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="cite-tags" type="text" name="quote-tags" placeholder="Введите теги" value="<? if(isset($_POST['quote-tags'])) {print($_POST['quote-tags']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['quote-tags']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['quote-tags']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (count($errors)) : ?>
            <div class="form__invalid-block">
                <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                <ul class="form__invalid-list">
                    <?php foreach ($errors as $k => $v) : ?>
                        <li class="form__invalid-item"><?=$v['field_name_rus']. '.' . ' ' . $v['error_title'] ;?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
            <a class="adding-post__close" href="#">Закрыть</a>
        </div>
    </form>
</section>

<?php elseif ($get_ct_id == '3') : ?>    <!-- ct = 'Картинка'  -->

<section class="adding-post__photo tabs__content tabs__content--active tabs__content--active">
    <h2 class="visually-hidden">Форма добавления фото</h2>
    <form class="adding-post__form form" action="/add.php/?content_type_id=<?= $get_ct_id; ?>" method="post" enctype="multipart/form-data">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="photo-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['photo-heading']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="photo-heading" type="text" name="photo-heading" placeholder="Введите заголовок" value="<? if(isset($_POST['photo-heading'])) {print($_POST['photo-heading']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['photo-heading']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['photo-heading']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="photo-url">Ссылка из интернета<span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['photo-link']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="photo-url" type="text" name="photo-link" placeholder="Введите ссылку" value="<? if(isset($_POST['photo-link'])) {print($_POST['photo-link']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['photo-link']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['photo-link']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="photo-tags">Теги<span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['photo-tags']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="photo-tags" type="text" name="photo-tags" placeholder="Введите теги" value="<? if(isset($_POST['photo-tags'])) {print($_POST['photo-tags']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['photo-tags']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['photo-tags']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (count($errors)) : ?>
            <div class="form__invalid-block">
                <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                <ul class="form__invalid-list">
                    <?php foreach ($errors as $k => $v) : ?>
                        <li class="form__invalid-item"><?=$v['field_name_rus']. '.' . ' ' . $v['error_title'] ;?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <div class="adding-post__input-file-container form__input-container form__input-container--file">
            <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                <div class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
                    <input class="adding-post__input-file form__input-file" id="userpic-file-photo" type="file" name="userpic-file-photo" title=" ">
                    <div class="form__file-zone-text">
                        <span>Перетащите фото сюда</span>
                    </div>
                </div>
                <button class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button" type="button">
                    <span>Выбрать фото</span>
                    <svg class="adding-post__attach-icon form__attach-icon" width="10" height="20">
                        <use xlink:href="#icon-attach"></use>
                    </svg>
                </button>
            </div>
            <div class="adding-post__file adding-post__file--photo form__file dropzone-previews">

            </div>
        </div>
        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
            <a class="adding-post__close" href="#">Закрыть</a>
        </div>
    </form>
</section>

<?php elseif ($get_ct_id == '4') : ?>    <!-- ct = 'Видео'  -->

<section class="adding-post__video tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления видео</h2>
    <form class="adding-post__form form" action="/add.php/?content_type_id=<?= $get_ct_id; ?>" method="post" enctype="multipart/form-data">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="video-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['video-heading']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="video-heading" type="text" name="video-heading" placeholder="Введите заголовок" value="<? if(isset($_POST['video-heading'])) {print($_POST['video-heading']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['video-heading']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['video-heading']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="video-url">Ссылка youtube <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['video-link']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="video-url" type="text" name="video-link" placeholder="Введите ссылку" value="<? if(isset($_POST['video-link'])) {print($_POST['video-link']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['video-link']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['video-link']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="video-tags">Теги<span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['video-tags']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="video-tags" type="text" name="video-tags" placeholder="Введите теги" value="<? if(isset($_POST['video-tags'])) {print($_POST['video-tags']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['video-tags']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['video-tags']['error_title'];?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (count($errors)) : ?>
            <div class="form__invalid-block">
                <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                <ul class="form__invalid-list">
                    <?php foreach ($errors as $k => $v) : ?>
                        <li class="form__invalid-item"><?=$v['field_name_rus']. '.' . ' ' . $v['error_title'] ;?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
            <a class="adding-post__close" href="#">Закрыть</a>
        </div>
    </form>
</section>

<?php elseif ($get_ct_id == '5') : ?>    <!-- ct = 'Ссылка'  -->

<section class="adding-post__link tabs__content tabs__content--active">
    <h2 class="visually-hidden">Форма добавления ссылки</h2>
    <form class="adding-post__form form" action="/add.php/?content_type_id=<?= $get_ct_id; ?>" method="post">
        <div class="form__text-inputs-wrapper">
            <div class="form__text-inputs">
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="link-heading">Заголовок <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['link-heading']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="link-heading" type="text" name="link-heading" placeholder="Введите заголовок" value="<? if(isset($_POST['link-heading'])) {print($_POST['link-heading']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['link-heading']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['link-heading']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__textarea-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="post-link">Ссылка <span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['post-link']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="post-link" type="text" name="post-link" placeholder="Введите ссылку" value="<? if(isset($_POST['post-link'])) {print($_POST['post-link']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['post-link']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['post-link']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
                <div class="adding-post__input-wrapper form__input-wrapper">
                    <label class="adding-post__label form__label" for="link-tags">Теги<span class="form__input-required">*</span></label>
                    <div class="form__input-section <? if($errors['link-tags']) {echo "form__input-section--error";} ?>">
                        <input class="adding-post__input form__input" id="link-tags" type="text" name="link-tags" placeholder="Введите теги" value="<? if(isset($_POST['link-tags'])) {print($_POST['link-tags']);} ?>">
                        <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span></button>
                        <div class="form__error-text">
                            <h3 class="form__error-title"><?=$errors['link-tags']['error_title'];?></h3>
                            <p class="form__error-desc"><?=$errors['link-tags']['error_desc'];?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (count($errors)) : ?>
            <div class="form__invalid-block">
                <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                <ul class="form__invalid-list">
                    <?php foreach ($errors as $k => $v) : ?>
                        <li class="form__invalid-item"><?=$v['field_name_rus']. '.' . ' ' . $v['error_title'] ;?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <div class="adding-post__buttons">
            <button class="adding-post__submit button button--main" type="submit">Опубликовать</button>
            <a class="adding-post__close" href="#">Закрыть</a>
        </div>
    </form>
</section>

<?php endif; ?>