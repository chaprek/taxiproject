<?php
/*-LOCAL Connect Info-*/
$mysql_username_local="admin"; //Имя пользователя базы данных
$mysql_password_local="rfcbjgtz"; //Пароль пользователя базы данных
$mysql_host_local="195.16.88.187"; //Сервер базы данных
$mysql_database_local="taxi"; //Префикс таблиц в базе данных
/*-END LOCAL Connect Info-*/

//Соединяемся с локальной базой данных
$dbl = mysql_connect($mysql_host_local, $mysql_username_local, $mysql_password_local);

//Выбираем базу данных для работы с сервером
mysql_select_db($mysql_database_local, $dbl);

mysql_query ("set character_set_client='cp1251'", $dbl); //кодировка, в которой данные будут поступать от клиента
mysql_query ("set character_set_results='cp1251'", $dbl); //кодировка, в которой будет выбран результат
mysql_query ("set collation_connection='cp1251_general_ci'", $dbl); //кодировка по умолчанию для всего, что в рамках соединения не имеет кодировки
mysql_query('SET CHARACTER SET cp1251', $dbl);
mysql_query('SET NAMES cp1251', $dbl);
/*-END LOCAL Connect Info-*/






?>