<?php

session_start();

// Подключение к БД и создание массивов для работы с ней

require_once('init.php');

// Подключаем функции

require_once('functions.php');

// Обращаемся к таблице users для извлечения имени пользователя и его e-mail. 

$user_data = getUsers($connect, isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : "");

// Обращаемся к таблице projects для получения списка проектов 

$projects = getProjects($connect, isset($user_data['email']) ? $user_data['email'] : ""
);

// Считываем список задач пользователя

$tasks = getTasks($connect, $user_data);

// Проверяем что сценарий вызван методом POST формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Подтверждение того, что метод вызван формой, вводящей новую задачу  
    if (isset($_POST['preview']) or isset($_FILES['preview'])) {

// Валидация введенных данных
// Массив с введенными значениями полей (копируем из $_POST)

        $task_value = $_POST;

// Массив обязательных полей

        $required = ['name'];

//Cписок полей, для валидации. Описание полей

        $dict = [
            'name' => 'Введите корректное название задачи',
            'project' => 'Введите корректное название проекта',
            'date' => 'Проверьте формат. Дата не должна быть прошедшей'
        ];

// Массив для хранения ошибок валидации

        $errors = [];

// Проверяем заполнение обязательных полей
        foreach ($required as $key) {
            if (empty(isset($_POST[$key]) ? $_POST[$key] : "")) {
                $errors[$key] = 'Это поле необходимо заполнить';
            }
        }

// Проверка существования проекта. SQL - запрос в таблицу projects. Если результат пустой - проект не существует, ошибка 

// Проверяем результат подключения

        if ($connect == false) {
            print ("Ошибка подключения: " . mysqli_connect_error());
        } else {
            if (isset($_POST['project'])) {

// Устанавливаем кодировку

                mysqli_set_charset($connect, "utf8");

// Таблица projects: формируем запрос на получение списка проектов по e-mail пользователя выбранного из таблицы users 

                $safe_project = mysqli_real_escape_string($connect, isset($_POST['project']) ? $_POST['project'] : "");

                $sql = "SELECT * FROM projects WHERE projects.id = $safe_project";

// Получаем объект результата, проверяем успешность результатов запроса

                $result = mysqli_query($connect, $sql);

                checkResult($result, $connect);

//  Считаем количество возвращенных записей, если 0, то id проекта нет в БД    

                $row_cnt = mysqli_num_rows($result);
            }
        }

// Фиксация ошибки выбора проекта

        if ($row_cnt == 0) {
            $errors['project'] = 'Проект не существует';
        }

// Проверка формата даты ДД.ММ.ГГГГ
        if ((isset($_POST['date']) ? $_POST['date'] : ""
            ) != "") {
            if (!is_valid_date(trim($_POST['date']))) {
                $errors['date'] = 'Ошибка ввода даты';
            }
        }

// Проверка: дата не должна быть прошедшей  
        if ((isset($_POST['date']) ? $_POST['date'] : ""
            ) != "") {
            if (date_task_exec($_POST['date']) == 'overdue') {
                $errors['date'] = 'Ошибка ввода даты';
            }
        }

// Проверяем результаты валидации. 

        if ((int)count($errors) != 0) {
            // Если получены ошибки валидации, загружаем шаблон templates/add.php, выводим в него найденные ошибки

            $page_content = include_template('add.php',
                ['projects' => $projects, 'task_value' => $task_value, 'dict' => $dict, 'errors' => $errors]);
            $layout_content = include_template('layout.php', [
                'content' => $page_content,
                'projects' => $projects,
                'user_name' => $user_data['name'],
                'title' => 'Дела в порядке',
                'tasks' => $tasks
            ]);
            print ($layout_content);
            exit();

        } else {
            //Ветка при отсутствии ошибок валидации
            //Если пользователь выбрал файл загружаем его в папку uploads
            if (isset($_FILES['preview']) && ((isset($_FILES['preview']['name']) ? $_FILES['preview']['name'] : "") != "")) {

                $file_name = $_FILES['preview']['name'];
                $file_path = __DIR__;
                $file_url = "uploads\\" . $file_name;
                move_uploaded_file($_FILES['preview']['tmp_name'], $file_path . '\\uploads\\' . $file_name);
            }

            // Записываем задачу в БД.
            // Устанавливаем кодировку

            mysqli_set_charset($connect, "utf8");

            // Формируем запрос на добавление данных. Для защиты от SQL-инъекций используем функцию mysqli_real_escape_string

            $safe_user_id = mysqli_real_escape_string($connect, isset($user_data['id']) ? $user_data['id'] : "");

            $safe_project_id = mysqli_real_escape_string($connect, isset($_POST['project']) ? $_POST['project'] : "");

            $safe_done = mysqli_real_escape_string($connect, 0);

            $safe_name = mysqli_real_escape_string($connect, isset($_POST['name']) ? $_POST['name'] : "");

            $tmp = isset($file_url) ? $file_url : "";
            $safe_file = mysqli_real_escape_string($connect, $tmp);

            $safe_date_planned = mysqli_real_escape_string($connect,
                (isset($_POST['date']) && ((isset($_POST['date']) ? $_POST['date'] : "") != "")) ? date_Ymd($_POST['date']) : "");

            $sql = "INSERT INTO tasks SET user_id = '$safe_user_id', project_id = '$safe_project_id', done = '$safe_done', name = '$safe_name', file = '$safe_file', date_planned = '$safe_date_planned'";

            // Если дата выполнения пустая - исключаем ее из запроса
            if ($safe_date_planned == '') {
                $sql = "INSERT INTO tasks SET user_id = '$safe_user_id', project_id = '$safe_project_id', done = '$safe_done', name = '$safe_name', file = '$safe_file'";
            }

            // Выполняем запрос

            $result = mysqli_query($connect, $sql);

            checkResult($result, $connect);

            // После успешной записи в БД преходим на основную страницу

            header('Location: index.php');

        }
    }
}

// Этот фрагмент выполняется при вызове сценария add.php, при нажатии кнопки "Добавить задачу" на layout 

$page_content = include_template('add.php', ['projects' => $projects]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'projects' => $projects,
    'user_name' => $user_data['name'],
    'title' => 'Дела в порядке',
    'tasks' => $tasks
]);
print ($layout_content);




