<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    
    
    
    //echo get_list_where('rates', array('city_id' => $_GET['city_id'], 'lang' => $config['lang']));
//    
//    return;

    $alll = array();
        
    $rates = make_array_from_query(select_where('rates', array('city_id' => $_GET['city_id'], 'lang' => $config['lang']), true));
        
    $alll['rates'] = $rates;
    
    $services = make_array_from_query(select_where('tariffs', array('city_id' => $_GET['city_id']), true));
    
    $services_name = make_array_from_query(select_all('services', true));        
     
    $serv = array();
     
    $s=0;
    for($i=0; $i < count($services); $i++){
        if($services[$i]['type'] != 111 && $services[$i]['type'] != 222 && $services[$i]['type'] != 333 && $services[$i]['type'] != 1){
             
             $serv[$s] = array(
                 'uid' => $services[$i]['uid'],
                 'type' => $services[$i]['type']
             );
             
             foreach($services_name as $ser){
                if($services[$i]['type'] == $ser['uid']){
                    $serv[$s]['name'] = $ser['name'];
                }
             }
                
             if($services[$i]['rate'] < 2){
                $serv[$s]['percent'] = $services[$i]['rate'];
             } else {
                $serv[$s]['price'] = $services[$i]['rate'];
             }
                
            $s++;
        }
    }  
        
    $alll['services'] = $serv;
    
    
    $mess = (is_array($rates) && is_array($services))?"Ok":"DB ERROR";
    $code = (is_array($rates) && is_array($services))?0:5;
 
  
    $alll['status'] = array(
        'code' => $code,
        'message' => $mess   
    );
    if(!empty($php_errormsg)){
            $alll['status']['debugInfo'] = $php_errormsg; 
    }
    echo jdecoder(json_encode($alll));
    
    return;

?>