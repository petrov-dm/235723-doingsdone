<?php
session_start();

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
            $errors[$key] = $errors[$key] . 'Это поле необходимо заполнить. ';
        }
    }

    // Проверяем валидность адреса e-mail 

    if ((!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) && !empty($_POST['email'])) {
        $errors['email'] = $errors['email'] . 'E-mail указан неверно. ';
    }

    // Если адрес e-mail не пустой и прошел валидацию проверяем его существование в таблице users БД

    if (empty($errors['email'])) {

        // Подключение к БД и создание массивов для работы с ней

        require_once('init.php');

        // Подключаем функции

        require_once('functions.php');

        // Проверяем результат подключения

        if ($connect == false) {
            print ("Ошибка подключения: " . mysqli_connect_error());
        } else {
            // Устанавливаем кодировку

            mysqli_set_charset($connect, "utf8");

            // Таблица users: формируем запрос проверку существования e-mail  

            $safe_email = mysqli_real_escape_string($connect, isset($_POST['email']) ? $_POST['email'] : "");

            $sql = "SELECT * FROM users WHERE users.email = '$safe_email'";

            // Получаем объект результата, проверяем успешность результатов запроса

            $result = mysqli_query($connect, $sql);

            checkEmail($result,$connect, $errors);

            /*
            if ($result == false) {
                $error = mysqli_error($connect);
                print ("Ошибка MySQL: " . $error);
            } else {
                $row_cnt = mysqli_num_rows($result);
                // Если в БД уже есть такой e-mail фиксируем ошибку
                if ($row_cnt != 0) {
                    $errors['email'] = $errors['email'] . 'Такой E-mail уже зарегистрирован. ';
                }

            }
            */
        }
    }

    // Проверяем результаты валидации. 

    if ((int)count($errors) == 0) {
        // Ошибок нет. Записываем информацию в БД. Перенаправляем пользователя на главную страницу

        // Подключение к БД и создание массивов для работы с ней

        require_once('init.php');

        // Подключаем функции

        require_once('functions.php');

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

            checkResult($result,$connect);
            /*
            if (!$result) {
                $error = mysqli_error($connect);
                print("Ошибка MySQL: " . $error);
            }
            */
        }

        // После успешной регистрации открываем форму входа auth.php  
        header('Location: auth.php');

    } else {
        // Если есть ошибки валидации вызываем сценарий register.php и выводим их под полями
        require_once('functions.php');
        $page_content = include_template('register.php', ['user_value' => $user_value, 'errors' => $errors]);
        $layout_content = include_template('layout.php', ['content' => $page_content]);
        print ($layout_content);
        exit();
    }
}

// Этот фрагмент код выполняется при перенаправлении со сценария index.php, когда пользователь не аутентифицирован и пытается зайти на сайт


require_once('functions.php');
$page_content = include_template('register.php', []);
$layout_content = include_template('layout.php', ['content' => $page_content]);
print ($layout_content);


/*
$register_content = include_template('register.php', []);
print ($register_content);
*/
?>
