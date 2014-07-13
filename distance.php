<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    
    
    
    $data="http://maps.googleapis.com/maps/api/directions/json?mode=driving&sensor=false&language=ru";

    $data .= "&origin=49.438888,31.478888";
    $data.="&destination=49.431617,30.479936"; 
    //echo $data;
    $curl  =  curl_init($data); 
    //  Устанавливаем параметры  соединения 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    
    $json  =  curl_exec($curl);
    
    curl_close($curl);
    $resp=json_decode($json);
    
    $all = array();
    //
    $mess = ($resp)?"Ok":"JSON error";
    $code = ($resp)?0:9;
        
    $all['status'] = array(
        'code' => $code,
        'message' => $mess
    );
    $all['price'] = distance_count(substr($resp->routes[0]->legs[0]->distance->text, 0, -5), $_POST['services']);
    
    
     echo jdecoder(json_encode($all));
     
 // $data = "http://195.16.88.187/dis.php";
////echo $data;
//$curl  =  curl_init($data); 
////  Устанавливаем параметры  соединения 
//curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
////curl_setopt($curl, CURLOPT_POST, 1);
////$data = "distance=17.8&cond=".serialize(array(17,12))."";
////curl_setopt($curl, CURLOPT_COOKIE, "login=taximoto");
//
////curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
////  Получаем  содержимое  страницы 
//$content  =  curl_exec($curl);
//
//curl_close($curl);
//
//$tr = substr($content, 0, 30); 
//
////header('Content-Type: application/json; charset=utf-8');
//  
//  echo $tr;
        return;
        
?>