<?php
    session_start();
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    
  
    
    if(isset($_GET['cities'])){
        header('Content-Type: application/json; charset=utf-8');
        echo get_list('cities', true);
        return;
    } elseif(isset($_GET['about'])){
         header('Content-Type: application/json; charset=utf-8');
        echo substr(get_list(), 0, -1);
        if($config['lang'] == 'ru'){
        ?>,
  "info" : { 
    "htm_text" : "<style>.main {padding: 3px 35px 0 10px;} img {width: 100%; margin-bottom: 10px;}.wrap {box-shadow: inset 0px 1px 1px 1px #cbcbcb; padding: 3px; border-radius: 11px; -webkit-border-radius: 11px; } .mb {margin-bottom: 15px;} p {font-family: HelveticaNeue-Light, Helvetica Neue Light, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;  font-size: 15px; text-align: justify; } a { background: linear-gradient(#ffffff, #e3e3e3) repeat scroll 0 0 #207abe; -webkit-box-shadow: 0 0 4px #999, 0 0 1px #fff; color: #FF9600; border: 1px solid #FF9600; border-radius: 10px; -webkit-border-radius: 10px; font-size: 15px; font-family: Helvetica; line-height: 35px; text-align: center; text-decoration: none; display: block; font-weight: bold; } a.blue {color: #2993D1; border-color: #2993D1;} h2 {font-size: 17px; font-weight: normal; color: #2993D1; font-family: Helvetica;}<\/style><div><div class='main'><img src='logo-image@2x.png' \/><\/div><div class='wrap'><a href='http:\/\/optima.fm'>optima.fm<\/a><\/div><p>Транспортная компания \"Оптимальное такси\" работает по всей Украине и предоставляет полный спектр услуг по пассажирским перевозкам. Комфорт, приемлемые цены и высокое качество обслуживания - это три составляющих первосклассного сервиса.<\/p><p>Заказать такси Вы можете 24 часа в сутки по телефону:<\/p><div class='wrap'><a href='telprompt:+380444999898'>+38 (044) 499-98-98<\/a><\/div><p>Телефон отдела пассажирских перевозок:<\/p><div class='wrap'><a href='telprompt:+380444675446'>+38 (044) 467-54-46<\/a><\/div><p>Также компания предоставляет следующие услуги:<\/p><div class='wrap mb'><a href='http:\/\/gruztaxi.org' class='blue'>Грузоперевозки<\/a><\/div><div class='wrap mb'><a href='http:\/\/gruztaxi.org\/mebelupak_offis.php' class='blue'>Офисный переезд<\/a><\/div><div class='wrap mb'><a href='http:\/\/gruztaxi.org\/remuve_kvartira.php' class='blue'>Квартирный переезд<\/a><\/div><div class='wrap mb'><a href='http:\/\/gruztaxi.org\/remuve_kvartira.php' class='blue'>Перевозка мебели<\/a><\/div><div class='wrap mb'><a href='http:\/\/gruztaxi.org\/gruzchiki.php' class='blue'>Заказ грузчиков<\/a><\/div><p>Мы набираем на работу водителей со стежем не менее пяти лет. Прием заказов осуществляется как на русском, украинском так и на английском языках, что значительно облегчает сотрудничество с нашей компанией. Мы понимаем Ваших иностранных партнеров с полуслова. Ценовая политика транспортной компании Оптимальное такси предусматривает гибкую систему скидок. При предъявлении купона или визитки нашей компании Вам будет предоставлена скидка с фиксированного тарифа. Оплата услуг по фиксированным тарифам позволит Вам точно рассчитать расходы на трансферт. Четкая организация работы всех сотрудников компании - гарантия стопроцентного успеха. Мы поможем Вам оказаться в нужном месте в нужное время.<\/p><h2>'ВАШ ОТДЫХ НАЧИНАЕТСЯ В НАШЕМ АВТО!'<\/h2><p>Транспортная компания Оптимальное такси<\/p><div class='wrap mb'><a href='http:\/\/mobox.kiev.ua' class='blue'>Разработчики приложения<\/a><\/div><\/div>"        
        }
    }<?
        } else {
         ?>,
  "info" : { 
    "htm_text" : "<style>.main {padding: 3px 35px 0 10px;} img {width: 100%; margin-bottom: 10px;}.wrap {box-shadow: inset 0px 1px 1px 1px #cbcbcb; padding: 3px; border-radius: 11px; -webkit-border-radius: 11px; } .mb {margin-bottom: 15px;} p {font-family: HelveticaNeue-Light, Helvetica Neue Light, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;  font-size: 15px; text-align: justify;} a { background: linear-gradient(#ffffff, #e3e3e3) repeat scroll 0 0 #207abe; -webkit-box-shadow: 0 0 4px #999, 0 0 1px #fff; color: #FF9600; border: 1px solid #FF9600; border-radius: 10px; -webkit-border-radius: 10px; font-size: 15px; font-family: Helvetica; line-height: 35px; text-align: center; text-decoration: none; display: block; font-weight: bold; } a.blue {color: #2993D1; border-color: #2993D1;} h2 {font-size: 17px; font-weight: normal; color: #2993D1; font-family: Helvetica;}<\/style><div><div class='main'><img src='logo-image@2x.png' \/><\/div><div class='wrap'><a href='http:\/\/optima.fm'>optima.fm<\/a><\/div><p>Транспортна компанія \"Оптимальне таксі\" працює по всій теретирії України і надає повний спектр послуг по пасажирським перевезенням. Комфорт, прийнятні ціни та висока якість обслуговування - це три складові першокласного сервісу.<\/p><p>Замовити таксі Ви можете 24 години на добу за телефоном:<\/p><div class='wrap'><a href='telprompt:+380444999898'>+38 (044) 499-98-98<\/a><\/div><p>Телефон відділу пасажирських перевезень:<\/p><div class='wrap'><a href='telprompt:+380444675446'>+38 (044) 467-54-46<\/a><\/div><p>Також компанія надає наступні послуги:<\/p><div class='wrap mb'><a href='http:\/\/gruztaxi.org' class='blue'>Грузоперевезення<\/a><\/div><div class='wrap mb'><a href='http:\/\/gruztaxi.org\/mebelupak_offis.php' class='blue'>Офісний переїзд<\/a><\/div><div class='wrap mb'><a href='http:\/\/gruztaxi.org\/remuve_kvartira.php' class='blue'>Квартирний переїзд<\/a><\/div><div class='wrap mb'><a href='http:\/\/gruztaxi.org\/remuve_kvartira.php' class='blue'>Перевезення меблів<\/a><\/div><div class='wrap mb'><a href='http:\/\/gruztaxi.org\/gruzchiki.php' class='blue'>Замовлення вантажників<\/a><\/div><p>Ми приймаємо на роботу водіїв з досвідом не менш ніж п'ять років. Приймання замовлень здійснюється як укрїнською так і російською мовами, що значно полегшує співробітництво з нашою компанією. Ми розуміємо Ваших іноземних партнерів з півслова. Цінова політика транспортної компанії Оптимальне таксі пропонує гнучку систему знижок. При наданні купону або візитки нашої компанії Вам буде надана знижка з фіксованого тарифу. Оплата послуг за фіксованими тарифами дозволить Вам точно розрахувати витрати на трансферт. Чітка організація роботи всіх співробітників компанії - гарантія стовідсоткового успіху. Ми допоможемо Вам з'явитися впотрібному місці у потрібний час.<\/p><h2>'ВАШ ВІДПОЧИНОК ПОЧИНАЄТЬСЯ У НАШОМУ АВТО!'<\/h2><p>Транспортна компанія Оптимальне таксі<\/p><div class='wrap mb'><a href='http:\/\/mobox.kiev.ua' class='blue'>Розробники додатку<\/a><\/div><\/div>"        
        }
    }<?   
        }
        return;
    } 
   
    
require_once ('cms.php');

?>