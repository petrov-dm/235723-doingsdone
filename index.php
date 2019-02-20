<?php

// показывать или нет выполненные задачи

$show_complete_tasks = rand(0, 1);

// Массивы для работы с БД:
// с таблицей users

$user_name = [];

// с таблицей projects

$projects = [];

// с таблицей tasks

$tasks = [];

// Переменная для получения e-mail пользователя из формы

$email_form = "";

/*
// Массив проектов

//$projects = ["Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];

// Массив задач

$tasks = [
    [
        'name' => "Собеседование в IT компании",
        'date' => "01.12.2019",
        'proect' => "Работа",
        'done' => false
    ],
    [
        'name' => "Выполнить тестовое задание",
        'date' => "25.12.2019",
        'proect' => "Работа",
        'done' => false
    ],
    [
        'name' => "Сделать задание первого раздела",
        'date' => "21.12.2019",
        'proect' => "Учеба",
        'done' => true
    ],
    [
        'name' => "Встреча с другом",
        'date' => "22.12.2019",
        'proect' => "Входящие",
        'done' => false
    ],
    [
        'name' => "Купить корм для кота",
        'date' => "Нет",
        'proect' => "Домашние дела",
        'done' => false
    ],
    [
        'name' => "Заказать пиццу",
        'date' => "Нет",
        'proect' => "Домашние дела",
        'done' => false
    ]
];

*/

// Подключаем функции

require_once('functions.php');

// Получаем e-mail пользователя из формы. Используем функцию фильтрации esc().

$email_form = esc("ivan@mail.ru");

// Подключаемся к базе данных

$connect = mysqli_connect("localhost", "root", "", "things_fine");

// Обращаемся к таблице users для извлечения имени пользователя и его e-mail. Значение переменной $email_form используется для поиска в таблице БД.   

$user_name = getUsers($connect, $email_form);

// Обращаемся к таблице projects для получения списка проектов 

$projects = getProjects($connect, $user_name['email']);

// Обращаемся к таблице tasks для получения списка задач текущего пользователя 

$tasks = getTasks($connect, $user_name['email']);

// Шаблоны

$page_content = include_template('index.php', ['tasks' => $tasks, 'show_complete_tasks' => $show_complete_tasks]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'projects' => $projects,
    'user_name' => $user_name['name'],
    'title' => 'Дела в порядке',
    'tasks' => $tasks
]);
print ($layout_content);

?>
