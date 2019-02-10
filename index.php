<?php
// показывать или нет выполненные задачи

$show_complete_tasks = rand(0, 1);

//Массив проектов

$projects = ["Входящие","Учеба","Работа","Домашние дела","Авто"];

//Массив задач

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

//Подключаем функции

require_once('functions.php');

//Шаблоны

$page_content = include_template('index.php', ['tasks' => $tasks, 'show_complete_tasks' => $show_complete_tasks]);
$layout_content = include_template('layout.php',['content' => $page_content, 'projects' => $projects,'user_name'=>'Константин','title' => 'Дела в порядке', 'tasks' => $tasks]);
print($layout_content);

?>
