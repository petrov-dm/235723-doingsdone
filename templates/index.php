<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/index.php?tasks_switch=all" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
        <a href="/index.php?tasks_switch=today" class="tasks-switch__item">Повестка дня</a>
        <a href="/index.php?tasks_switch=tomorrow" class="tasks-switch__item">Завтра</a>
        <a href="/index.php?tasks_switch=overdue" class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input class="checkbox__input visually-hidden show_completed"
               type="checkbox" <?php if ($show_complete_tasks == 1): ?> checked
        <?php endif; ?> >
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>
<table class="tasks">
    <!--            Вывод списка задач-->
    <?php foreach ($tasks as $key => $item): ?>
        <?php if (!(( (isset($item['done']) ? $item['done'] : null) == 1) && ($show_complete_tasks == 0))): ?>
            <!--  Оповещение о необходимости выполнить задачу <= 24ч.  -->
            <tr class="tasks__item task <?php if ( (isset($item['done']) ? $item['done'] : null) == 1): ?> task--completed <?php endif; ?> <?php if (date_task_exec(date_dmY(isset($item['date_planned']) ? $item['date_planned'] : null)) == 'make'): ?> task--important <?php endif; ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox"
                               value="<?php isset($item['id']) ? print(esc($item['id'])) : print(''); ?>">
                        <!--            Отмечаем просроченные задачи    -->
                        <span
                            class="checkbox__text" <?php if (date_task_exec(date_dmY(isset($item['date_planned']) ? $item['date_planned'] : "")) == 'overdue' or date_task_exec(date_dmY(isset($item['date_planned']) ? $item['date_planned'] : "")) == 'today'): ?> style="color:red;"
                        <?php endif; ?>>
                    <!-- Фильтрация названия задачи-->
                    <?= esc(isset($item['name']) ? $item['name'] : "") ?>
                </span>
                    </label>
                </td>

                <td class="task__file">
                    <a class="<?php if (isset($item['file'])) { !empty($item['file']) ? print("download-link") : print(""); } ?>" href="<?php if (isset($item['file'])) {
                        print(esc($item['file']));
                    } ?>"><?php if (isset($item['file'])) {
                            print(esc($item['file']));
                        } ?></a>
                </td>

                <td class="task__date">
                    <!-- Фильтрация даты задачи-->
                    <?= esc(date_dmY(isset($item['date_planned']) ? $item['date_planned'] : null)) ?>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    <!--показывать следующий тег <tr/>, если переменная $show_complete_tasks равна единице-->
    <?php if ($show_complete_tasks == 1): ?>
        <tr class="tasks__item task task--completed">
            <td class="task__select">
                <label class="checkbox task__checkbox">
                    <input class="checkbox__input visually-hidden" type="checkbox" checked>
                    <span class="checkbox__text">Записаться на интенсив "Базовый PHP"</span>
                </label>
            </td>
            <td class="task__date">10.10.2019</td>

            <td class="task__controls">
            </td>
        </tr>
    <?php endif; ?>
</table>
