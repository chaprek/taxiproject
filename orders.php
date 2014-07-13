<?php
    session_start();
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    
    header('Content-Type: application/json; charset=utf-8');
    
     if(isset($_GET['authToken'])){
        $auth = array('authToken' => $_GET['authToken']);
    } else {
        $auth = array('authToken' => 13);        
    }
        
        
    $id = make_array_from_query(select_where('clients', $auth, true)); 
     
     if(isset($id['uid'])){  
            
            /*-Lasy Loading-*/
            extract(lasy_loading((isset($_GET['count']))?$_GET['count']:'', (isset($_GET['since_id']))?$_GET['since_id']:'', (isset($_GET['max_id']))?$_GET['max_id']:''));
            /*-END Lasy Loading-*/
            
        // первичная информация по заказам
         $orders = make_array_from_query(select_user_orders($id['uid'], $count, $since, $max), true);
            
         $all = array();
         $orders_all = array();       
         
        if(isset($orders['key_points'])){
            $orders['key_points'] = make_array_from_query(select_key_points($orders['key_points']));//не менять кодировку
                          
             $i = 0;
                
                if(isset($orders['key_points'][0])){
                
                    foreach($orders['key_points'] as $gps){
                        
                        /*-Создание GPS-*/
                        $orders['key_points'][$i]['gps'] = array(
                            "lat" => $gps['lat'],
                            "lng" => $gps['lng']
                        );
                        unset($orders['key_points'][$i]['lat']);
                        unset($orders['key_points'][$i]['lng']);
                        unset($orders['key_points'][$i]['order_id']);
                        
                        /*-Создание города-*/
                        
                        $city = make_array_from_query(select_where('cities', array ( 'uid' => $orders['key_points'][$i]['city'], 'lang' => $config['lang']), true));
                        
                        $orders['key_points'][$i]['city'] = $city;
                        
                        $i++;
                    }
                } else {
                    
                    $ord = $orders['key_points'];
                    unset($orders['key_points']);
                    
                    $orders['key_points'][0] = $ord;
                    
                    $orders['key_points'][0]['gps'] = array(
                            "lat" => $ord['lat'],
                            "lng" => $ord['lng']
                        );
                        unset($orders['key_points'][0]['lat']);
                        unset($orders['key_points'][0]['lng']);
                        unset($orders['key_points'][0]['order_id']);
                        
                        /*-Создание города-*/
                        
                        $city = make_array_from_query(select_where('cities', array ( 'uid' => $orders['key_points'][0]['city'], 'lang' => $config['lang']), true));
                        
                        
                        $orders['key_points'][0]['city'] = $city;
                }
                
                $orders['services']  =  make_array_from_query(select_services($orders['ordertype']));
                
                if(!isset($orders['services']['name']) && isset($orders['services'][0]['name'])){
                    for($i=0; $i < count($orders['services']); $i++){
                        
                        if($orders['services'][$i]['uid'] != 1){
                            $orders['services'][$i]['type'] = $orders['services'][$i]['uid'];
                        } else {
                            unset($orders['services'][$i]);
                        }
                    }
                } elseif(isset($orders['services']['name'])) {
                    $orders['services']['type'] = $orders['services']['uid'];
                } else {
                    $orders['services'] = array();
                }
                
                $car_info = explode(',', $orders['driver_info']);
                
                $orders['car'] = array(
                    'model' => iconv('cp1251', 'utf-8', $car_info[1]),
                    'number' => iconv('cp1251', 'utf-8', $car_info[0]),
                    'phone' => $car_info[3]            
                );
                
                $dat = $orders['pretime'];
                   // Разбиение строки в 3 части - date, time and AM/PM 
                $dt_elements = explode(' ',$dat);
                
                // Разбиение даты
                $date_elements = explode('-',$dt_elements[0]);
                
                // Разбиение времени
                $time_elements =  explode(':',$dt_elements[1]);
                
                // вывод результата
                $orders['date']  = mktime($time_elements[0], $time_elements[1],$time_elements[2], $date_elements[1],$date_elements[2], $date_elements[0]);
                
                
                unset($orders['ordertype']);
                unset($orders['driver_info']);
            
            $orders_all[] = $orders; 
        } else {
            
            
            foreach($orders as $pre_order){
                           
                $pre_order['key_points'] =  make_array_from_query(select_key_points($pre_order['key_points']));//не менять кодировку
                                
                
                $i = 0;
                $on_city = false;
                if(isset($pre_order['key_points'][0])){
                
                    foreach($pre_order['key_points'] as $gps){
                        
                        /*-Создание GPS-*/
                        $pre_order['key_points'][$i]['gps'] = array(
                            "lat" => $gps['lat'],
                            "lng" => $gps['lng']
                        );
                        unset($pre_order['key_points'][$i]['lat']);
                        unset($pre_order['key_points'][$i]['lng']);
                        unset($pre_order['key_points'][$i]['order_id']);
                        
                        /*-Создание города-*/
                        
                        $city = make_array_from_query(select_where('cities', array ( 'uid' => $pre_order['key_points'][$i]['city'], 'lang' => $config['lang']), true));
                        
                        $pre_order['key_points'][$i]['city'] = $city;
                        
                        $i++;
                    }
                    
                    //$pre_order['services']  =  make_array_from_query(select_services($pre_order['ordertype']));//не слать если 
                    
                } else {
                    
                    $ord = $pre_order['key_points'];
                    unset($pre_order['key_points']);
                    
                    $pre_order['key_points'][0] = $ord;
                    
                     $pre_order['key_points'][0]['gps'] = array(
                            "lat" => $ord['lat'],
                            "lng" => $ord['lng']
                        );
                        unset($pre_order['key_points'][0]['lat']);
                        unset($pre_order['key_points'][0]['lng']);
                        unset($pre_order['key_points'][0]['order_id']);
                        
                        /*-Создание города-*/
                        
                        $city = make_array_from_query(select_where('cities', array ( 'uid' => $ord['city'], 'lang' => $config['lang']), true));
                        
                        $pre_order['key_points'][0]['city'] = $city;
                        
                        $on_city = true;
                        //$pre_order['services']  = "";//не слать если только по городу
                }
                
                
                $pre_order['services']  =  make_array_from_query(select_services($pre_order['ordertype']));
                
                //$pre_order['services']  = (!isset($pre_order['services'][0]) && $pre_order['services']['uid'] == 1 )?"":$pre_order['services'];
                
                $orda = array();
                
                if(isset($pre_order['services'][0])){
                                        
                    $j = 0;
                    for($i=0; $i < count($pre_order['services']); $i++){
                    
                        if($pre_order['services'][$i]['uid'] != 1){
                            $orda[$j] = $pre_order['services'][$i];
                            $orda[$j]['type'] = $pre_order['services'][$i]['uid'];
                            $j++;
                        } 
                    
                    }
                    $pre_order['services'] = $orda;
                    
                 } elseif(isset($pre_order['services']['name']) && $on_city) {
                    $pre_order['services']['type'] = $pre_order['services']['uid'];
                } else {
                    $pre_order['services'] = array();
                }
                
                $car_info = explode(',', $pre_order['driver_info']);
                
                $dat = $pre_order['date'];
    
                // Разбиение строки в 3 части - date, time and AM/PM 
                $dt_elements = explode(' ',$dat);
                
                // Разбиение даты
                $date_elements = explode('-',$dt_elements[0]);
                
                // Разбиение времени
                $time_elements =  explode(':',$dt_elements[1]);
                
                // вывод результата
                $pre_order['date']  = mktime($time_elements[0], $time_elements[1],$time_elements[2], $date_elements[1],$date_elements[2], $date_elements[0]);
                
                if(isset($car_info[3])){
                
                    $pre_order['car'] = array(
                        'model' => iconv('cp1251', 'utf-8', $car_info[1]),
                        'number' => iconv('cp1251', 'utf-8', $car_info[0]),
                        'phone' => $car_info[3]            
                    );
                    
                }
                
                unset($pre_order['ordertype']);
                unset($pre_order['driver_info']);
                
                $orders_all[] = $pre_order;
               //print_r($keys);
            }
        }
        
        $all['orders'] = $orders_all;
    }
    
     $all['status'] = array(
        'code' => (isset($id['uid']))?'0':'1',
        'message' => (isset($id['uid']))?'User Ok':'No User'    
    );
    
    if(!empty($php_errormsg)){
            $all['status']['debugInfo'] = $php_errormsg; 
    }
    
   echo jdecoder(json_encode($all));
            
        return;
        
?>