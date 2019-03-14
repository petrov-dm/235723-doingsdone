<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Проверяем аутентификацию пользователя 
// $_SESSION['user']  - пользователь аутентифицирован, если эта сессия существует

if (!isset($_SESSION['user'])) {

// Выводим гостевую страницу, если пользователь не аутентифицирован  
// Выше header не должно быть вывода html или текста (иначе возникнет ошибка)      
    header('Location: guest.php');

} else {

// ========== ЭТО ВЫПОЛНЯЕТСЯ ПОСЛЕ АУТЕНТИФИКАЦИИ ======

// При щелчке по логотипу сбрасываем в сессии массив задач выбранного проекта. После этого отображаются все задачи.

    if (isset($_GET['show_all'])) {
        if (isset($_SESSION['user']['tasksProjectID'])) {
            unset($_SESSION['user']['tasksProjectID']);
        }

    }

// Управление отображением выполненных задач по умолчанию

    $show_complete_tasks = isset($_SESSION['user']['show_complete_tasks']) ? $_SESSION['user']['show_complete_tasks'] : 0;

// Управление отображением выполненных задач кнопкой chekbox - для всех проектов

    if (isset($_GET['show_completed'])) {
        if ($_SESSION['user']['show_complete_tasks'] == 0) {
            $_SESSION['user']['show_complete_tasks'] = 1;
            $show_complete_tasks = $_SESSION['user']['show_complete_tasks'];
        } else {
            $_SESSION['user']['show_complete_tasks'] = 0;
            $show_complete_tasks = $_SESSION['user']['show_complete_tasks'];
        }
    }

// Подключение к БД и создание массивов для работы с ней

    require_once('init.php');

// Подключаем функции

    require_once('functions.php');

// Обращаемся к таблице users для извлечения имени пользователя и его e-mail.   

    $user_data = getUsers($connect, $_SESSION['user']['email']);

// Обращаемся к таблице projects для получения списка проектов 

    $projects = getProjects($connect, $user_data['email']);

// Обращаемся к таблице tasks для получения списка задач всех проектов текущего пользователя

    $tasks = getTasks($connect, $user_data);

// Содержимое главной страницы: по умолчанию отображаем все задачи всех проектов 

    $page_content = include_template('index.php', ['tasks' => $tasks, 'show_complete_tasks' => $show_complete_tasks]);

// Вывод задач по выбранному проекту текущего пользователя - по щелчку названия проекта

    if (isset($_GET['project_id'])) {

        $projec_id = (int)$_GET['project_id'];

        // Массив в сессии для хранения задач выбранного проекта

        $_SESSION['user']['tasksProjectID'] = [];
        $_SESSION['user']['tasksProjectID'] = getTasksByProjectID($connect, $projec_id);

        // Управление отображением выполненных задач. Считываем текущий режим отображения из сессии.

        $show_complete_tasks = $_SESSION['user']['show_complete_tasks'];

        //  Содержимое главной страницы: отображаем задачи выбранного проекта

        contentProjectId($tasks, $page_content, $show_complete_tasks);
    }

// Управление отображением выполненных задач кнопкой chekbox - для текущего выбранного проекта     

    if (isset($_GET['show_completed']) && isset($_SESSION['user']['tasksProjectID'])) {

        // Управление отображением выполненных задач

        $show_complete_tasks = $_SESSION['user']['show_complete_tasks'];

        //  Содержимое главной страницы: отображаем задачи выбранного проекта

        contentProjectId($tasks, $page_content, $show_complete_tasks);
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
            $rows[0]['done'] == 1 ? $_GET['done'] = 0 : $_GET['done'] = 1;


            // Запрос на обновление поля done задачи

            $safe_task_id = mysqli_real_escape_string($connect, $_GET['task_id']);

            $safe_done = mysqli_real_escape_string($connect, $_GET['done']);

            $sql = "UPDATE tasks SET done = " . $safe_done . " WHERE id = " . $safe_task_id . ";";

            // Получаем объект результата, проверяем успешность результатов запроса

            $result = mysqli_query($connect, $sql);

            checkResult($result, $connect);

            // Если выбран проект вызываем сценарий index.php?project_id= , чтобы отобразить только его задачи. В противном случае выводим все таски.

            isset($_SESSION['user']['tasksProjectID']) ? header('Location: index.php?project_id=' . $_SESSION['user']['tasksProjectID'][0]['project_id']) : header('Location: index.php');

        }
    }

// Блок фильтрации задач  

    if (isset($_GET['tasks_switch']) && $_GET['tasks_switch'] == 'all') {

        // Отображаем все задачи 
        // Определяем задачи  и помещаем их во временный массив

        $tmp_arr = [];

        // Проверка существования массива задач по выбранному проекту

        $tmp = isset($_SESSION['user']['tasksProjectID']) ? true : false;

        if (!$tmp) {

            // Если нет выбранного проекта выводим задачи всех проектов 

            $tmp_arr = $tasks;

        } else {

            // Если выбран проект, то выводим его задачи    

            $tmp_arr = $_SESSION['user']['tasksProjectID'];

        }

        // Выводим список задач на экран

        showFilter($tasks, $tmp_arr, $show_complete_tasks, $page_content);

    }

    if (isset($_GET['tasks_switch']) && $_GET['tasks_switch'] == 'today') {

        // Отображаем задачи на сегодня
        // С помощью функции date_task_exec определяем задачи на сегодня и помещаем их во временный массив

        $tmp_arr = [];

        // Проверка существования массива задач по выбранному проекту

        $tmp = isset($_SESSION['user']['tasksProjectID']) ? true : false;

        if (!$tmp) {

            // Если нет выбранного проекта выводим задачи всех проектов

            foreach ($tasks as $key => $item) {
                (date_task_exec($item['date_planned']) == 'today') ? $tmp_arr[$key] = $tasks[$key] : "";
            }
        } else {

            // Если выбран проект, то выводим его задачи

            foreach ($_SESSION['user']['tasksProjectID'] as $key => $item) {
                (date_task_exec($item['date_planned']) == 'today') ? $tmp_arr[$key] = $_SESSION['user']['tasksProjectID'][$key] : "";
            }

        }

        // В массиве $tasks оставляем только задачи на сегодня 

        showFilter($tasks, $tmp_arr, $show_complete_tasks, $page_content);
    }

    if (isset($_GET['tasks_switch']) && $_GET['tasks_switch'] == 'tomorrow') {

        // Отображаем задачи на завтра
        // С помощью функции date_task_exec определяем задачи на завтра и помещаем их во временный массив

        $tmp_arr = [];

        // Проверка существования массива задач по выбранному проекту

        $tmp = isset($_SESSION['user']['tasksProjectID']) ? true : false;

        if (!$tmp) {

            // Если нет выбранного проекта выводим задачи всех проектов

            foreach ($tasks as $key => $item) {
                (date_task_exec($item['date_planned']) == 'make') ? $tmp_arr[$key] = $tasks[$key] : "";
            }
        } else {

            // Если выбран проект, то выводим его задачи

            foreach ($_SESSION['user']['tasksProjectID'] as $key => $item) {
                (date_task_exec($item['date_planned']) == 'make') ? $tmp_arr[$key] = $_SESSION['user']['tasksProjectID'][$key] : "";
            }

        }

        // В массиве $tasks оставляем только задачи на завтра 

        showFilter($tasks, $tmp_arr, $show_complete_tasks, $page_content);
    }

    if (isset($_GET['tasks_switch']) && $_GET['tasks_switch'] == 'overdue') {

        // Отображаем просроченные задачи 
        // С помощью функции date_task_exec определяем просроченные задачи и помещаем их во временный массив

        $tmp_arr = [];

        // Проверка существования массива задач по выбранному проекту

        $tmp = isset($_SESSION['user']['tasksProjectID']) ? true : false;

        if (!$tmp) {

            // Если нет выбранного проекта выводим задачи всех проектов

            foreach ($tasks as $key => $item) {
                ((date_task_exec($item['date_planned']) == 'overdue') && ($item['done'] == 0)) ? $tmp_arr[$key] = $tasks[$key] : "";
            }
        } else {

            // Если выбран проект, то выводим его задачи

            foreach ($_SESSION['user']['tasksProjectID'] as $key => $item) {

                ((date_task_exec($item['date_planned']) == 'overdue') && ($item['done'] == 0)) ? $tmp_arr[$key] = $_SESSION['user']['tasksProjectID'][$key] : "";
            }

        }

        // В массиве $tasks оставляем только просроченные задачи  

        showFilter($tasks, $tmp_arr, $show_complete_tasks, $page_content);

    }

// Шаблоны

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
