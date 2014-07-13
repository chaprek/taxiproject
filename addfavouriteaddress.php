<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    
    
    
    if(isset($_POST['authToken'])){
        $auth = array('authToken' => $_POST['authToken']);
    } else {
        $auth = array('authToken' => 13);        
    }
    
    $id = make_array_from_query(select_where('clients', $auth, true)); 
     
    if(isset($id['uid'])){ 
        
        if(isset($_POST['key_point'])){
            $arr = json_decode($_POST['key_point']); 
        } else {
            $arr = null;
        }
        
        
       if($arr != null){ 
        
            $all = array();
            $createorder = array();
            
                    $createorder['street'] = (isset($arr->street))?iconv('utf-8', 'cp1251', $arr->street):"";
                    $createorder['house'] = (isset($arr->house))?iconv('utf-8', 'cp1251', $arr->house):"";
                    $createorder['entranse'] = (isset($arr->entranse))?iconv('utf-8', 'cp1251', $arr->entranse):""; 
                    $createorder['comment'] = (isset($arr->comment))?iconv('utf-8', 'cp1251', $arr->comment):""; 
                    $createorder['city'] = (isset($arr->city->uid))?iconv('utf-8', 'cp1251', $arr->city->uid):"";
                    $createorder['lat'] = (isset($arr->gps->lat))?iconv('utf-8', 'cp1251', $arr->gps->lat):"";
                    $createorder['lng'] = (isset($arr->gps->lng))?iconv('utf-8', 'cp1251', $arr->gps->lng):""; 
                    $createorder['user_id'] = iconv('utf-8', 'cp1251', $id['uid']); 
                      
             $is_adr = $createorder;
             
             unset($is_adr['entranse']);
             unset($is_adr['lat']);
             unset($is_adr['lng']);
             unset($is_adr['comment']);
                     
                      
            $fadress = make_array_from_query(select_where('fav_adress', $is_adr, true));
                   
                      
            if(!isset($fadress['street'])){
                                    
                  $row_num = insert_in_line("fav_adress", $createorder, true);
              
               $fadress = make_array_from_query(select_where('fav_adress', array( 'uid' => $row_num), true));
            
                 $all = array();     
             
            
                if(isset($fadress['key_num'])){
                    
                     $fadress['city'] = make_array_from_query(select_where('cities', array ( 'uid' => $fadress['city'], 'lang' => $config['lang']), true));
                     $fadress['gps'] = array(
                        'lat' => $fadress['lat'],
                        'lng' => $fadress['lng']
                     );      
                     
                        unset($fadress['lat']);
                        unset($fadress['lng']);
                        unset($fadress['user_id']);
                           
                }
            } else {
                
                if(isset($fadress['key_num'])){
                    
                     $fadress['city'] = make_array_from_query(select_where('cities', array ( 'uid' => $fadress['city'], 'lang' => $config['lang']), true));
                     $fadress['gps'] = array(
                        'lat' => $fadress['lat'],
                        'lng' => $fadress['lng']
                     );      
                     
                        unset($fadress['lat']);
                        unset($fadress['lng']);
                        unset($fadress['user_id']);
                           
                }
                
            }  
      }
   }
    
    
    $all['status'] = array(
         'code' => (isset($id['uid']))?((isset($fadress))?'0':'4'):'1',
        'message' => (isset($id['uid']))?((isset($fadress))?'User Ok':'Wrong cod'):'No User'    
    );
    
    $all['point'] = (isset($fadress))?$fadress:"";
    
    if(!empty($php_errormsg)){
            $all['status']['debugInfo'] = $php_errormsg; 
    }
    
   echo jdecoder(json_encode($all));
            
        return;
        
        
        
?>