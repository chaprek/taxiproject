<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    
    
    if(isset($_GET['authToken'])){
        $auth = array('authToken' => $_GET['authToken']);
    } else {
        $auth = array('authToken' => 13);        
    }
        
    $id = make_array_from_query(select_where('clients', $auth, true)); 
   
   
   if(isset($id['uid'])){   
    
    extract(lasy_loading((isset($_GET['count']))?$_GET['count']:'', (isset($_GET['since_id']))?$_GET['since_id']:'', (isset($_GET['max_id']))?$_GET['max_id']:''));
        
    // первичная информация по заказам
    $hadress = make_array_from_query(select_hadress('kei_points', array( 'user_id' => $id['uid']), $count, $since, $max));
    //print_r($hadress);
    
    //echo "<pre>";
//    print_r($fadress);
//    echo "</pre>";

     $all = array();
     $orders_all = array();       
     
    
        if(isset($hadress['key_number'])){
            
             $hadress['city'] = make_array_from_query(select_where('cities', array ('uid' => $hadress['city'], 'lang' => $config['lang']), true));
             $hadress['gps'] = array(
                'lat' => $hadress['lat'],
                'lng' => $hadress['lng']
             );
             
                unset($hadress['lat']);
                unset($hadress['lng']);
                unset($hadress['user_id']);
                unset($hadress['order_id']);
             
            $orders_all[] = $hadress; 
            
        } else {
            foreach($hadress as $fadr){
                
             $fadr['city'] = make_array_from_query(select_where('cities', array ('uid' => $fadr['city'], 'lang' => $config['lang']), true));
             $fadr['gps'] = array(
                'lat' => $fadr['lat'],
                'lng' => $fadr['lng']
             );   
             
                unset($fadr['lat']);
                unset($fadr['lng']);
                unset($fadr['user_id']);
                unset($fadr['order_id']);
             
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