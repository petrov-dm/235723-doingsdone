<?php

session_start();

// Пользователь аутентифицирован. Считываем из БД информацию о нем

if (isset($_SESSION['user'])) {

    // Подключение к БД и создание массивов для работы с ней

    require_once('init.php');

    // Подключаем функции

    require_once('functions.php');

    // Обращаемся к таблице users для извлечения имени пользователя и его e-mail. 

    $user_data = getUsers($connect, isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : "");

    // Обращаемся к таблице projects для получения списка проектов 

    $projects = getProjects($connect, isset($user_data['email']) ? $user_data['email'] : "");

    // Считываем список задач пользователя

    $tasks = getTasks($connect, $user_data);

}

// Пользователь аутентифицирован. Сценарий вызван через POST - кнопка "Добавить"

if (isset($_SESSION['user']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {

    // Валидация введенных данных
    // Массив с введенными значениями полей (копируем из $_POST)

    $project_value = $_POST;

    // Массив обязательных полей

    $required = ['name'];

    // Массив для хранения ошибок валидации

    $errors = [];

    // Проверяем заполнение обязательных полей

    foreach ($required as $key) {
        if (empty(isset($_POST[$key]) ? $_POST[$key] : "")) {
            $errors[$key] = 'Это поле необходимо заполнить';
        }
    }

    // Если поле заполнено, проверяем существование названия проекта в базе

    if (empty($errors['name'])) {

        // Поскольку пользователь аутентифицирован - получен список проектов в массиве $projects. Проверяем существование в базе вводимого названия проекта
        foreach ($projects as $key => $item) {
            if (trim(isset($_POST['name']) ? $_POST['name'] : "") == (isset($item['name']) ? $item['name'] : "-")) {
                $errors['name'] = $errors['name'] . 'Такой проект уже существует. ';
            }
        }
    }

    // Проверяем результаты валидации. 

    if ((int)count($errors) == 0) {

        // Ошибок нет. Записываем информацию в БД. Перенаправляем пользователя на главную страницу

        // Проверяем результат подключения

        if ($connect == false) {
            print ("Ошибка подключения: " . mysqli_connect_error());
        } else {
            // Устанавливаем кодировку

            mysqli_set_charset($connect, "utf8");

            // Формируем SQL - запрос на добавление информации в БД 

            $safe_user_id = mysqli_real_escape_string($connect, isset($user_data['id']) ? $user_data['id'] : "");

            $safe_project = mysqli_real_escape_string($connect, isset($_POST['name']) ? $_POST['name'] : "");

            $sql = "INSERT INTO projects SET user_id = '$safe_user_id', name = '$safe_project'";

            // Выполняем запрос

            $result = mysqli_query($connect, $sql);

            // Проверяем успешность выполнения запроса

            checkResult($result, $connect);

        }

        // После успешного добавления проекта в БД переадресовываем пользователя на главную страницу  

        header('Location: index.php');

    } else {

        // Если есть ошибки валидации вызываем сценарий project.php и выводим их под полями

        $page_content = include_template('project.php', ['project_value' => $project_value, 'errors' => $errors]);

        $layout_content = include_template('layout.php', [
            'content' => $page_content,
            'projects' => $projects,
            'user_name' => $user_data['name'],
            'title' => 'Дела в порядке',
            'tasks' => $tasks
        ]);
        print ($layout_content);
        exit();
    }
}

// Пользователь аутентифицирован. Сценарий вызван через GET - кнопка "Добавить проект"

if (isset($_SESSION['user'])) {
    $page_content = include_template('project.php', []);

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
