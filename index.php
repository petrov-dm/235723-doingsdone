<?php
session_start();

// Управление отображением выполненных задач

$show_complete_tasks = isset($_SESSION['user']['show_complete_tasks']) ? $_SESSION['user']['show_complete_tasks'] : "";

// Подключение к БД и создание массивов для работы с ней

require_once('init.php');

// Подключаем функции

require_once('functions.php');

// Проверяем аутентификацию пользователя 
// $_SESSION['user']  - пользователь аутентифицирован, если эта сессия существует

if (!isset($_SESSION['user'])) {

// Выводим гостевую страницу, если пользователь не аутентифицирован  
// Выше header не должно быть вывода html или текста (иначе возникнет ошибка)      
    header('Location: guest.php');

} else {

// ========== ЭТО ВЫПОЛНЯЕТСЯ ПОСЛЕ АУТЕНТИФИКАЦИИ ======

// Обращаемся к таблице users для извлечения имени пользователя и его e-mail.   

    $user_data = getUsers($connect, isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : "");

// Обращаемся к таблице projects для получения списка проектов 

    $projects = getProjects($connect, isset($user_data['email']) ? $user_data['email'] : "");

// Обращаемся к таблице tasks для получения списка задач всех проектов текущего пользователя

    $tasks = getTasks($connect, $user_data);

// Вывод задач по выбранному проекту текущего пользователя 

    if (isset($_GET['project_id'])) {
        $projec_id = (int)$_GET['project_id'];
        $tasks = getTasksByProjectID($connect, $projec_id);

        // Управление отображением выполненных задач

        $show_complete_tasks = isset($_SESSION['user']['show_complete_tasks']) ? $_SESSION['user']['show_complete_tasks'] : "";
    }

// Управление отображением выполненных задач

    if (isset($_GET['show_completed'])) {
        if ((isset($_SESSION['user']['show_complete_tasks']) ? $_SESSION['user']['show_complete_tasks'] : "")
            === 0) {
            $_SESSION['user']['show_complete_tasks'] = 1;
            $show_complete_tasks = 1;
        } else {
            $_SESSION['user']['show_complete_tasks'] = 0;
            $show_complete_tasks = 0;
        }

    }

// Инвертируем статус выполнения задачи. Это событие происходит при щелчке по чекбоксу задачи. Меняем значение done в БД на противоположное.    

    if (isset($_GET['task_id'])) {
        if ($connect == false) {
            print ("Ошибка подключения: " . mysqli_connect_error());
        } else {

            // Устанавливаем кодировку

            mysqli_set_charset($connect, "utf8");

            // Запрос на получение строки из таблицы tasks по id задачи

            $safe_task_id = mysqli_real_escape_string($connect, $_GET['task_id']);
            $sql = "SELECT * FROM tasks WHERE id = " . $safe_task_id . ";";

            // Получаем объект результата, проверяем успешность результатов запроса

            $result = mysqli_query($connect, $sql);

            checkResult($result, $connect);

            // Преобразуем объект результата в массив

            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Инвертируем статус задачи. Зписываем его в массив $_GET
            (isset($rows[0]['done']) ? $rows[0]['done'] : "") == 1 ? $_GET['done'] = 0 : $_GET['done'] = 1;

            // Запрос на обновление поля done задачи

            $safe_task_id = mysqli_real_escape_string($connect, $_GET['task_id']);

            $safe_done = mysqli_real_escape_string($connect, $_GET['done']);

            $sql = "UPDATE tasks SET done = " . $safe_done . " WHERE id = " . $safe_task_id . ";";

            // Получаем объект результата, проверяем успешность результатов запроса

            $result = mysqli_query($connect, $sql);

            checkResult($result, $connect);

            header('Location: index.php');

        }
    }

// Блок фильтрации задач   

    if (isset($_GET['tasks_switch']) && $_GET['tasks_switch'] == 'today') {

        // Отображаем задачи на сегодня
        // С помощью функции date_task_exec определяем задачи на сегодня и помещаем их во временный массив

        $tmp_arr = [];
        foreach ($tasks as $key => $item) {
            if (date_task_exec(isset($item['date_planned']) ? $item['date_planned'] : "") == 'today') {
                $tmp_arr[$key] = (isset($tasks[$key]) ? $tasks[$key] : "");

            }
        }

        // В массиве $tasks оставляем только задачи на сегдня 

        unset($tasks);
        $tasks = $tmp_arr;
    }

    if (isset($_GET['tasks_switch']) && $_GET['tasks_switch'] == 'tomorrow') {

        // Отображаем задачи на завтра
        // С помощью функции date_task_exec определяем задачи на завтра и помещаем их во временный массив

        $tmp_arr = [];
        foreach ($tasks as $key => $item) {
            if (date_task_exec(isset($item['date_planned']) ? $item['date_planned'] : "") == 'make') {
                $tmp_arr[$key] = isset($tasks[$key]) ? $tasks[$key] : "";

            }
        }

        // В массиве $tasks оставляем только задачи на завтра 

        unset($tasks);
        $tasks = $tmp_arr;
    }

    if (isset($_GET['tasks_switch']) && $_GET['tasks_switch'] == 'overdue') {

        // Отображаем просроченные задачи 
        // С помощью функции date_task_exec определяем просроченные задачи и помещаем их во временный массив

        $tmp_arr = [];
        foreach ($tasks as $key => $item) {
            if ((date_task_exec(isset($item['date_planned']) ? $item['date_planned'] : "") == 'overdue') && ((isset($item['done']) ? $item['done'] : 2) == 0)) {
                $tmp_arr[$key] = $tasks[$key];

            }
        }

        // В массиве $tasks оставляем только задачи на сегдня 

        unset($tasks);
        $tasks = $tmp_arr;
    }

// Шаблоны

//Содержимое главной страницы 

    $page_content = include_template('index.php', ['tasks' => $tasks, 'show_complete_tasks' => $show_complete_tasks]);

// Содержимое главной страницы при нажатии кнопок "Добавить задачу" шаблона templates/layout.php

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['add_task'])) {
            $page_content = include_template('add.php', ['projects' => $projects]);
        }
    }

// Отрисовка всех страниц    

    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'projects' => $projects,
        'user_name' => $user_data['name'],
        'title' => 'Дела в порядке',
        'tasks' => $tasks
    ]);

    print ($layout_content);

}

?>
