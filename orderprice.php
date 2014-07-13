<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('bd_i.php');
    require_once ('display.php');
    
    
    $error = false;
       
    if(isset($_POST['key_point'])){
        $keys = json_decode($_POST['key_point']); 
    }
        
    foreach($keys as $key){
        if(isset($key->key_number)){
            if($key->key_number == 1){
                $city_id = $key->city->uid;
                break;
            }
        }
    }
        
    if(in_array($city_id, $config['first_base'])){
        require_once("orderprice_fb.php");
    } else {
        require_once("orderprice_sb.php");
    }
        
   $all['status'] = array(
        'code' => $code,
        'message' => $mess
    );
    
    if(!empty($php_errormsg)){
            $all['status']['debugInfo'] = $php_errormsg; 
    }
    
     echo jdecoder(json_encode($all));

        return;
        
?>