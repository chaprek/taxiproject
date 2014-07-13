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
            
            $arr = array(
                'rate' => $_POST['rate'],
                'text' => $_POST['text'],
                'user_id' => $id['uid']        
            );
            
        $pag = insert_in_line('review', arr_iconv_cp($arr), true);   
     }
        
        
    $all = array();
    
    $mess = (isset($id['uid']))?(($pag > 0)?"Ok":"DB ERROR"):"No User";
    $code = (isset($id['uid']))?(($pag > 0)?0:5):1;
    
    
    $all['status'] = array(
        'code' => $code,
        'message' => $mess
    );
    
   echo jdecoder(json_encode($all));
            
        return;
        
?>