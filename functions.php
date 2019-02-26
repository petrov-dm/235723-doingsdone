<?php

// Функция шаблонизации

function include_template($name, $data)
{
    $name = 'templates/' . $name;
    $result = '';
    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();
    return $result;
}

// Функция подсчета задач

function count_tasks($t, $p)
{
    $count = 0;

    foreach ($t as $key => $item) {

        if ((int)$item['project_id'] == (int)$p) {
            $count++;
        }
    }
    return $count;
}

// Функция фильтрации

function esc($str)
{
    $text = htmlspecialchars($str);

    // $text = strip_tags($str);

    return $text;
}

// Функция проверки даты выпонения задачи

function date_task_exec($d)
{
    $result = ''; // значение по умолчанию
    date_default_timezone_set('Asia/Yekaterinburg'); // Мой часовой пояс

    // метка текущего времени

    $cur_date = time();

    // метка времени задачи

    $task_date = strtotime($d);

    // У задачи нет даты

    if ((bool)$task_date === false) {
        return $result;
    }

    // Перевод в часы

    $cur_date = floor($cur_date / 3600);
    $task_date = floor($task_date / 3600);
    $diff = $task_date - $cur_date;
    if ($diff > 0) {
        if ($diff <= 24) {
            $result = 'make';
        } // до выполнения менее или 24ч.
    } else {
        $result = 'overdue';
    } //просроченное дело
    return $result;
}

// Функция преобразования даты в формат d-m-Y. Используется при чтении дат из БД
function date_dmY($date)
{
    if (isset($date)) {
        return date("d-m-Y", strtotime($date));
    }
}

// Функция преобразования даты в формат Y-m-d. Используется при записи дат в БД
function date_Ymd($date)
{
    if (isset($date) && ($date != "")) {
        return date("Y-m-d", strtotime($date));
    }
    return true;
}

// Функция проверки корректности формата даты ДД.ММ.ГГГГ

function is_valid_date($date)
{
    return preg_match('/^(\\d{2})\\.(\\d{2})\\.(\\d{4})$/', $date, $m)
        && checkdate($m[2], $m[1], $m[3]);
}

// Функции работы с БД
// Подключение к таблице users. Параметр $email - адрес эл. почты пользователя, полученный из формы

function getUsers($con, $email)
{
    // Проверяем результат подключения

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // ТАБЛИЦА USERS
        // Если введенный из формы e-mail есть в базе - получаем имя пользователя и e-mail из таблицы БД. Далее используем их для получения информации из таблиц users, tasks

        $sql = "SELECT * FROM users WHERE users.email = '" . trim($email) . "';";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);
        if ($result == false) {
            $error = mysqli_error($con);
            print ("Ошибка MySQL: " . $error);
        }

        // Преобразуем объект результата в массив

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Возвращаем массив [имя полльзователя, e-mail]

        return $rows[0];
    }
}

// Подключение к таблице proejcts. Параметр $email - эл. почта пользователя, полученная из таблицы users БД

function getProjects($con, $email)
{

    // Проверяем результат подключения

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // Таблица projects: формируем запрос на получение списка проектов по e-mail пользователя выбранного из таблицы users 

        $sql = "SELECT * FROM projects WHERE projects.user_id = (SELECT id FROM users WHERE users.email = '" . trim($email) . "');";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);
        if ($result == false) {
            $error = mysqli_error($con);
            print ("Ошибка MySQL: " . $error);
        }

        // Преобразуем объект результата в массив

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Возвращаем список проектов

        return $rows;

    }
}

// Подключение к таблице tasks. Выборка задач по всем проектам пользователя с идентификатором $user_id 

function getTasks($con, $user_id)
{

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // Запрос для вывода задач по всем проектам пользователя

        $sql = "SELECT * FROM tasks WHERE user_id = " . $user_id['id'] . ";";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);

        if ($result == false) {
            $error = mysqli_error($con);
            print ("Ошибка MySQL: " . $error);
        }

        // Преобразуем объект результата в массив 

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Возвращаем массив задач

        return $rows;
    }
}

// Выборка задач по выбранному проекту

function getTasksByProjectID($con, $proj_id)
{

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // Запрос для вывода задач по id выбранного проекта

        $sql = "SELECT * FROM tasks WHERE project_id = " . $proj_id . ";";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);

        if ($result == false) {
            $error = mysqli_error($con);
            print ("Ошибка MySQL: " . $error);
        }

        // Преобразуем объект результата в массив 

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Возвращаем код ответа 404 вместо содержимого страницы, если параметр запроса отсутствует, либо если по этому id не нашли ни одной записи.

        if (empty($rows)) {
            http_response_code(404);
        }

        // Возвращаем массив задач

        return $rows;
    }
}

?>
