<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');

    
    
    $all = array();
    $auth = array('authToken' => $_POST['authToken']);
    
    $id = make_array_from_query(select_where('clients', $auth, true)); 

     if(isset($id['uid'])){
          
        $arr = array('pushToken' => $_POST['pushToken']);  
          
        $pag = update_table('clients', $arr, array("uid" => $id['uid']), true); 
         
     }
      
    //$mess = (isset($id['uid']))?(( (isset($pag) && $pag > 0) || !isset($pag))?"Ok":"No change"):"No User";
//    $code = (isset($id['uid']))?(( (isset($pag) && $pag > 0) || !isset($pag))?0:7):1;
    $mess = (isset($id['uid']))?"Ok":"No User";
    $code = (isset($id['uid']))?0:1;
    
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