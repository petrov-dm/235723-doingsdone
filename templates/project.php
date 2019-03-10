<h2 class="content__main-heading">Добавление проекта</h2>

<form class="form" action="/project.php" method="post">
    <div class="form__row">
        <label class="form__label" for="project_name">Название <sup>*</sup></label>

        <!--    Выводим ошибки валидации поля "Название проекта"    -->
        <?php
        $classname = isset($errors['name']) ? "form__input--error" : "";
        $value = isset($project_value['name']) ? esc($project_value['name']) : "";
        $err_info = isset($errors['name']) ? $errors['name'] : "";
        ?>

        <input class="form__input <?= $classname ?>" type="text" name="name" id="project_name" value="<?= $value ?>"
               placeholder="Введите название проекта">
        <?php if (isset($errors['name'])): ?>
            <p class="form__message"><?= $err_info ?></p>
        <?php endif; ?>

    </div>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Добавить">
    </div>
</form>
