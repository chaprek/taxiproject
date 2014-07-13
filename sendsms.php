<?php
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    
            $lot_sms = false;
            
            $phone = valid_phone($_GET['phone']);
            
       if(!empty($_GET['phone']) && $_GET['phone'] != 1111111111  && $_GET['phone'] != 0000000000){     
            if(!empty($_GET['name']) && !empty($_GET['phone']) && $phone){
                                
                $cod = rand (1111, 9999);   
                 
                $text = "СМС КОД: ".$cod; 
                   
                $ins_arr['name'] = iconv("UTF-8", "cp1251", $_GET['name']);
                $ins_arr['phone'] = $phone;
                
                if(!empty($_GET['city_id']) && !isset($_GET['city'])){
                
                    $ins_arr['city'] = $_GET['city_id'];
                    
                } elseif(!isset($_GET['city_id']) && !empty($_GET['city'])){
                    $ins_arr['city'] = $_GET['city'];
                } else {
                    $ins_arr['city'] = 1;
                }
                
                $ins_arr['os'] = ($config['os'])?'ios':'android';  
                                
                $ins_arr['authToken'] = $cod;
                $ins_arr['date'] = date('Y.m.d H:i:m');
                
                if(!empty($_GET['card_number'])){
                    $ins_arr['cardnumber'] = $_GET['card_number'];
                }
                                         
                //$id = make_array_from_query(select_where('clients', array('phone' => $phone, 'name' => $ins_arr['name']), true));
                $id = make_array_from_query(select_where('clients', array('phone' => $phone), true)); 
                
                if(!isset($id['uid']) && !isset($id[0]['uid'])){
                    $client_id = insert_in_line("clients", $ins_arr, true);
                    
                    insert_in_line("num_sms", array('user_id' => $client_id, 'time' => time()), true);
                    
                } else {
                    
                    $upd_arr = array(
                        'authToken' => $cod, 
                        'name' => $ins_arr['name']
                    );
                    if(!empty($ins_arr['city'])){
                       $upd_arr['city'] = $ins_arr['city'];
                    }
                    
                    if(!empty($_GET['card_number'])){
                        $upd_arr['cardnumber'] = $_GET['card_number'];
                    }
                    
                    update_table('clients', $upd_arr, array('uid' => $id['uid']), true);
                    
                    //вычисление колличества смс за день
                    $sms = make_array_from_query(select_where('num_sms', array('user_id' => $id['uid']), true)); 
                    
                    if(!$sms){
                        insert_in_line("num_sms", array('user_id' => $id['uid'], 'time' => time()), true);
                    } else {
                        if(isset($sms['count']) && $sms['count'] < $config['count_sms'] && (time() - $sms['time']) < $config['time_sms'] ){
                            update_table('num_sms', array('count' => $sms['count']+1), array('user_id' => $id['uid']), true);
                        } elseif(isset($sms['count']) && (time() - $sms['time']) > $config['time_sms']){
                            update_table('num_sms', array('count' => 0, 'time' => time()), array('user_id' => $id['uid']), true);
                        } elseif(isset($sms['count']) && $sms['count'] >= $config['count_sms'] && (time() - $sms['time']) < $config['time_sms']){
                            $lot_sms = true;
                        }
                    }          
                } 
                                
                    $to="+".$phone;
                         
                    $xml="<message><service id='single' validity='+2 hour' start='' /><to>$to</to>
                    <body content-type='plain/text' encoding='plain'>$text</body></message>";
                   
                    $answ = post_request($xml, 'http://bulk.bs-group.com.ua/clients.php', 'Taxi_Kiev', 'kudmtg5');
                      
            }
        }    
        
        //Catch errors
              
        if(empty($_GET['name'])){
            $code = 2;
            $mess = "No name";
        } elseif(!$phone){
            $code = 3;
            $mess = "Invalid phone";            
        } elseif($lot_sms){
            $code = 11;
            $mess = "Lot of attempts";
        } else {
            $code = 0;
            $mess = "Ok";            
        }
                
        $alll = array();
        
        $alll['status'] = array(
            'code' => $code,
            'message' => $mess     
        );
                
        if(!empty($php_errormsg)){
            $alll['status']['debugInfo'] = $php_errormsg; 
        }
        echo jdecoder(json_encode($alll));
    

?>