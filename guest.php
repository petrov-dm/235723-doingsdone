<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('functions.php');

$page_content = include_template('guest.php', []);
$layout_content = include_template('layout.php', [
    'content' => $page_content, 'title' => 'Дела в порядке'
]);

print ($layout_content);

?>
