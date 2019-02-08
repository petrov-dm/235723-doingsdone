<?php 
// Функция шаблонизации
    function include_template($name, $data) {
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

//Функция фильтрации
function esc($str) {
	$text = htmlspecialchars($str);
	//$text = strip_tags($str);
	return $text;
}
?>