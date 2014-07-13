<?php
    session_start();
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('bd_i.php');
    require_once ('display.php');
    
    header('Content-Type: application/json; charset=utf-8');
    
     if(isset($_POST['authToken'])){
        $auth = array('authToken' => $_POST['authToken']);
    } else {
        $auth = array('authToken' => 13);        
    }
      
    $city_id = (isset($_POST['city_id']))?$_POST['city_id']:0;  
    $order_id = (isset($_POST['id']))?$_POST['id']:0;
        
    $id = make_array_from_query(select_where('clients', $auth, true)); 
     
     $error = false;
     
     if(isset($id['uid'])){  
         
            
       if(in_array($city_id, $config['first_base'])){
        
            $infos = make_array_from_query(select_driver($order_id));   //выбираем id таксиста по номеру заказа 
                      
            if(isset($infos['driver'])){//
                
             
                    $driver_info = make_array_from_query(select_driver_info($infos['driver']), true);
                    //$driver_info['pretime'] = $infos['pretime'];
                    unset($driver_info['color']);
                    $order_info = make_array_from_query(select_order_info($order_id, 'orders'), true);//узнаем информацию о заказе для переноса в локальную таблицу
                
                
                $keys = make_array_from_query(select_where('kei_points', array ('order_id' => $order_id), true)); 
                
                $order_info['key_points'] = array();
                
                foreach($keys as $key){
                    $order_info['key_points'][] = $key['uid'];
                }  
                   
                $order_info['client_id'] = $keys[0]['user_id']; 
                $order_info['date'] = $order_info['ordertime'];  
                
                unset($order_info['client']);
                unset($order_info['ordertime']);
                
                $order_info['key_points'] = implode(",", $order_info['key_points']);
                                      
                $order_info['car'] = (isset($driver_info['model']))?$driver_info:array();//добавляем отформатированную информацию о водителе
                $order_info['pretime'] = $infos['pretime'];
                $order_info['price'] = $order_info['ordersum'];
                unset($order_info['ordersum']);
                                
            } else {
                    $error = true;
            }
            
        } else {
               
                       
                 $infos = make_array_from_query_i(select_driver_i($order_id));   //выбираем id таксиста по номеру заказа 
                            
                if(isset($infos['Driver_No'])){//если таксист назначен либо такой строки нет в таблице (перенесена в таблицу orderscomplete)
                    
                 if($infos['Driver_No'] != 0 && $infos['Driver_No'] != ""){
                        $sign = make_array_from_query_i(select_where_i("Drivers", array('Driver_No' => $infos['Driver_No'])), true);
                        $sign = explode(" ", $sign['F']);
                            
                        $driver_info = make_array_from_query_i(select_driver_info_i($infos['Driver_No'], $sign[0]));//узнаем необходимую информацию о таксисте
                        $driver_info['pretime'] = $infos['Req_Start_Time'];
                      }
                            
                    $keys = make_array_from_query(select_where('kei_points', array ('order_id' => $order_id), true)); 
                    
                    $order_info['key_points'] = array();
                    
                    foreach($keys as $key){
                        $order_info['key_points'][] = $key['uid'];
                        
                        if($key['key_number'] == 1){
                            $order_info['town'] = $key['city'];
                        }
                    }  
                       
                    $order_info['ordertype'] = "1";
                    $order_info['ordertype'] .= ($infos['Universal'] != 0)?",17":""; 
                    $order_info['ordertype'] .= ($infos['Animal'] != 0)?",12":"";
                    $order_info['ordertype'] .= ($infos['Baggage'] != 0)?",10":"";
                    $order_info['ordertype'] .= ($infos['Condition'] != 0)?",4":"";
                    
                       
                    $order_info['client_id'] = $keys[0]['user_id']; 
                    $order_info['date'] = $infos['Creation_Time']; 
                    $order_info['price'] = $infos['Cost'];  
                    $order_info['phone'] = $infos['Phone'];  
                    $order_info['driver'] = $infos['Driver_No'];  
                    $order_info['pretime'] = $infos['Req_Start_Time'];
                    
                    $adr = explode(" ", $infos['Address']);
                    $dest = explode(" ", $infos['Dest']);
                    
                    $street = "";
                    $streetto = "";
                    $a = 0;
                    foreach($adr as $ad){
                        if($a < count($adr)-1){
                            $street .= $ad." ";
                        } else {
                            $house = $ad;
                        }
                        $a++;
                    }
                    
                    $d = 0;
                    foreach($dest as $de){
                        if($d < count($dest)-1){
                            $streetto .= $de." ";
                        } else {
                            $houseto = $de;
                        }
                        $d++;
                    }
                    
                    $order_info['street'] = $street;
                    $order_info['house'] = $house;
                    $order_info['streetto'] = $streetto;
                    $order_info['houseto'] = $houseto; 
                    
                     
                    $order_info['key_points'] = implode(",", $order_info['key_points']);
                       
                    $order_info['car'] = (isset($driver_info['model']))?$driver_info:array();//добавляем отформатированную информацию о водителе
                    
                    $order_info['price'] = $infos['Cost'];                             
                    
                } else {
                    $error = true;
                }
           
      }       
          
          if(isset($order_info)){
                        
              $order_info['comment'] = (!empty($order_info['meet']))?$order_info['meet']:"";
              
              unset($order_info['town']);
              unset($order_info['street']);
              unset($order_info['house']);
              unset($order_info['houseto']);
              unset($order_info['route']);
              unset($order_info['driver']);
              unset($order_info['meet']);
              unset($order_info['phone']);
              unset($order_info['endtask']);
              unset($order_info['streetto']);
              unset($order_info['client_id']);
              unset($order_info['pretime']);
              unset($order_info['driver_info']);
              
                
             $order_info['uid'] = $order_id;
                          
            // первичная информация по заказам
             $orders = $order_info;
          } else {
            $orders = array();       
          }
         $all = array();
         $orders_all = array();       
         
        if(isset($orders['key_points'])){
            $orders['key_points'] = make_array_from_query(select_key_points($orders['key_points']));
             
             $i = 0;
                
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
                    
                    $city = make_array_from_query(select_where('cities', array ( 'uid' => $orders['key_points'][$i]['city']), true));
                    
                    $orders['key_points'][$i]['city'] = $city;
                    
                    $i++;
                }
                
                $orders['services']  =  make_array_from_query(select_services($orders['ordertype']));
                
                
                if(!isset($orders['services']['name']) && isset($orders['services'][0]['name'])){
                    for($i=0; $i < count($orders['services']); $i++){
                        $orders['services'][$i]['type'] = $orders['services'][$i]['uid'];
                    }
                    
                } elseif(isset($orders['services']['name'])) {
                    $orders['services']['type'] = $orders['services']['uid'];
                } else {
                    $orders['services'] = array();
                }
                
                $dat = $orders['date'];
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
        } 
        
        $all['order'] = $orders_all;
        $all['order']['uid'] = $order_id;
    }
    
     $all['status'] = array(
        'code' => (isset($id['uid']))?'0':'1',
        'message' => (isset($id['uid']))?'User Ok':'No User'    
     );
    
    if($error){
        $all['status'] = array(
        'code' => 4,
        'message' => 'Wrong cod'    
     );
    }
    
    if(!empty($php_errormsg)){
            $all['status']['debugInfo'] = $php_errormsg; 
    }
    
   echo jdecoder(json_encode($all));
            
        return;
        
?>