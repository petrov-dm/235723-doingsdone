<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Подключение к БД и создание массивов для работы с ней

    require_once('init.php');

    // Подключаем функции

    require_once('functions.php');

    // Валидация введенных в форму данных
    // Массив с введенными значениями полей (копируем из $_POST)

    $user_value = $_POST;

    // Массив обязательных полей

    $required = ['email', 'password'];

    // Массив для хранения ошибок валидации

    $errors = [];

    // Проверяем заполнение обязательных полей
    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле необходимо заполнить';
        }
    }

    // Проверяем валидность адреса e-mail 

    if ((!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) && !empty($_POST['email'])) {
        $errors['email'] =  'E-mail указан неверно. ';
    }

    // Если адрес e-mail не пустой и прошел валидацию проверяем его существование в таблице users БД

    if (empty($errors['email'])) {

        // Для предотвращения SQL-инъекции вносим значение из формы в переменную $safe_email

        $safe_email = mysqli_real_escape_string($connect, isset($_POST['email']) ? $_POST['email'] : "");

        // Проверяем существование email в БД при помощи функции getUsers

        $user_data = getUsers($connect, $safe_email);

        if (empty($user_data)) {
            $errors['email'] = 'Такого e-mail нет в базе.';
        }
    }

    // Проверяем результаты валидации данных из формы. Если ошибок нет, то проверяем пароль 

    if ((int)count($errors) == 0) {
        // Ошибок нет. E-mail пользователя зарегистрирован в системе. Проверяем введенный в форму пароль
        if (password_verify($_POST['password'], $user_data['pwd'])) {

            // Пароль верный. Пользователь аутентифицирован. Открываем сессию. 

            $_SESSION['user'] = $user_data;

            // После аутентификации по умолчанию выполненные задачи не отображаются

            $_SESSION['user']['show_complete_tasks'] = 0;
            
            // Переходим на главную страницу.
            
            header('Location: index.php');

        } else {
            $errors['password'] = "Не верный пароль! Повторите попытку.";
        }
    }

    // В случае ошибок валидации или неправильного пароля заново вызываем сценарий auth.php
    if ((int)count($errors) !== 0) {
        $page_content = include_template('auth.php', ['user_value' => $user_value, 'errors' => $errors]);
        $layout_content = include_template('layout.php', [
            'content' => $page_content, 'title' => 'Дела в порядке'
        ]);
        print ($layout_content);
        exit();
    }

}

/* 
   Этот фрагмент код выполняется при: 
   1) перенаправлении со сценария register.php,после успешной регистрации 
   2) нажатии кнопки "Войти" в header страницы
   3) нажатии кнопки "Войти" в сценарии register.php (находится в левой части окна)
   4) нажатии кнопки "Войти" в сценарии auth.php (находится в левой части окна)
   Пользователь еще не аутентифицирован и для него отображается форма входа.
*/

require_once('functions.php');
$page_content = include_template('auth.php', []);
$layout_content = include_template('layout.php', [
    'content' => $page_content, 'title' => 'Дела в порядке'
]);
print ($layout_content);
?>
