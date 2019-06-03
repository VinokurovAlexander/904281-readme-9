<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
        <h2 class="visually-hidden">Сообщения</h2>
        <div class="messages__contacts">
            <ul class="messages__contacts-list tabs__list">
                <?php foreach ($dialogs as $dialog) : ?>
                    <li class="messages__contacts-item">
                        <a class="messages__contacts-tab tabs__item <?php if (isset($_GET['user_id']) && $_GET['user_id'] == get_dialog_user_id($con, $dialog)) {
                            echo 'messages__contacts-tab--active ';
                        } ?>" href="/messages.php/?user_id=<?= get_dialog_user_id($con, $dialog) ?>">
                            <div class="messages__avatar-wrapper">
                                <img class="messages__avatar" src="<?= get_dialog_avatar($con, $dialog) ?>"
                                     alt="Аватар пользователя">
                                <?php if (get_dialog_unread_msg_cnt($con, $dialog['dialog_name']) > 0) : ?>
                                    <i class="messages__indicator"><?= get_dialog_unread_msg_cnt($con, $dialog['dialog_name']) ?></i>
                                <? endif; ?>
                            </div>
                            <div class="messages__info">
                                <span class="messages__contact-name"><?= get_dialog_username($con, $dialog) ?></span>
                                <div class="messages__preview">
                                    <p class="messages__preview-text">
                                        <?php if ($dialog['sen_id'] == $_SESSION['user']['user_id']) {
                                            echo 'Вы: ';
                                        } ?>
                                        <?= htmlspecialchars($dialog['content']) ?>
                                    </p>
                                    <time class="messages__preview-time" datetime="<?= $dialog['pub_date'] ?>">
                                        <?= get_message_time($dialog['pub_date']) ?>
                                    </time>
                                </div>
                            </div>
                        </a>
                    </li>
                <? endforeach; ?>
            </ul>
        </div>

        <div class="messages__chat">
            <div class="messages__chat-wrapper">
                <ul class="messages__list tabs__content tabs__content--active">
                    <?php foreach ($messages as $message) : ?>
                        <li class="messages__item <?php if ($message['sen_id'] == $_SESSION['user']['user_id']) {
                            echo 'messages__item--my';
                        } ?>">
                            <div class="messages__info-wrapper">
                                <div class="messages__item-avatar">
                                    <a class="messages__author-link"
                                       href="/profile.php/?user_id=<?= $message['sen_id'] ?>">
                                        <img class="messages__avatar" src="<?= $message['avatar_path'] ?>"
                                             alt="Аватар пользователя">
                                    </a>
                                </div>
                                <div class="messages__item-info">
                                    <a class="messages__author" href="/profile.php/?user_id=<?= $message['sen_id'] ?>">
                                        <?= htmlspecialchars($message['user_name']) ?>
                                    </a>
                                    <time class="messages__time" datetime="<?= $message['pub_date'] ?>">
                                        <?= rel_time($message['pub_date']) ?> назад
                                    </time>
                                </div>
                            </div>
                            <p class="messages__text">
                                <?= htmlspecialchars($message['content']) ?>
                            </p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if (isset($_GET['user_id']) && !empty($_GET['user_id'])) : ?>
            <div class="comments">
                <form class="comments__form form" action="/messages.php/?user_id=<?= $_GET['user_id'] ?>" method="post">
                    <div class="comments__my-avatar">
                        <img class="comments__picture" src="<?= $_SESSION['user']['avatar_path']; ?>"
                             alt="Аватар пользователя">
                    </div>
                    <textarea class="comments__textarea form__textarea <?php if (isset($errors['message-text'])) {
                        echo 'message-text-error';
                    } ?>"
                              placeholder="<?php if (isset($errors['message-text'])) {
                                  echo $errors['message-text'];
                              } else {
                                  echo 'Введите ваше сообщение';
                              } ?>" name="message-text"></textarea>
                    <label class="visually-hidden">Ваше сообщение</label>
                    <button class="comments__submit button button--green" type="submit">Отправить</button>
                </form>
            </div>

        </div>
        <? endif; ?>
    </section>
</main>
