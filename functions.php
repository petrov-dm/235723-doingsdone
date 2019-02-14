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

//    Функция подсчета   задач

function count_tasks($t, $p)
{
	$count = 0;
	foreach($t as $key => $item) {
		if ($item['proect'] === $p) {
			$count++;
		}
	}

	return $count;
};

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
	}
	else {
		$result = 'overdue';
	} //просроченное дело
	return $result;
}

// Функции работы с БД
// Подключение к таблице users

function connect_users($con)
{

    
    // Проверяем результат подключения

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    }
    else {
        print ("Соединение установлено");

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // ТАБЛИЦА USERS
        // Получаем имя пользователя из таблицы users
        // Формируем запрос

        $sql = "SELECT name FROM users WHERE users.email = 'konst@mail.ru';";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);
        if ($result == false) {
            $error = mysqli_error($con);
            print ("Ошибка MySQL: " . $error);
        }

        // Преобразуем объект результата в массив

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Передаем имя из массива в переменную $user_name

        return $rows[0]['name'];
    }
}

// Подключение к таблице proejcts

function connect_projects($con)
{

    // Проверяем результат подключения

    if ($con == false) {
        print ("Ошибка подключения: " . mysqli_connect_error());
    }
    else {
        print ("Соединение установлено");

        // Устанавливаем кодировку

        mysqli_set_charset($con, "utf8");

        // Таблица projects: формируем запрос на получение списка проектов 

        $sql = "SELECT name FROM projects GROUP BY name;";

        // Получаем объект результата, проверяем успешность результатов запроса

        $result = mysqli_query($con, $sql);
        if ($result == false) {
            $error = mysqli_error($con);
            print ("Ошибка MySQL: " . $error);
        }

        // Преобразуем объект результата в массив

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // Заполняем одномерный массив с названиями проектов
        
        $tmp =[];
        foreach($rows as $key => $item){
            foreach($item as $name_pr){
                array_push($tmp,$name_pr);
            }
        }
        
        // Возвращаем список проектов
        
        return $tmp;
    }
}





?>