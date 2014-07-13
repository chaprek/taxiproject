<?php

/*-LOCAL Connect Info-*/
$mysql_username_local="bioboomc_open"; //Имя пользователя базы данных
$mysql_password_local="rfcbjgtzom"; //Пароль пользователя базы данных
$mysql_host_local="195.248.234.45"; //Сервер базы данных
$mysql_database_local="bioboomc_open"; //Префикс таблиц в базе данных
/*-END LOCAL Connect Info-*/
ini_set('display_errors', 1);
error_reporting(E_ALL);

//Соединяемся с локальной базой данных
$db2 = mysql_connect($mysql_host_local, $mysql_username_local, $mysql_password_local) or die(mysql_error());

//Выбираем базу данных для работы с сервером
mysql_select_db($mysql_database_local, $db2) or die(mysql_error());

$th = "INSERT INTO product SET model = 'Test',
quantity = '10',
minimum = '1',
stock_status_id = '7',
date_available = '',
manufacturer_id = '',
shipping = '0',
price = '100.500',
points = '0',
weight = '(0',
weight_class_id = '0',
length = '0',
width = '0',
height = '0',
length_class_id = '0',
status = '1',
sort_order = '1',
date_added = NOW()";
    
    
    $th ='SELECT product_id FROM "product" WHERE product_ids = 81325562 ';
    $th = 'SELECT product_id FROM product WHERE product_ids = 81325394';
    $ok = mysql_query($th, $db2);
    
      // $i = 0;
        while($result = mysql_fetch_assoc($ok)){
                         
               print_r($result);
                //$array[$i][$k] = ($decod)?iconv('cp1251', 'utf-8', $col):$col;
      
            $i++; 
        }

?>