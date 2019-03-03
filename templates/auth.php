<div class="content">

    <section class="content__side">
        <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

        <a class="button button--transparent content__side-button" href="auth.php">Войти</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Вход на сайт</h2>

        <form class="form" action="/auth.php" method="post">

            <!--    Выводим ошибки валидации поля email                     -->

            <?php
            $classname = isset($errors['email']) ? "form__input--error" : "";
            $value = isset($user_value['email']) ? esc($user_value['email']) : "";
            $err_info = isset($errors['email']) ? $errors['email'] : "";
            ?>

            <div class="form__row">
                <label class="form__label" for="email">E-mail <sup>*</sup></label>

                <input class="form__input <?= $classname ?>" type="text" name="email" id="email" value="<?= $value ?>"
                       placeholder="Введите e-mail">

                <p class="form__message"><?= $err_info ?></p>
            </div>

            <!--    Выводим ошибки валидации поля password                     -->

            <?php
            $classname = isset($errors['password']) ? "form__input--error" : "";
            $value = isset($user_value['password']) ? esc($user_value['password']) : "";
            $err_info = isset($errors['password']) ? $errors['password'] : "";
            ?>

            <div class="form__row">
                <label class="form__label" for="password">Пароль <sup>*</sup></label>

                <input class="form__input <?= $classname ?>" type="password" name="password" id="password"
                       value="<?= $value ?>" placeholder="Введите пароль">

                <p class="form__message"><?= $err_info ?></p>
            </div>

            <div class="form__row form__row--controls">
                <input class="button" type="submit" name="" value="Войти">
            </div>
        </form>

    </main>

</div>
