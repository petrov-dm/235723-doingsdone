<?php

// Функция шаблонизации
/**
 * @param $name
 * @param $data
 * @return false|string
 */
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
/**
 * @param $t
 * @param $p
 * @return int
 */
function count_tasks($t, $p)
{
    $count = 0;

    foreach ($t as $key => $item) {

        if ((int)(isset($item['project_id']) ? $item['project_id'] : "") == (int)$p) {
            $count++;
        }
    }
    return $count;
}

// Функция фильтрации данных полученных из форм
/**
 * @param $str
 * @return string
 */
function esc($str)
{
    $text = htmlspecialchars($str);

    // $text = strip_tags($str);

    return $text;
}

// Функция проверки даты выпонения задачи
/**
 * @param $d
 * @return string
 */
function date_task_exec($d)
{
    // значение по умолчанию

    $result = '';

    // Мой часовой пояс

    date_default_timezone_set('Asia/Yekaterinburg');

    // Метка текущего времени

    $cur_date = time();

    // Метка времени указанной даты задачи с начала суток 00:00

    $task_date = strtotime($d);

    // У задачи нет даты

    if ((bool)$task_date === false) {
        return $result;
    }

    // Перевод в часы

    $cur_date_h = floor($cur_date / 3600);
    $task_date_h = floor($task_date / 3600);
    $diff = $task_date_h - $cur_date_h;
    if ($diff > 0) {

        // До выполнения менее или 24ч.

        if ($diff <= 24) {
            $result = 'make';
        }
    } else {

        // Дата выполнения: сегодня или прошла

        (abs($task_date - $cur_date) < 86400) ? $result = 'today' : $result = 'overdue';
    }
    return $result;
}

// Функция преобразования даты в формат d-m-Y. Используется при чтении дат из БД
/**
 * @param $date
 * @return false|string
 */
function date_dmY($date)
{
    if (isset($date)) {
        return date("d-m-Y", strtotime($date));
    }
}

// Функция преобразования даты в формат Y-m-d. Используется при записи дат в БД
/**
 * @param $date
 * @return bool|false|string
 */
function date_Ymd($date)
{
    if (isset($date) && ($date != "")) {
        return date("Y-m-d", strtotime($date));
    }
    return true;
}

// Функция проверки корректности формата даты ДД.ММ.ГГГГ
/**
 * @param $date
 * @return bool
 */
function is_valid_date($date)
{
    return preg_match('/^(\\d{2})\\.(\\d{2})\\.(\\d{4})$/', $date, $m)
        && checkdate($m[2], $m[1], $m[3]);
}

// Функции работы с БД

// Функция проверки результата выполнения запроса к БД
/**
 * @param $result
 * @param $connect
 */
function checkResult($result, $connect)
{
    if (!$result) {
        $error = mysqli_error($connect);
        print("Ошибка MySQL: " . $error);
    }
}

// Подключение к таблице users. Параметр $email - адрес эл. почты пользователя, полученный из формы
/**
 * @param $con
 * @param $email
 * @return array
 */
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

        $email = mysqli_real_escape_string($con, $email);

        $sql = "SELECT * FROM users WHERE users.email = '" . trim($email) . "';";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);
        checkResult($result, $con);

        // Преобразуем объект результата в массив

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Возвращаем массив [имя пользователя, e-mail]

        $tmp = [];
        if (empty($rows)) {
            return $tmp;
        } else {
            return isset($rows[0]) ? $rows[0] : "";
        }
    }
}

/**
 * @param $con
 * @param $email
 * @return array|null
 */
// Подключение к таблице proejcts. Параметр $email - эл. почта пользователя, полученная из таблицы users БД
/**
 * @param $con
 * @param $email
 * @return array|null
 */
function getProjects($con, $email)
{

    // Проверяем результат подключения

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // Таблица projects: формируем запрос на получение списка проектов по e-mail пользователя выбранного из таблицы users 

        $email = mysqli_real_escape_string($con, $email);

        $sql = "SELECT * FROM projects WHERE projects.user_id = (SELECT id FROM users WHERE users.email = '" . trim($email) . "');";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);

        checkResult($result, $con);

        // Преобразуем объект результата в массив

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Возвращаем список проектов

        return $rows;

    }
}

// Подключение к таблице tasks. Выборка задач по всем проектам пользователя с идентификатором $user_id 
/**
 * @param $con
 * @param $user_id
 * @return array|null
 */
function getTasks($con, $user_id)
{

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // Запрос для вывода задач по всем проектам пользователя

        $user_id['id'] = mysqli_real_escape_string($con, isset($user_id['id']) ? $user_id['id'] : "");

        $sql = "SELECT * FROM tasks WHERE user_id = " . $user_id['id'] . ";";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);

        checkResult($result, $con);

        // Преобразуем объект результата в массив 

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Возвращаем массив задач

        return $rows;
    }
}

// Выборка задач по выбранному проекту
/**
 * @param $con
 * @param $proj_id
 * @return array|null
 */
function getTasksByProjectID($con, $proj_id)
{

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // Запрос для вывода задач по id выбранного проекта

        $proj_id = mysqli_real_escape_string($con, $proj_id);

        $sql = "SELECT * FROM tasks WHERE project_id = " . $proj_id . ";";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);

        checkResult($result, $con);

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
