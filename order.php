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
        
    $orderId =  (isset($_GET['id']))?$_GET['id']:0;  
        
    $id = make_array_from_query(select_where('clients', $auth, true)); 
     
     if(isset($id['uid'])){  
           
        // первичная информация по заказам
        $orders = make_array_from_query(select_user_order($id['uid'], $orderId), true);
            
        $all = array();      
         
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
                
                $orders['services']  = (!isset($orders['services'][0]) && $orders['services']['uid'] == 1 )?"":$orders['services'];
                
                if(!isset($orders['services']['name']) && isset($orders['services'][0]['name'])){
                    for($i=0; $i < count($orders['services']); $i++){
                        $orders['services'][$i]['type'] = $orders['services'][$i]['uid'];
                    }
                } elseif(isset($orders['services']['name'])) {
                    $orders['services']['type'] = $orders['services']['uid'];
                } else {
                    $orders['services'] = array();
                }
                
                                
                $car_info = arr_iconv(explode(',', $orders['driver_info']));
                   
                if(count($car_info) > 3){
                    $model = $car_info[1];
                    $number = $car_info[0];
                    $phone = $car_info[3];

                    $datecar = explode('T', $car_info[4]);
                    if(count($datecar) < 2){
                        $datecar = explode(' ', $car_info[4]);
                    }

                    $mdy = explode('-',$datecar[0]);
                
                    // Разбиение даты
                    $hmspre = explode('.',$datecar[1]);
                    $hms = explode(':',$hmspre[0]);

                    // вывод результата
                    $time  = mktime($hms[0], $hms[1],$hms[2], $mdy[1], $mdy[2], $mdy[0]);
                } else {
                    $car_info = arr_iconv(explode(' : ', $orders['driver_info']));

                    $car_info2 = explode(' ', $car_info[1]);

                    $model = $car_info2[2];
                    $number = $car_info2[0];
                    $phone = $car_info2[count($car_info2) - 1];

                    $pretime = explode(':', trim(substr($car_info[0], 2)));
                   // Разбиение строки в 3 части - date, time and AM/PM 
                    $dt_elements = explode(' ',$orders['date']);
                    // Разбиение даты
                    $date_elements = explode('-',$dt_elements[0]);

                    $time = mktime($pretime[0], $pretime[1],0, $date_elements[1], $date_elements[2], $date_elements[0]);
                }


                $orders['car'] = array(
                    'model' => $model,
                    'number' => $number,
                    'phone' => $phone,
                    'date' => $time           
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
            
        } 
        if(count($orders) > 0){
            $all['order'] = $orders;
        
            $all['order']['operator_phones'] = $config['phones'][$orders['town']];
        }
        
        unset($orders['town']);
    }
    
     $all['status'] = array(
        'code' => (isset($id['uid']))?'0':'1',
        'message' => (isset($id['uid']))?'User Ok':'No User'    
     );
       
    if(count($orders) < 1){
        $all['status'] = array(
            'code' => 4,
            'message' => 'Wrong Cod'    
        );
    }
    
    if(!empty($php_errormsg)){
            $all['status']['debugInfo'] = $php_errormsg; 
    }
    
   echo jdecoder(json_encode($all));
            
        return;
        
?>