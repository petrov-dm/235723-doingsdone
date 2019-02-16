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

// Функция подсчета   задач

function count_tasks($t, $p)
{
    /*
    print('<pre>');
        print_r($t);
    print('</pre>');
    */
    print('<pre>');
    //     print_r($p);
    print('</pre>');

    $count = 0;

    foreach ($t as $key => $item) {

        if (strcasecmp(trim($item['project']), trim($p)) == 0) {
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

// Функции работы с БД
// Подключение к таблице users. Параметр $us_eml - адрес эл. почты пользователя, полученный из формы

function connect_users($con, $us_eml)
{
    // Проверяем результат подключения

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {
        print ("Соединение установлено");

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // ТАБЛИЦА USERS
        // Если введенный из формы e-mail есть в базе - получаем имя пользователя и e-mail из таблицы БД. Далее используем их для получения информации из таблиц users, tasks

        $sql = "SELECT name, email FROM users WHERE users.email = '" . trim($us_eml) . "';";

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

// Подключение к таблице proejcts. Параметр $us_eml_found - эл. почта пользователя, полученная из таблицы users БД

function connect_projects($con, $us_eml_found)
{

    // Проверяем результат подключения

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {
        print ("Соединение установлено");

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // Таблица projects: формируем запрос на получение списка проектов по e-mail пользователя выбранного из таблицы users

        $sql = "SELECT name FROM projects WHERE projects.user_id = (SELECT id FROM users WHERE users.email = '" . trim($us_eml_found) . "') GROUP BY name ;";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);
        if ($result == false) {
            $error = mysqli_error($con);
            print ("Ошибка MySQL: " . $error);
        }

        // Преобразуем объект результата в массив

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Заполняем одномерный массив с названиями проектов

        $tmp = [];
        foreach ($rows as $key => $item) {
            foreach ($item as $key2 => $item2) {
                array_push($tmp, $item2);
            }
        }

        // Возвращаем список проектов

        return $tmp;

    }
}

// Подключение к таблице tasks. Параметр $us_eml_found - эл. почта пользователя, полученная из таблицы users БД

function connect_tasks($con, $us_eml_found)
{
    // Проверяем результат подключения

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    } else {
        print ("Соединение установлено");

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // Таблица tasks: формируем запрос на получение списка задач по e-mail пользователя, выбранного из таблицы users 

        $sql = "SELECT tasks.name, tasks.date_planned AS date, projects.name AS project, tasks.done FROM tasks INNER JOIN projects ON tasks.user_id = (SELECT id FROM users WHERE users.email = '" . trim($us_eml_found) . "') WHERE projects.user_id = (SELECT id FROM users WHERE users.email = '" . trim($us_eml_found) . "') AND projects.id = tasks.project_id;";

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

?>
