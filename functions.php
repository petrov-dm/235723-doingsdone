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


//    Функция подсчета   задач
    function count_tasks($t,$p){
    $count = 0;
    foreach($t as $key => $item){
        if ($item['proect']===$p){
            $count++;
        }
    }
    return $count;
};


//Функция фильтрации
function esc($str) {
	$text = htmlspecialchars($str);
	//$text = strip_tags($str);
	return $text;
}

//Функция проверки даты выпонения задачи 
function date_task_exec($d){
    $result = ''; // значение по умолчанию
    
    date_default_timezone_set('Asia/Yekaterinburg'); // Мой часовой пояс 
    // метка текущего времени 
    $cur_date = time();    
    // метка времени задачи
    $task_date = strtotime($d);   
    // У задачи нет даты
    if ($task_date===false){return $result;} 
    // Перевод в часы
    $cur_date = floor($cur_date/3600);
    $task_date = floor($task_date/3600);
    $diff = $task_date - $cur_date;
    if ($diff > 0){
        if ($diff <= 24){ $result='make' ; } // до выполнения менее или 24ч.
    }else{ $result='overdue' ; } //просроченное дело
    return $result;
}

?>