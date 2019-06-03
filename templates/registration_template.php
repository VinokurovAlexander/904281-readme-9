<main class="page__main page__main--registration">
    <div class="container">
        <h1 class="page__title page__title--registration">Регистрация</h1>
    </div>
    <section class="registration container">
        <h2 class="visually-hidden">Форма регистрации</h2>
        <form class="registration__form form" action="/registration.php" method="post" enctype="multipart/form-data">
            <div class="form__text-inputs-wrapper">
                <div class="form__text-inputs">
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-email">Электронная почта <span
                                    class="form__input-required">*</span></label>
                        <div class="form__input-section <? if ($errors['email']) {
                            echo "form__input-section--error";
                        } ?>">
                            <input class="registration__input form__input" id="registration-email" type="email"
                                   name="email" placeholder="Укажите эл.почту" value="<? if (isset($_POST['email'])) {
                                print($_POST['email']);
                            } ?>">
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                            </button>
                            <div class="form__error-text">
                                <h3 class="form__error-title"><?= $errors['email']['field-rus']; ?></h3>
                                <p class="form__error-desc"><?= $errors['email']['error-desc']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-login">Логин <span
                                    class="form__input-required">*</span></label>
                        <div class="form__input-section <? if ($errors['login']) {
                            echo "form__input-section--error";
                        } ?>">
                            <input class="registration__input form__input" id="registration-login" type="text"
                                   name="login" placeholder="Укажите логин" value="<? if (isset($_POST['login'])) {
                                print($_POST['login']);
                            } ?>">
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                            </button>
                            <div class="form__error-text">
                                <h3 class="form__error-title"><?= $errors['login']['field-rus']; ?></h3>
                                <p class="form__error-desc"><?= $errors['login']['error-desc']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-password">Пароль<span
                                    class="form__input-required">*</span></label>
                        <div class="form__input-section <? if ($errors['password']) {
                            echo "form__input-section--error";
                        } ?>">
                            <input class="registration__input form__input" id="registration-password" type="password"
                                   name="password" placeholder="Придумайте пароль"
                                   value="<? if (isset($_POST['password'])) {
                                       print($_POST['password']);
                                   } ?>">
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                            </button>
                            <div class="form__error-text">
                                <h3 class="form__error-title"><?= $errors['password']['field-rus']; ?></h3>
                                <p class="form__error-desc"><?= $errors['password']['error-desc']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="registration__input-wrapper form__input-wrapper">
                        <label class="registration__label form__label" for="registration-password-repeat">Повтор
                            пароля<span class="form__input-required">*</span></label>
                        <div class="form__input-section <? if ($errors['password-repeat']) {
                            echo "form__input-section--error";
                        } ?>">
                            <input class="registration__input form__input" id="registration-password-repeat"
                                   type="password" name="password-repeat" placeholder="Повторите пароль"
                                   value="<? if (isset($_POST['password-repeat'])) {
                                       print($_POST['password-repeat']);
                                   } ?>">
                            <button class="form__error-button button" type="button">!<span class="visually-hidden">Информация об ошибке</span>
                            </button>
                            <div class="form__error-text">
                                <h3 class="form__error-title"><?= $errors['password-repeat']['field-rus']; ?></h3>
                                <p class="form__error-desc"><?= $errors['password-repeat']['error-desc']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="registration__textarea-wrapper form__textarea-wrapper">
                        <label class="registration__label form__label" for="text-info">Информация о себе</label>
                        <div class="form__input-section">
                            <textarea class="registration__textarea form__textarea form__input" id="text-info"
                                      name="about_me"
                                      placeholder="Коротко о себе в свободной форме"><? if (isset($_POST['about-me'])) {
                                    print($_POST['about-me']);
                                } ?></textarea>
                        </div>
                    </div>
                </div>
                <?php if (count($errors)) : ?>
                    <div class="form__invalid-block">
                        <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                        <ul class="form__invalid-list">
                            <?php foreach ($errors as $v) : ?>
                                <li class="form__invalid-item"><?= $v['field-rus'] . '.' . ' ' . $v['error-title']; ?></li>
                            <?php endforeach; ?>

                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="registration__input-file-container form__input-container form__input-container--file">
                <div class="registration__input-file-wrapper form__input-file-wrapper">
                    <div class="registration__file-zone form__file-zone dropzone">
                        <input class="registration__input-file form__input-file" id="userpic-file" type="file"
                               name="userpic-file" title=" ">
                        <div class="form__file-zone-text">
                            <span>Перетащите фото сюда</span>
                        </div>
                    </div>
                    <button class="registration__input-file-button form__input-file-button button" type="button">
                        <span>Выбрать фото</span>
                        <svg class="registration__attach-icon form__attach-icon" width="10" height="20">
                            <use xlink:href="#icon-attach"></use>
                        </svg>
                    </button>
                </div>
                <div class="registration__file form__file dropzone-previews">

                </div>
            </div>
            <button class="registration__submit button button--main" type="submit">Отправить</button>
        </form>
    </section>
</main>

