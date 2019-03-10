<?php

session_start();

require_once('functions.php');

$page_content = include_template('guest.php', []);
$layout_content = include_template('layout.php', [
    'content' => $page_content
]);

print ($layout_content);

?>
