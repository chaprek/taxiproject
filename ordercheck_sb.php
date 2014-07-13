<?php

   require_once("apievos/apievos.php");


	$WebOrder = new weborders($config['evos_url'][$city_id]);
    
    $cmd = $WebOrder->checkorder($order_id);
    $resp = json_decode($cmd);
    
    $close = 1*$resp->close_reason;
    $car_inf = $resp->order_car_info;

    if($close < 0 && empty($car_inf)){//машина в поиске
        $orderStatus = "ORDER_SEARCH";
    } elseif(($close < 1 || $close > 7) && !empty($car_inf)){//машина найдена
        $adn = second_request_evos($order_id, $resp, $id);
        delete_row('new_orders', array('num_row' => $order_id), true);
        
        $orderStatus = "0";
        
        $id_o = make_array_from_query(select_where("orders", array('old_num' => $order_id), true), true);  
        $id_order = (isset($id_o['uid']))?$id_o['uid']:$id_o[0]['uid'];   
                                
        if($arr['pushToken'] != false && $config['os']){
            send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderStatus);
        }
        
        //Лог ответа от апи евоса
        $fp=fopen("checkord.log","a");
        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n". $_SERVER['REQUEST_URI']."\n".$cmd."----------EVOS-API-RESPONSE---------------\n");
        fclose($fp);
                        
        $orderStatus = "ORDER_READY";
        
    } elseif($close == 4){ // нет машины
        $orderStatus = "ORDER_NO_CAR";
        delete_row('new_orders', array('num_row' => $order_id), true);   
                            
        $orderS = "2";
        $adn = "Нет машины";
        $id_order = "";
        if($arr['pushToken'] != false && $config['os']){
            send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderS);
        }
    } elseif(($close > 0 || $close < 8) && empty($car_inf)){ // отмена
        $orderStatus = "ORDER_CANCEL";
        delete_row('new_orders', array('num_row' => $order_id), true);    
                            
        $orderS = "1";
        $adn = "Заказ отменен";
        $id_order = "";
        if($arr['pushToken'] != false && $config['os']){
            send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderS);
        }
        
    } else { // хз что
        $orderStatus = "";
    }
 

?>