<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
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
    // первичная информация по заказам
        $fadress = make_array_from_query(select_where('fav_adress', array( 'user_id' => $id['uid']), true));

         $all = array();
         $orders_all = array();       
     
        if(isset($fadress['key_num'])){
            
             $fadress['city'] = make_array_from_query(select_where('cities', array ('uid' => $fadress['city'], 'lang' => $config['lang']), true));
             $fadress['gps'] = array(
                'lat' => $fadress['lat'],
                'lng' => $fadress['lng']
             );      
             
                unset($fadress['lat']);
                unset($fadress['lng']);
                unset($fadress['user_id']);
             
            $orders_all[] = $fadress; 
            
        } else {
                        
            foreach($fadress as $fadr){
                                
             $fadr['city'] = make_array_from_query(select_where('cities', array ( 'uid' => $fadr['city'], 'lang' => $config['lang']), true));
             $fadr['gps'] = array(
                'lat' => $fadr['lat'],
                'lng' => $fadr['lng']
             );      
             
                unset($fadr['lat']);
                unset($fadr['lng']);
                unset($fadr['user_id']);
             
            $orders_all[] = $fadr;
                
            }
        }
        if(count($orders_all) > 0){
            $all['points'] = $orders_all;
        }
    }
    
    $mess = (isset($id['uid']))?((count($orders_all))?"Ok":"No adress"):"No User";
    $code = (isset($id['uid']))?((count($orders_all))?0:6):1;
    
    
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