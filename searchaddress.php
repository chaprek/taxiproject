<?php
    session_start();
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('bd_i.php');
    require_once ('display.php');
    
    header('Content-Type: application/json; charset=utf-8');
      
    require_once("apievos/apievos.php");

    $all = array();
    $error = false;
    
    if(isset($_GET['livestring']) && isset($_GET['city'])){
        
        if(in_array($_GET['city'], $config['first_base'])){
            
            
            require_once("base/db".$_GET['city'].".php"); 
            $new = make_array_from_query(select_streets($_GET['livestring'], $_GET['city']));
        
            $street_arr = array();
             
            if(is_array($new) && count($new) > 0){     
                foreach($new as $re){                        
                        $street_arr[] = array(
                            'address' => $re['name'],
                            'type' => 'street'
                        );
                }
                $all['data'] = $street_arr;
            } else {
                    $error = true;
            }
            
            
            
            
       //     $city = make_array_from_query(select_where('cities', array('uid' => $_GET['city'], 'lang' => $config['lang']), true));
//            $city = $city['name'];
//            
//            $data="https://maps.googleapis.com/maps/api/place/autocomplete/json?input=".urlencode( $city." ".$_GET['livestring'])."&types=geocode&components=country:ua&sensor=false&key=AIzaSyDl76KuHuAAg0thNBPloO3hrybv4_x5wPg";
//    
//            $request = $data;
//            
//            $curl  =  curl_init($request); 
//            /*-  Устанавливаем параметры  соединения-*/ 
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
//            
//            $json  =  curl_exec($curl);
//            
//            curl_close($curl);
//            
//            if($resp=json_decode($json, true)){
//            
//                $street_arr = array();
//                
//                foreach($resp['predictions'] as $re){
//                    if(count($re['terms'])){
//                        $street_arr[] = array(
//                            'address' => $re['terms'][0]['value'],
//                            'type' => 'street'
//                        );
//                    }
//                }
//                
//                $all['data'] = $street_arr;
//            } else {
//                $error = true;
//            }
            
        } else {
            
            $WebOrder = new weborders($config['evos_url'][$_GET['city']]);
            $cmd = $WebOrder->searchGeoDataByName($_GET['livestring']);
            
            $type_array = array(
                'railway' => 'ЖД', 
                'embassy' => 'П', 
                'autostation' => 'АВ', 
                'exhibition' => 'В', 
                'school' => 'ШК', 
                'hotel' => 'Г', 
                'supermapket' => 'С', 
                'restoran' => 'Р', 
                'metro' => 'М', 
                'kp' => 'КП', 
                'hospital' => 'БЦ'
            );
            
            $street_arr = array();
            
            if($streets = json_decode($cmd, true)){
                foreach($streets['geo_streets']['geo_street'] as $street){
                    $street_arr[] = array(
                        'address' => mb_ucasefirst($street['name']),
                        'type' => 'street'
                    );
                }
                foreach($streets['geo_objects']['geo_object'] as $street){
                    
                    $type = explode(" ", $street['name']);
                    
                    if(in_array($type[0], $type_array)){
                        foreach($type_array as $k=>$val){
                            if($type[0] == $val){
                                $type_add = $k;
                            }
                        }
                    } else {
                        $type_add = 'other';
                    }
                    
                    
                    $street_arr[] = array(
                        'address' => $street['name'],
                        'type' => $type_add
                    );
                }
                
                $all['data'] = $street_arr;
                
            } else {
                $error = true;
            }
            
        }
    } else {
        $error = true;
    }
   
    if(!$error){
        
        $all['status'] = array(
            'code' => '0',
            'message' => 'User Ok'
        );
        
    } else {
        
        $all['status'] = array(
            'code' => '4',
            'message' => 'Wrong cod'    
        );
        
    }
    
     if(!empty($php_errormsg)){
            $all['status']['debugInfo'] = $php_errormsg; 
    }
    
   echo jdecoder(json_encode($all));
            
        return;
        
        
        
?>