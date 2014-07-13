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
    
    $id = make_array_from_query(select_where('clients', $auth, true)); 
    
    $error = false; 
     
    $all = array();
     
    if(isset($id['uid'])){ 
        
        $base = true; 
            
            if(isset($_POST['orderId']) && isset($_POST['city_id'])){
                
                if(!in_array($_POST['city_id'], $config['first_base'])){
                    $base = false;           
                }
                
                if($base){
                    //подключаем базу
                    require_once("base/db".$_POST['city_id'].".php");
                    update_table('orders', array('meet' => 'Отказ', 'endtask' => 6), array('num' => $_POST['orderId']), $_POST['city_id']);
                } else {
                    //update_table_i('RunOrders', array('Comment' => 'Отказ', 'Close_Reason' => 1, 'BackColor' => 1, 'Zaezd' => 'Отказ'), array('Order_No' => $_POST['orderId']), $_POST['city_id']);
                //отмена заказа через апи
                    require_once("apievos/apievos.php");
                    $WebOrder = new weborders($config['evos_url'][$_POST['city_id']]);

                    $cmd = $WebOrder->cancelorder($_POST['orderId']);
                
                }
                delete_row('new_orders', array('num_row' => $_POST['orderId']), true);
            } else {
                $error = true;
                $code = '4';
                $mess = 'Wrong cod';
            }
        
   } else {
        $error = true;
        $code = '1';
        $mess = 'No User';
   }
   
    if(!$error){
        
        $all['status'] = array(
            'code' => '0',
            'message' => 'User Ok'    
        );
        
    } else {
        
        $all['status'] = array(
            'code' => $code,
            'message' => $mess    
        );
        
    }
    
     if(!empty($php_errormsg)){
            $all['status']['debugInfo'] = $php_errormsg; 
    }
    
   echo jdecoder(json_encode($all));
            
        return;
        
        
        
?>