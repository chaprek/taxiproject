<?php

    $order_result = make_array_from_query(select_where("orders", array('num' => $order_id), $city_id), true);
                            
                if(isset($order_result['driver']) && $order_result['driver'] != 0 && $order_result['driver'] != ""){
                
                    $adn = second_request($order_id, false, $city_id);
                            
                    //if($id_order > 0){
                        delete_row('new_orders', array('num_row' => $order_id), true);
                    //}
                    
                     $id_o = make_array_from_query(select_where("orders", array('old_num' => $order_id), true), true);                         
                     $id_order = (isset($id_o['uid']))?$id_o['uid']:$id_o[0]['uid'];
                     $orderStatus = "0";
                            
                     if($arr['pushToken'] != false && $config['os']){
                        send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderStatus);
                     }
                        
                    $orderStatus = "ORDER_READY";
                
                } elseif(isset($order_result['driver']) && $order_result['endtask'] != 0 ){
                    //добполнение для автоматического назначения машины
                    
                    if($order_result['endtask'] == 5){
                        $orderStatus = "ORDER_NO_CAR";
                        delete_row('new_orders', array('num_row' => $order_id), true);  
                          
                        $orderS = "2";
                        $adn = "no car";
                        $id_order = "";
                        if($arr['pushToken'] != false && $config['os']){
                            send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderS);
                        }
                    } elseif($order_result['endtask'] > 5){
                        $orderStatus = "ORDER_CANCEL";
                        delete_row('new_orders', array('num_row' => $order_id), true);  
                        
                        $orderS = "1";
                        $adn = "order cancel";
                        $id_order = "";
                        if($arr['pushToken'] != false && $config['os']){
                            send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderS);
                        } 
                    }
                    
                    
                    
                } elseif(isset($order_result['driver']) && ($order_result['driver'] == 0 || $order_result['driver'] == "") ){
                    
                    $orderStatus = "ORDER_SEARCH";
                    
                } else {
                    
                    $order_result = make_array_from_query(select_where("orderscomplete", array('oldnum' => $order_id), $city_id), true);
                    
                    if(isset($order_result[0])){
                        $order_result = $order_result[count($order_result) - 1];
                    }
                    
                    if(isset($order_result['driver']) && $order_result['driver'] != 0 && $order_result['driver'] != "" && $order_result['endtask'] == 1){
                        
                        $id_order = second_request($order_id, true, $city_id);
                        
                        if($id_order > 0){
                            delete_row('new_orders', array('num_row' => $order_id), true);
                        }
                        
                        $orderStatus = "ORDER_READY";
                        
                    } elseif(isset($order_result['endtask']) && $order_result['endtask'] == 5) {
                        $orderStatus = "ORDER_NO_CAR";
                        delete_row('new_orders', array('num_row' => $order_id), true);  
                          
                        $orderS = "2";
                        $adn = "Нет машины";
                        $id_order = "";
                        if($arr['pushToken'] != false && $config['os']){
                            send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderS);
                        }
                                              
                    } elseif(isset($order_result['endtask']) && $order_result['endtask'] > 5) {
                        $orderStatus = "ORDER_CANCEL";
                        delete_row('new_orders', array('num_row' => $order_id), true);  
                        
                        $orderS = "1";
                        $adn = "Заказ отменен";
                        $id_order = "";
                        if($arr['pushToken'] != false && $config['os']){
                            send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderS);
                        }                     
                    } else {
                        $orderStatus = "";
                    }
                        
                }
?>