<?php
session_start();

// Подключение к БД и создание массивов для работы с ней

require_once('init.php');

// Подключаем функции

require_once('functions.php');


// Проверяем аутентификацию пользователя 

// $_SESSION['user_data'] = $user_data; - пользователь аутентифицирован

// unset($_SESSION); - пользователь не аутентифицирован

if (!isset($_SESSION['user_data'])) {

// ========== ЭТО ВЫПОЛНЯЕТСЯ ДО АУТЕНТИФИКАЦИИ ======

// Выводим страницу регистрации, если пользователь не аутентифицирован  
// Выше и ниже header не должно быть вывода html или текста (иначе возникнет ошибка)      

    header('Location: register.php');


} else {

// ========== ЭТО ВЫПОЛНЯЕТСЯ ПОСЛЕ АУТЕНТИФИКАЦИИ ======


// Получаем e-mail пользователя из формы. Используем функцию фильтрации esc().

    $email_form = esc("ivan@mail.ru"); // - вместо этого $user_data['email'];


// Обращаемся к таблице users для извлечения имени пользователя и его e-mail. Значение переменной $email_form используется для поиска в таблице БД.   

    $user_data = getUsers($connect, $email_form);

// Обращаемся к таблице projects для получения списка проектов 

    $projects = getProjects($connect, $user_data['email']);

// Вывод задач по выбранному проекту текущего пользователя 

    if (isset($_GET['project_id'])) {
        $projec_id = (int)$_GET['project_id'];
        $tasks = getTasksByProjectID($connect, $projec_id);
    } else {
        // Обращаемся к таблице tasks для получения списка задач всех проектов текущего пользователя
        $tasks = getTasks($connect, $user_data);
    }

// Шаблоны

//Содержимое главной страницы по умолчанию - вывод всех задач авторизованного пользователя

    $page_content = include_template('index.php', ['tasks' => $tasks, 'show_complete_tasks' => $show_complete_tasks]);

// Содержимое главной страницы при нажатии кнопок "Добавить задачу" шаблона templates/layout.php

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['add_task'])) {
            $page_content = include_template('add.php', ['projects' => $projects]);
        }
    }


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
