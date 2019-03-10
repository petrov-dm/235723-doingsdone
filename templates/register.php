<div class="content">


    <section class="content__side">
        <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

        <a class="button button--transparent content__side-button" href="auth.php">Войти</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Регистрация аккаунта</h2>

        <form class="form" action="register.php" method="post">
            <div class="form__row">
                <label class="form__label" for="email">E-mail <sup>*</sup></label>

                <!--    Выводим ошибки валидации поля e-mail                     -->

                <?php
                $classname = isset($errors['email']) ? "form__input--error" : "";
                $value = isset($user_value['email']) ? esc($user_value['email']) : "";
                $err_info = isset($errors['email']) ? $errors['email'] : "";
                ?>

                <input class="form__input <?= $classname ?>" type="text" name="email" id="email" value="<?= $value ?>"
                       placeholder="Введите e-mail">

                <p class="form__message"><?= $err_info ?></p>
            </div>

            <div class="form__row">
                <label class="form__label" for="password">Пароль <sup>*</sup></label>

                <!--    Выводим ошибки валидации поля пароль                     -->

                <?php
                $classname = isset($errors['password']) ? "form__input--error" : "";
                $value = isset($user_value['password']) ? esc($user_value['password']) : "";
                $err_info = isset($errors['password']) ? $errors['password'] : "";
                ?>
                <input class="form__input <?= $classname ?>" type="password" name="password" id="password"
                       value="<?= $value ?>"
                       placeholder="Введите пароль">
                <?php if (isset($errors['password'])): ?>
                    <p class="form__message"><?= $err_info ?></p>
                <?php endif; ?>
            </div>

            <div class="form__row">
                <label class="form__label" for="name">Имя <sup>*</sup></label>

                <!--    Выводим ошибки валидации поля имя                     -->

                <?php
                $classname = isset($errors['name']) ? "form__input--error" : "";
                $value = isset($user_value['name']) ? esc($user_value['name']) : "";
                $err_info = isset($errors['name']) ? $errors['name'] : "";
                ?>
                <input class="form__input <?= $classname ?>" type="text" name="name" id="name" value="<?= $value ?>"
                       placeholder="Введите имя">
                <?php if (isset($errors['name'])): ?>
                    <p class="form__message"><?= $err_info ?></p>
                <?php endif; ?>
            </div>

            <div class="form__row form__row--controls">
                <?php if (!empty($errors)): ?>
                    <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
                <?php endif; ?>

                <input class="button" type="submit" name="" value="Зарегистрироваться">
            </div>
        </form>
    </main>


</div>

