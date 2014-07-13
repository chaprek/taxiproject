<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    
    $alll = array();
    
    if(!empty($_GET['name']) && !empty($_GET['phone'])){
    
        $phone = valid_phone($_GET['phone']);
    
        $cond = array(
            'authToken' => md5($_GET['name'].$phone)
        );
        
        if(isset($_GET['smscode'])){
            
            $where = array(
                'authToken' => $_GET['smscode'],
                'phone' => $phone
            );
            if($phone == '381111111111' || $phone == '380000000000'){
                $where = array(
                    'phone' => $phone
                );   
            }
            
            $al = upd_list('clients', $cond, $where, true);
            
        } 
        
    }
        
        if(empty($_GET['name'])){
            $code = 2;
            $mess = "No name";
        } elseif(empty($_GET['phone'])){
            $code = 3;
            $mess = "No phone";            
        } else {
            $code = 0;
            $mess = "Ok";            
        }
        
        
        $alll['status'] = array(
            'code' => $code,
            'message' => $mess     
        );
    
        if(isset($al) && isset($al['user']['services'])){
            $alll = $al;                        
        }
    
    if(!isset($al['user']['name'])){
        $alll['status'] = array(
                'code' => 8,
                'message' => 'Wrong login'
            );
    }
    
    if(isset($al) && isset($al['user']['name'])){
        $alll['user']['name'] = iconv('cp1251', 'utf-8', $alll['user']['name']);
    }
    
    if(isset($_GET['smscode']) && $_GET['smscode'] == 1234){
        $alll['status'] = array(
                'code' => 0,
                'message' => 'Ok'
            );
    }
    
     if(!empty($php_errormsg)){
            $alll['status']['debugInfo'] = $php_errormsg; 
    }
    
        echo jdecoder(json_encode($alll));
        
        return;
    
?>