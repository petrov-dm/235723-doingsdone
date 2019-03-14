<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Подключение к БД и создание массивов для работы с ней

require_once('init.php');

// Подключаем функции

require_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    
    // Валидация введенных данных
    // Массив с введенными значениями полей (копируем из $_POST)

    $user_value = $_POST;

    // Массив обязательных полей

    $required = ['email', 'password', 'name'];

    // Массив для хранения ошибок валидации

    $errors = [];

    // Проверяем заполнение обязательных полей

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле необходимо заполнить. ';
        }
    }

    // Проверяем валидность адреса e-mail 

    if ((!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) && !empty($_POST['email'])) {
        $errors['email'] = 'E-mail указан неверно. ';
    }

    // Если адрес e-mail не пустой и прошел валидацию проверяем его существование в таблице users БД
        
    if (empty($errors['email'])) {
    
        // Готовим адрес e-mail для SQL-запроса
        
        $safe_email = mysqli_real_escape_string($connect, isset($_POST['email']) ? $_POST['email'] : "");
        
        // Вызываем функцию getUsers. Если она возвращает пустой массив - то данного адреса e-mail в базе нет   
        if ( !empty(getUsers($connect, $safe_email)) ){
            $errors['email'] = 'Такой E-mail уже зарегистрирован. ';
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

            $safe_email = mysqli_real_escape_string($connect, isset($_POST['email']) ? $_POST['email'] : "");

            $safe_password = mysqli_real_escape_string($connect, isset($_POST['password']) ? $_POST['password'] : "");
            $safe_password = password_hash($safe_password, PASSWORD_DEFAULT);

            $safe_name = mysqli_real_escape_string($connect, isset($_POST['name']) ? $_POST['name'] : "");

            $sql = "INSERT INTO users SET email = '$safe_email', name = '$safe_name', pwd =  '$safe_password'";

            // Выполняем запрос

            $result = mysqli_query($connect, $sql);

            // Проверяем успешность выполнения запроса

            checkResult($result, $connect);
        }

        // После успешной регистрации открываем форму входа auth.php  
        header('Location: auth.php');

    } else {

        // Если есть ошибки валидации вызываем сценарий register.php и выводим их под полями

        $page_content = include_template('register.php', ['user_value' => $user_value, 'errors' => $errors]);
        $layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке']);
        print ($layout_content);
        exit();
    }
    }

// Этот фрагмент код выполняется при перенаправлении со сценария index.php, когда пользователь не аутентифицирован и пытается зайти на сайт

$page_content = include_template('register.php', []);
$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Дела в порядке']);
print ($layout_content);

?>
