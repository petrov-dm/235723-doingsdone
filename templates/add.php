<h2 class="content__main-heading">Добавление задачи</h2>

<form enctype="multipart/form-data" class="form" action="../add.php" method="post" name="add_task">
    <div class="form__row">
        <label class="form__label" for="name">Название <sup>*</sup></label>

        <!--    Отображаем ошибки валидации поля названия задачи-->

        <?php
        $classname = isset($errors['name']) ? "form__input--error" : "";
        $value = isset($task_value['name']) ? $task_value['name'] : "";
        $dict_info = isset($dict['name']) ? $dict['name'] : "";
        ?>

        <input class="form__input <?php print($classname); ?>" type="text" name="name" id="name"
               value="<?php print($value); ?>" placeholder="Введите название">
        <?php if (isset($dict['name']) && isset($errors['name'])) {
            print("<p class = 'form__message'>$dict_info</p>");
        } ?>
    </div>

    <div class="form__row">
        <label class="form__label" for="project">Проект</label>

        <!--    Отображаем ошибки валидации поля названия проекта-->

        <?php
        $classname = isset($errors['project']) ? "form__input--error" : "";
        $value = isset($task_value['project']) ? $task_value['project'] : "";
        $dict_info = isset($dict['project']) ? $dict['project'] : "";
        ?>

        <select class="form__input form__input--select <?php print($classname); ?>" name="project" id="project">
            <!--            Заполняем список проектов из БД-->
            <?php foreach ($projects as $key => $item): ?>
                <option value="<?php print(esc($item['id'])); ?>"><?php print(esc($item['name'])); ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($dict['project']) && isset($errors['project'])) {
            print("<p class = 'form__message'>$dict_info</p>");
        } ?>

    </div>

    <div class="form__row">
        <label class="form__label" for="date">Дата выполнения</label>

        <!--    Отображаем ошибки валидации поля названия даты-->

        <?php
        $classname = isset($errors['date']) ? "form__input--error" : "";
        $value = isset($task_value['date']) ? $task_value['date'] : "";
        $dict_info = isset($dict['date']) ? $dict['date'] : "";
        ?>

        <input class="form__input form__input--date <?php print($classname); ?>" type="date" name="date" id="date"
               value="<?php print($value); ?>"
               placeholder="Введите дату в формате ДД.ММ.ГГГГ">
        <?php if (isset($dict['date']) && isset($errors['date'])) {
            print("<p class = 'form__message'>$dict_info</p>");
        } ?>
    </div>

    <div class="form__row">
        <label class="form__label" for="preview">Файл</label>

        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="preview" id="preview" value="">

            <label class="button button--transparent" for="preview">
                <span>Выберите файл</span>
            </label>
        </div>
    </div>

    <div class="form__row form__row--controls">
        <input class="button" type="submit" name="" value="Добавить">
    </div>
</form>
