<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('bd_i.php');
    require_once ('display.php');
    
    
    $all = array();
    
    if(isset($_POST['authToken'])){
        $auth = array('authToken' => $_POST['authToken']);
    } else {
        $auth = array('authToken' => 13);        
    }
    
    $id = make_array_from_query(select_where('clients', $auth, true)); 
 

     $arr = array();
     
     if(isset($id['uid'])){   
            
        if(!empty($_POST['name'])){
            $arr['name'] = $ins_arr['name'] = iconv("UTF-8", "cp1251", $_POST['name']);
        }    
            
        if(!empty($_POST['phone'])){
            $arr['phone'] = valid_phone($_POST['phone']);
        }   
        
        if(!empty($_POST['services']) && $_POST['services'] != 1){
            $arr['services'] = str_replace('1,', '', $_POST['services']);
        }  
        
        if(!empty($_POST['city'])){
            $arr['city'] = $_POST['city'];
            $city_id = (!empty($_POST['city']))?$_POST['city']:$id['city'];
        } 
        if(!empty($_POST['city_id'])){
            $arr['city'] = $_POST['city_id'];
            $city_id = (!empty($_POST['city_id']))?$_POST['city_id']:$id['city'];
        } 
         
        if(!empty($_POST['card_number'])){
            $arr['cardnumber'] = $_POST['card_number'];
             //подключаем базу
             require_once("base/db".$city_id.".php");
            
            $card = check_card($_POST['card_number'], (isset($arr['phone']))?$arr['phone']:$id['phone'], $city_id, $id);
        }
     }
     
     if(isset($card) && !$card && $_POST['card_number'] != ""){
        unset($arr['cardnumber']);            
     }

     if(count($arr) > 0 && isset($id['uid'])){
        $pag = update_table('clients', $arr, array("uid" => $id['uid']), true);
      
        $id = make_array_from_query(select_where('clients', array("uid" => $id['uid']), true)); 
     } 
       
     if(isset($id['uid'])){ 
        
        $user = $id;
        array_splice($user, 6);
        $user['cardnumber'] = $id['cardnumber'];
        $all['user'] = $user;
        
        $all['user']['city'] = make_array_from_query(select_where('cities', array ('uid' => $all['user']['city'], 'lang' => $config['lang']), true));
        
        $all['user']['services']  =  make_array_from_query(select_services($all['user']['services']));
               
        if(!isset($all['user']['services']['name']) && isset($all['user']['services'][0]['name'])){
            for($i=0; $i < count($all['user']['services']); $i++){
                $all['user']['services'][$i]['type'] = $all['user']['services'][$i]['uid'];
            }
        } elseif(isset($all['user']['services']['name'])) {
            $all['user']['services']['type'] = $all['user']['services']['uid'];
        } else {
            $all['user']['services'] = array();
        }
        
      }            
    
     $mess = (isset($id['uid']))?(( (isset($pag) && $pag > 0) || !isset($pag))?"Ok":"No change"):"No User";
     $code = (isset($id['uid']))?(( (isset($pag) && $pag > 0) || !isset($pag))?0:7):1;
    
    $all['status'] = array(
        'code' => $code,
        'message' => $mess
    );       
    
     if(isset($card) && !$card && $_POST['card_number'] != ""){
        $all['status'] = array(
            'code' => '13',
            'message' => 'Wrong Card'
        );            
     } 
        
        
     if(!empty($php_errormsg)){
            $all['status']['debugInfo'] = $php_errormsg; 
    }
    
   echo jdecoder(json_encode($all));
            
        return;
        
?>