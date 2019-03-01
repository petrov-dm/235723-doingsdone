<?php

session_start();

// показывать или нет выполненные задачи

$show_complete_tasks = rand(0, 1);

// Массивы для работы с БД:
// с таблицей users

$user_data = [];

// с таблицей projects

$projects = [];

// с таблицей tasks

$tasks = [];

// Переменная для получения e-mail пользователя из формы

$email_form = "";

// Подключаемся к базе данных

$connect = mysqli_connect("localhost", "root", "", "things_fine");

?>