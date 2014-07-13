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
        
    $createorder = array();
    
       $fadress = make_array_from_query(select_where('fav_adress', array( 'uid' => (isset($_GET['addressId']))?$_GET['addressId']:0), true));
    
    if(isset($fadress['key_num'])){
            
             $fadress['city'] = make_array_from_query(select_where('cities', array ( 'uid' => $fadress['city']), true));
             $fadress['gps'] = array(
                'lat' => $fadress['lat'],
                'lng' => $fadress['lng']
             );      
             
                unset($fadress['lat']);
                unset($fadress['lng']);
                unset($fadress['user_id']);
      }
      
      delete_row('fav_adress', array('uid' => $_GET['addressId']), true);
      
   }
    
    
    
    $all = array();  
    
    if(isset($fadress['key_num'])){
    
        $all['status'] = array(
             'code' => (isset($id['uid']))?'0':'1',
            'message' => (isset($id['uid']))?'User Ok':'No User'    
        );
        
        $all['point'] = $fadress;
    } else {
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