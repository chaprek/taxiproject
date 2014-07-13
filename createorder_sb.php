<?php
            
            require_once("apievos/apievos.php");
            
            $data="http://maps.googleapis.com/maps/api/geocode/json?sensor=false&language=ru&address=";
            
            
            foreach($arr as $key){
                    
                    if(isset($key->key_number)){
                        if(empty($key->gps->lat)){
                            //$route[$key->key_number - 1]['name'] = $req[$key->key_number - 1] = "".str_replace(" ", "", $key->city->name).",".str_replace(" ", "_", $key->street).",".str_replace(" ", "", $key->house);
                        
                            //$route[$key->key_number - 1]['name'] = trim(str_replace("_", " ", $key->street));
//                            if(strpos($key->street, "_") === false){
//                                $route[$key->key_number - 1]['number'] = $key->house;
//                            }
                            
                            if(strpos($key->street, "_") === false){
                                $route[$key->key_number - 1]['name'] = mb_strtoupper(trim(str_replace("_", " ", $key->street)), "UTF-8");
                                $route[$key->key_number - 1]['number'] = $key->house;
                            } else {
                                $route[$key->key_number - 1]['name'] = trim(str_replace("_", " ", $key->street));                                
                            }
                        
                        } else {
                            $route[$key->key_number - 1]['name'] = "".str_replace(" ", "", $key->city->name).",".str_replace(" ", "_", $key->street).",".str_replace(" ", "", $key->house);
                            $route[$key->key_number - 1]['lat'] = $key->gps->lat;
                            $route[$key->key_number - 1]['lng'] = $key->gps->lng;
                        }
                    }
                } 
               
                ksort($route);
                            
                $serv = array(
                     '17' => (isset($createorder['Universal']))?true:false,
                     '10' => (isset($createorder['Baggage']))?true:false,
                     '12' => (isset($createorder['Animal']))?true:false,
                     '4' => (isset($createorder['Condition']))?true:false,
                     '1' => (isset($createorder['IsViaCity']))?true:false
                 );
                $user['name'] = $id['name'];
                $user['phone'] = $createorder['Phone'];
                $user['card'] = (isset($id['cardnumber']))?$id['cardnumber']:"";
                $user['addcost'] = (isset($_POST['additioanalmoney']))?str_replace("-", "", $_POST['additioanalmoney']):0;
                $user['req_time'] = (isset($createorder['Req_Start_Time']))?$createorder['Req_Start_Time']:null;
                $user['reserv'] = (isset($createorder['Req_Start_Time']))?true:false;
                $user['entrance'] = $createorder['Entrance'];
                               
                
                $WebOrder = new weborders($config['evos_url'][$city]);
                $cmd = $WebOrder->createOrder($route, $serv, $user);
                
                $row_num = json_decode($cmd)->dispatching_order_uid;

?>