<?php
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    require_once ('sample_push_custom.php');
        
   // send_push('3e01744aa100cfcffa1c031b0980eb42238b85533456445c60cb0870df1d50ae', 'Prihod Pusha', 123, 0);
        
    if(isset($_GET['authToken'])){
        $auth = array('authToken' => $_GET['authToken']);
    } else {
        $auth = array('authToken' => 13);        
    }
    
    $id = make_array_from_query(select_where('clients', $auth, true));
        
    if(isset($id['uid']) && $config['os']){                    
    
        send_push($id['pushToken'], $_GET['message'], $_GET['orderId'], $_GET['orderStatus']);
        
    }
            
?>