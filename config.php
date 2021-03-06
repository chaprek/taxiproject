<?php

set_time_limit(60*10);
ini_set('track_errors', 1); 
ini_set('mysql.connect_timeout', 1); 
//error_reporting(E_ALL);

/*-Определение языка-*/
if(isset($_SERVER['HTTP_LANGUEGE_TEXT'])){
    $lang = $_SERVER['HTTP_LANGUEGE_TEXT'];
} elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
} else {
    $lang = 'ru';
}

require_once ('makelogs.php'); // создание логов запросов


/*-Конфигурация-*/
$config = array
    (
        'phones' => array( //телефоны для городов
                        '1' => array('+380444675467',//Киев
                                     '+380676655464',
                                     '+380660882508',
                                     '+380631195770'),
                        '2' => array('+380577281728',//Харьков
                                     '+380995148888',
                                     '+380630798888',
                                     '+380986428888'),
                        '3' => array('+380567944794',//Днепропетровск
                                     '+380978197777',
                                     '+380633617777',
                                     '+380664697777'),
                        '4' => array('+380487344734',//Одесса
                                     '+380966474444',
                                     '+380996334444',
                                     '+380932904444'),
                        '5' => array('+380322428242',//Львов
                                     '+380676074242',
                                     '+380958154242',
                                     '+380631954242'),
                        '6' => array('+380622101110',//Донецк
                                     '579'),
                        '7' => array('+380612282228',//Запорожье
                                     '+380995602222',
                                     '+380970562222',
                                     '+380934412222'),
                        '8' => array('+380362431561',//Ровно
                                     '+380979991561',
                                     '+380939991561',
                                     '+380669201561'),
                        '9' => array('+380342591111',//Ивано-Франковск
                                     '+380962331111',
                                     '+380938571111',
                                     '+380669521111'),
                        '10' => array('+380652791791',//Симферополь
                                     '+380962391111',
                                     '+380636621111',
                                     '+380662361111'),
                        '11' => array('+380342591111',//Тернополь
                                     '+380962331111',
                                     '+380938571111',
                                     '+380669521111'),
                        '12' => array('+380652791791',//Ужгород
                                     '+380962391111',
                                     '+380636621111',
                                     '+380662361111'),
                        '13' => array('+380652791791',//Кривой Рог
                                     '+380962391111',
                                     '+380636621111',
                                     '+380662361111'),
                        '14' => array('+380342591111',//Черновцы
                                     '+380962331111',
                                     '+380938571111',
                                     '+380669521111'),
                        '15' => array('+380652791791',//Хмельницкий
                                     '+380962391111',
                                     '+380636621111',
                                     '+380662361111'),
                        '16' => array('+380652791791',//Луцк
                                     '+380962391111',
                                     '+380636621111',
                                     '+380662361111')         
        ),
        'evos_url' => array( //телефоны для городовhttp://82.117.248.7:6969/api/
                        '1' => array('http://46.151.86.228:6969/api/', 'http://159.224.156.123:6969/api/'),//Киев
                        '2' => array('http://109.86.42.147:6969/api/', 'http://46.151.86.229:6969/api/'),//Харьков
                        '3' => array('http://89.28.206.110:6969/api/', 'http://83.170.214.178:6969/api/'),//Днепропетровск
                        '4' => array('http://85.238.99.79:6969/api/', 'http://62.64.92.142:6969/api/'),//Одесса
                        '5' => array('http://95.69.245.34:6969/api/', 'http://91.200.113.74:6969/api/'),//Львов
                        '6' => array('http://178.151.205.204:6969/api/', 'http://195.58.231.206:6969/api/'),//Донецк
                        '7' => array('http://77.93.34.82:6969/api/', 'http://77.93.32.103:6969/api/'),//Запорожье
                        '8' => array('http://optimarv.dlinkddns.com:6969/api/', 'http://194.44.93.169:6969/api/'),//Ровно
                        '9' => array('http://62.122.204.174:6969/api/', 'http://62.122.205.115:6969/api/'),//Ивано-Франковск
                        '10' => array('http://109.86.42.147:6666/api/', 'http://46.151.86.229:6666/api/'),//Симферополь    
                        '11' => array('http://62.122.204.174:6969/api/', 'http://62.122.205.115:6969/api/'),//Тернополь   
                        '12' => array('http://62.122.204.174:6969/api/', 'http://62.122.205.115:6969/api/'),//Ужгород   
                        '13' => array('http://46.151.86.228:6970/api/', 'http://159.224.156.123:6970/api/'),//Кривой Рог   
                        '14' => array('http://62.122.204.174:6969/api/', 'http://62.122.205.115:6969/api/'),//Черновцы   
                        '15' => array('http://62.122.204.174:6969/api/', 'http://62.122.205.115:6969/api/'),//Хмельницкий   
                        '16' => array('http://46.151.86.228:6970/api/', 'http://159.224.156.123:6970/api/')//Луцк     
                ),
        'disp_url' => array( //телефоны для городовhttp:82.117.248.7:6969/api/
                        '1' => array('host' => '46.151.86.228', 'port' => '339'),//Киев
                        '2' => array('host' => '109.86.42.147', 'port' => '339'),//Харьков
                        '3' => array('host' => '89.28.206.110', 'port' => '339'),//Днепропетровск
                        '4' => array('host' => '85.238.99.79', 'port' => '339'),//Одесса
                        '5' => array('host' => '95.69.245.34', 'port' => '339'),//Львов
                        '6' => array('host' => '178.151.205.204', 'port' => '339'),//Донецк
                        '7' => array('host' => '77.93.34.82', 'port' => '339'),//Запорожье
                        '8' => array('host' => 'optimarv.dlinkddns.com', 'port' => '339'),//Ровно
                        '9' => array('host' => '62.122.204.174', 'port' => '339'),//Ивано-Франковск
                        '10' => array('host' => '109.86.42.147', 'port' => '339'),//Симферополь    
                        '11' => array('host' => '62.122.204.174', 'port' => '340'),//Тернополь   
                        '12' => array('host' => '62.122.204.174', 'port' => '341'),//Ужгород
                        '13' => array('host' => '46.151.86.228', 'port' => '339'),//Кривой Рог    
                        '14' => array('host' => '95.69.245.34', 'port' => '340'),//Черновцы   
                        '15' => array('host' => '95.69.245.34', 'port' => '341'),//Хмельницкий
                        '16' => array('host' => 'optimarv.dlinkddns.com', 'port' => '340')//Луцк     
                ),
        'os'  => (isset($_SERVER['HTTP_OS']) && $_SERVER['HTTP_OS'] == 'android')?false:true,//айди оператора
        'lang' => ($lang == "ua" || $lang == "uk" )?'uk':'ru',//определение языка приложения
        'oper'  => 333,//айди оператора
        'first_base' => array(5,7,8,9,10,11,12,14,15,16),//города которые используют базу mysql
        'count_sms' => 5,//колличество смс
        'time_sms'  => 60*5,//время для смс
        'count_orders' => 5,//колличество заказов за промежуток времени
        'time_orders'  => 60*5, // промежуток времени заказов заказов
        'timeout_orders'  => 60*10 // таймаут ожидания заказа, после отмена
    );



?>