<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Массивы для работы с БД:
// с таблицей users

$user_data = [];

// с таблицей projects

$projects = [];

// с таблицей tasks

$tasks = [];

// Подключаемся к базе данных

$connect = mysqli_connect("localhost", "root", "", "things_fine");

?>