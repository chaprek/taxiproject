<?php
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('bd_i.php');
    require_once ('display.php');
    require_once ('sample_push_custom.php');
        
    //send_push('69dce1b3c582d45ecb3e06ff9f4857a37715e1adeef614e055fabed08a614a1c', 'Prihod Pusha', "15", "1");
     
                    
    $arr = make_array_from_query(select_all('new_orders', true));
      
    //  if($arr['user_id'] == 186){
//        
//            //$id_order = (string) $id_o['uid'];
//                              
//               $orderStatus = "0";
//               
//                    send_push($arr['pushToken'], arr_iconv('vnezapno'), '2402', $orderStatus);
//               
//                        
//              
//      }
      
        
    if(isset($arr[0])){  
                
        foreach($arr as $num){
            
            if(time() - $num['time'] < $config['timeout_orders']){
                //подключаем базы
                require_once("base/db".$num['city_id'].".php");
                
                if(in_array($num['city_id'], $config['first_base'])){
                  $adn = second_request($num['num_row'], false, $num['city_id']);
                } else {
                  /*-$adn = second_request_i($num['num_row'], false, $num['city_id']);-*/
                  
                    $id = make_array_from_query(select_where('clients', array('uid' => $num['user_id']), true));
                    $city_id = $num['city_id'];
                    $order_id = $num['num_row'];
                    require_once("ordercheck_sb.php");
                    return;  
                }
            
                if($adn !== false && $adn != 'cancel' && $adn != 'nocar' ){
                                        
                    $id_o = make_array_from_query(select_where("orders", array('old_num' => $num['num_row']), true), true);
                    $id_order = (string) $id_o['uid'];
                    
                    $orderStatus = "0";
                    if($num['pushToken'] != false && $config['os']){
                        send_push($num['pushToken'], arr_iconv($adn), $id_order, $orderStatus);
                    }
                    delete_row('new_orders', array('num_row' => $num['num_row']), true);
                    
                } elseif($adn == 'cancel'){ // если заказ отменен
                    
                    $orderStatus = "1";
                    $ad = "Заказ отменен";
                    $id_order = "";
                    if($num['pushToken'] != false && $config['os']){
                        send_push($num['pushToken'], iconv('cp1251', 'utf-8', $ad), $id_order, $orderStatus);
                    }
                    delete_row('new_orders', array('num_row' => $num['num_row']), true);
                    
                } elseif($adn == 'nocar'){ // если нет машины
                
                    $orderStatus = "2";
                    $ad = "Нет машины";
                    $id_order = "";
                    if($num['pushToken'] != false && $config['os']){
                        send_push($num['pushToken'], iconv('cp1251', 'utf-8', $ad), $id_order, $orderStatus);
                    }
                    delete_row('new_orders', array('num_row' => $num['num_row']), true);
                    
                }  
            } else {
            /*-Если прошло больше 5-ти минут возвращаем что нет машины-*/
                
                $id_order = "";                                
                $orderStatus = "2";
                $adn = "Нет машины";
                
                delete_row('new_orders', array('num_row' => $num['num_row']), true);
                    
                if(in_array($num['city_id'], $config['first_base'])){
                    update_table('orders', array('meet' => 'Отказ', 'endtask' => 6), array('num' => $num['num_row']), $num['city_id']);
                } else {                    
                    require_once("apievos/apievos.php");
                    $WebOrder = new weborders($config['evos_url'][$num['city_id']]);

                    $cmd = $WebOrder->cancelorder($num['num_row']);
                } 
                 
                 if($num['pushToken'] != false && $config['os']){
                    send_push($num['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderStatus);
                 }
                 
            }
        }
        
     } elseif(isset($arr['num_row'])){ 
        
        if(time() - $arr['time'] < $config['timeout_orders']){
        
            if(in_array($arr['city_id'], $config['first_base'])){
                //подключаем базу
                require_once("base/db".$arr['city_id'].".php");
                
                $adn = second_request($arr['num_row'], false, $arr['city_id']);
                
            } else {
                /*-$adn = second_request_i($arr['num_row'], false, $arr['city_id']);-*/  
                
                $id = make_array_from_query(select_where('clients', array('uid' => $arr['user_id']), true));
                $city_id = $arr['city_id'];
                $order_id = $arr['num_row'];
                require_once("ordercheck_sb.php");
                return;  
            }
            
             
            if($adn !== false && $adn != 'cancel' && $adn != 'nocar' ){
               
               $id_o = make_array_from_query(select_where("orders", array('old_num' => $arr['num_row']), true), true);
               $id_order = (string) $id_o['uid'];
                              
               $orderStatus = "0";
               
               if($config['os'] && $config['os']){
                    send_push($arr['pushToken'], arr_iconv($adn), $id_order, $orderStatus);
               }
               
               delete_row('new_orders', array('num_row' => $arr['num_row']), true);
               
            } elseif($adn == 'cancel'){ // если заказ отменен
                    $orderStatus = "1";
                    $ad = "Заказ отменен";
                    $id_order = "";
                    if($arr['pushToken'] != false && $config['os']){
                        send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $ad), $id_order, $orderStatus);
                    }
                    delete_row('new_orders', array('num_row' => $arr['num_row']), true);
                    
            } elseif($adn == 'nocar'){ // если нет машины
                    $orderStatus = "2";
                    $ad = "Нет машины";
                    $id_order = "";
                    if($arr['pushToken'] != false && $config['os']){
                        send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $ad), $id_order, $orderStatus);
                    }
                    delete_row('new_orders', array('num_row' => $arr['num_row']), true);
                    
            }
        
        }  else {
            /*-Если прошло больше 5-ти минут возвращаем что нет машины-*/
                
                $id_order = "";                                
                $orderStatus = "2";
                $adn = "Нет машины";
                
                delete_row('new_orders', array('num_row' => $arr['num_row']), true);
                    
                if(in_array($arr['city_id'], $config['first_base'])){
                    update_table('orders', array('meet' => 'Отказ', 'endtask' => 6), array('num' => $arr['num_row']), $arr['city_id']);
                } else {                    
                    require_once("apievos/apievos.php");
                    $WebOrder = new weborders($config['evos_url'][$arr['city_id']]);

                    $cmd = $WebOrder->cancelorder($arr['num_row']);
                } 
                 
                 if($arr['pushToken'] != false && $config['os']){
                    send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderStatus);
                 }
                    
                return;
                 
            } 
         
     } 
     
         
?>