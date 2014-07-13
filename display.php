<?php

function display($tmp, $arr){
    
   extract($arr);     

   return require_once("templates/".$tmp.".php");
 
}

function get_list($table = false, $lang = false){
    
    global $config;
    
    $alll = array();
        
                    
    if($table){
        
        if($lang){
            $datt = make_array_from_query(select_where($table, array('lang' => $config['lang']), true));
        } else {
            $datt = make_array_from_query(select_all($table, true));
        }
        
        $alll[$table] = $datt;
        //print_r($datt);
        $mess = (is_array($datt))?"Ok":"DB ERROR";
        $code = (is_array($datt))?0:5;
        
    } else {
        
        $mess = "Ok";
        $code = 0;
        
    }
         
    $alll['status'] = array(
        'code' => $code,
        'message' => $mess   
    );
    if(!empty($php_errormsg)){
            $alll['status']['debugInfo'] = $php_errormsg; 
    }
    return jdecoder(json_encode($alll));
}

function get_list_where($table, $cond){
    
        $alll = array();
        
    if($table){ 
        $datt = make_array_from_query(select_where($table, $cond, true));
        
        $alll[$table] = $datt;
        //print_r($datt);
        $mess = (is_array($datt))?"Ok":"DB ERROR";
        $code = (is_array($datt))?0:5;
        
    } else {
        $mess = "Ok";
        $code = 0;
    }
         
    $alll['status'] = array(
        'code' => $code,
        'message' => $mess   
    );
    if(!empty($php_errormsg)){
            $alll['status']['debugInfo'] = $php_errormsg; 
    }
    return jdecoder(json_encode($alll));
}


function upd_list($table, $cond, $where, $select = false){
    
    global $config;
    
        $alll = array();        
                                      
        $upd = update_table($table, $cond, $where, true);
                          
          // print_r($upd);                         
        if($select && $upd > 0){
            
            $cond = (isset($where['phone']))?array('phone' => $where['phone']):$cond;
            
            $datt = make_array_from_query(select_where($table, $cond, true), true);
            
           // print_r($datt);
            
            if($table == 'clients' ){
                $alll['user'] = $datt;
                
                
                
                if(!empty($alll['user']['services'])){
                
                    $alll['user']['services']  =  make_array_from_query(select_services($alll['user']['services']));
                 
                     if(is_array($alll['user']['services']) && !isset($alll['user']['services']['uid'])){
                        $s=0;
                        
                        foreach($alll['user']['services'] as $ser){
                            
                            $alll['user']['services'][$s]['type'] = $ser['uid'];
                            unset($alll['user']['services'][$s]['uid']);
                            $s++;
                        
                        }
                        
                     } else if(isset($alll['user']['services']['uid'])){
                        $alll['user']['services']['type'] = $alll['user']['services']['uid'];
                        unset($alll['user']['services']['uid']);
                     }
                    
                } else {
                    $alll['user']['services'] = "";
                }
                
                
                
               if(isset($alll['user']['city'])){
                
                    $alll['user']['city'] = make_array_from_query(select_where('cities', array ('uid' => $alll['user']['city'], 'lang' => $config['lang']), true));
               
               }
                                
                unset($alll['user']['active']);
                unset($alll['user']['os']);
                unset($alll['user']['block']);
                unset($alll['user']['discount']);
            }
        }
        
     $mess = ($upd > 0)?"Ok":(($table == 'clients')?"Wrong cod":"No user");
     $code = ($upd > 0)?0:(($table == 'clients')?4:1);
     
    $alll['status'] = array(
        'code' => $code,
        'message' => $mess   
    );
    if(!empty($php_errormsg)){
            $alll['status']['debugInfo'] = $php_errormsg; 
    }
    return $alll;
}

function jdecoder($json_str) {
  $cyr_chars = array (
    '\u0430' => 'а', '\u0410' => 'А',
    '\u0431' => 'б', '\u0411' => 'Б',
    '\u0432' => 'в', '\u0412' => 'В',
    '\u0433' => 'г', '\u0413' => 'Г',
    '\u0434' => 'д', '\u0414' => 'Д',
    '\u0435' => 'е', '\u0415' => 'Е',
    '\u0451' => 'ё', '\u0401' => 'Ё',
    '\u0436' => 'ж', '\u0416' => 'Ж',
    '\u0437' => 'з', '\u0417' => 'З',
    '\u0438' => 'и', '\u0418' => 'И',
    '\u0439' => 'й', '\u0419' => 'Й',
    '\u043a' => 'к', '\u041a' => 'К',
    '\u043b' => 'л', '\u041b' => 'Л',
    '\u043c' => 'м', '\u041c' => 'М',
    '\u043d' => 'н', '\u041d' => 'Н',
    '\u043e' => 'о', '\u041e' => 'О',
    '\u043f' => 'п', '\u041f' => 'П',
    '\u0440' => 'р', '\u0420' => 'Р',
    '\u0441' => 'с', '\u0421' => 'С',
    '\u0442' => 'т', '\u0422' => 'Т',
    '\u0443' => 'у', '\u0423' => 'У',
    '\u0444' => 'ф', '\u0424' => 'Ф',
    '\u0445' => 'х', '\u0425' => 'Х',
    '\u0446' => 'ц', '\u0426' => 'Ц',
    '\u0447' => 'ч', '\u0427' => 'Ч',
    '\u0448' => 'ш', '\u0428' => 'Ш',
    '\u0449' => 'щ', '\u0429' => 'Щ',
    '\u044a' => 'ъ', '\u042a' => 'Ъ',
    '\u044b' => 'ы', '\u042b' => 'Ы',
    '\u044c' => 'ь', '\u042c' => 'Ь',
    '\u044d' => 'э', '\u042d' => 'Э',
    '\u044e' => 'ю', '\u042e' => 'Ю',
    '\u044f' => 'я', '\u042f' => 'Я',
    '\u0456' => 'і', '\u0457' => 'ї',
    '\u0454' => 'є',
 
    '\r' => '',
    '\n' => '<br />',
    '\t' => ''
  );
 
  foreach ($cyr_chars as $key => $value) {
    $json_str = str_replace($key, $value, $json_str);
  }
  return $json_str;
}

function distance($lat1, $long1, $lat2, $long2)
    {

        $radius = 6372795;

        if ($lat2 == 0)
        {
            return;
        }
        // перевести координаты в радианы
        $lat1  = $lat1 * M_PI / 180;
        $lat2  = $lat2 * M_PI / 180;
        $long1 = $long1 * M_PI / 180;
        $long2 = $long2 * M_PI / 180;

        // косинусы и синусы широт и разницы долгот
        $cl1    = cos($lat1);
        $cl2    = cos($lat2);
        $sl1    = sin($lat1);
        $sl2    = sin($lat2);
        $delta  = $long2 - $long1;
        $cdelta = cos($delta);
        $sdelta = sin($delta);

        // вычисления длины большого круга
        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

        //
        $ad = atan2($y, $x);

        $dist = number_format($ad * $radius, 0, '.', ' ');

        return $dist;
    }



function mb_ucasefirst($str){ 
     $str = mb_strtolower($str, 'UTF-8');
   
     $str = mb_ereg_replace('^[\ ]+', '', $str);
     $str = mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8').
     mb_substr($str, 1, mb_strlen($str), 'UTF-8');
     return $str;    
} 

function post_request($data, $url, $login, $pwd)
    {
        $credent = sprintf('Authorization: Basic %s', base64_encode($login.":".$pwd));
        
        $params = array('http'=>array('method'=>'POST','content'=>$data, 'header'=>$credent));
        
        $ctx = stream_context_create($params);
        
        $fp=@fopen($url, 'rb', FALSE, $ctx);
        
        if ($fp){
            $response = @stream_get_contents($fp);
        } else {
            $response = false;
        }
        
        return $response;
    }

function registration($gget){
    
    $order = json_decode($gget, true);
        
    return insert_in_line("clients", $order, true);//insert order and return id of row
}

function valid_phone($phone){
    
      $phone = preg_replace("/\D/", "", $phone);
    
      if(strlen($phone) > 9 && strlen($phone) < 13){
                
                switch(strlen($phone)){
                
                    case 12:
                      $phone = $phone;
                      break;
                	case 11: 
                      $phone = '3'.$phone;
                      break;
                	case 10:
                      $phone = '38'.$phone;
                      break;
               	}
               
               return (int) $phone;
                
      } else {
        return false;
      }
}


function distance_count($dist, $cond = false,  $city = false){
    
    global $config;
    
    $rates = make_array_from_query(select_where('tariffs', array('city_id' => $city), true));
    
    $rat = array();
    
    foreach($rates as $val){
        
        $rat[$val['type']] = $val['rate'];
        
    }
    
    //$price = $dist*$rat['111'];
    
	$price = ($dist > $rat['333'])?($rat['222'] + ($dist - $rat['333'])*$rat['111']):$rat['222'];
	
    if($cond){
    
    if(!is_array($cond)){
        $cond = explode(',', $cond);
     }    
         
        foreach($cond as $val){           
                                                
            if($rat[$val] < 2){
                $price *= $rat[$val];
            } else {
                $price += $rat[$val];
            }
        }           
    }
    
    //$price = ($price > $rat['222'])?$price:$rat['222'];
    
    return $price;
}
function first_request($order, $list_query){
    
    //$order = json_decode($gget, true);
    
    /*-Mergin Querys-*/
    $big_arr = array_merge($order, $list_query);
    
    return insert_in_line("orders", $big_arr);//insert order and return id of row
    
}
function arr_iconv($array)
{
    if(is_array($array)){
        return array_map('arr_iconv', $array);
    } else {
        return iconv('cp1251', 'utf-8', $array);
    }
}
function arr_iconv_cp($array)
{
    if(is_array($array)){
        return array_map('arr_iconv_cp', $array);
    } else {
        return iconv('utf-8', 'cp1251', $array);
    }
}

/*-второй запрос пользователя с номером заказа для получения информации по таксисту-*/
function second_request($num, $id_order = false, $city){
           
                
    $infos = make_array_from_query(select_driver($num, 'orders', $city)); //выбираем id таксиста по номеру заказа 
      
      if(isset($infos[0])){
          $infos = $infos[count($infos) - 1];
      }
              
    if(!$infos || (isset($infos['driver']) && $infos['driver'] != 0)){//если таксист назначен либо такой строки нет в таблице (перенесена в таблицу orderscomplete)
        
        if(!$infos){ // если нет такой строки
            
            $infos = make_array_from_query(select_driver($num, 'orderscomplete', $city));//выбираем id таксиста из другой таблицы
            
              if(isset($infos[0])){
                  $infos = $infos[count($infos) - 1];
              }
                //print_r($infos);         
            if(isset($infos['driver']) && $infos['endtask'] == 1){ //если водитель есть
                
                $driver_info = make_array_from_query(select_driver_info($infos['driver'], $city), true);//узнаем необходимую информацию о таксисте
                
                $driver_info['pretime'] = $infos['pretime'];
                //удаляем запятые лишние                           
                $drive = $driver_info;
                foreach($drive as $k=>$val){
                    $driver_info[$k] = str_replace(",", "", $val);
                } 
                           
                $order_info = make_array_from_query(select_order_info($num, 'orderscomplete', $city), true);//узнаем информацию о заказе для переноса в локальную таблицу
                
                if(isset($order_info[0])){
                    $order_info = $order_info[count($order_info) - 1];
                }
            
            } elseif(isset($infos['endtask']) && $infos['endtask'] > 5) { // если заказ отменен
               
               return 'cancel';
                  
            } elseif(isset($infos['endtask']) && $infos['endtask'] == 5) { // если заказ отменен
               
               return 'nocar';
                  
            } else {
                return false;
            }
            
        } else {
            
             if(isset($infos['driver']) && $infos['driver'] > 0){
            
                $driver_info = make_array_from_query(select_driver_info($infos['driver'], $city), true);
                $driver_info['pretime'] = $infos['pretime'];
                //удаляем запятые лишние                           
                $drive = $driver_info;
                foreach($drive as $k=>$val){
                    $driver_info[$k] = str_replace(",", "", $val);
                } 
               
                $order_info = make_array_from_query(select_order_info($num, 'orders', $city), true);//узнаем информацию о заказе для переноса в локальную таблицу
                
            } elseif(isset($infos['endtask']) && $infos['endtask'] > 5) { // если заказ отменен
               
               return 'cancel';
                  
            } elseif(isset($infos['endtask']) && $infos['endtask'] == 5) { // если заказ отменен
               
               return 'nocar';
                  
            }
        }
        
        $keys = make_array_from_query(select_where('kei_points', array ('old_order_id' => $num), true)); 
        
        $order_info['key_points'] = array();
        
        if(isset($keys[0])){
            foreach($keys as $key){
                $order_info['key_points'][] = $key['uid'];
            }  
        } else {
            $order_info['key_points'][] = $keys['uid'];
        }
        
        $order_info['client_id'] = (isset($keys[0]))?$keys[0]['user_id']:$keys['user_id']; 
        $order_info['date'] = $order_info['ordertime']; 
        $order_info['price'] = $order_info['paysum'];  
        
        $id = make_array_from_query(select_where('clients', array('uid' => $order_info['client_id']), true));
        $order_info['os'] = $id['os'];
        
        unset($order_info['client']);
        unset($order_info['ordertime']);
        unset($order_info['paysum']);
         
        $order_info['key_points'] = implode(",", $order_info['key_points']);
         
        if(isset($driver_info['carnumber']) || (isset($infos['endtask']) && $order_info['endtask'] == 1)){  
            $order_info['driver_info'] = implode(', ', $driver_info);//добавляем отформатированную информацию о водителе
        }
        $order_info['pretime'] = $infos['pretime'];
        $order_info['old_num'] = $num;
         
        //проверяем не внесен ли уже этот заказ
        $id_o = make_array_from_query(select_where("orders", array('old_num' => $num), true), true); 
        if(!isset($id_o['uid'])){  
            $new_order_id = insert_in_line('orders', $order_info, true);
        } else { 
            return $id_o['uid'];
        }
        if(isset($keys[0])){
            foreach($keys as $key){
                update_table('kei_points', array('order_id' => $new_order_id), array('uid' => $key['uid']), true);
            } 
        } else {
            update_table('kei_points', array('order_id' => $new_order_id), array('uid' => $keys['uid']), true);
        }     
          
        if(!$id_order && isset($order_info['driver_info'])){
            return $order_info['driver_info'];
        } else {
            return $new_order_id;
        }
        
    } elseif(isset($infos['endtask']) && $infos['endtask'] > 5) { // если заказ отменен
               
               return 'cancel';
                  
    } elseif(isset($infos['endtask']) && $infos['endtask'] == 5) { // если заказ отменен
               
               return 'nocar';
                  
    } else {
        return false;
    }
}

function second_request_i($num, $id_order = false, $city){
                
    $infos = make_array_from_query_i(select_driver_i($num, 'RunOrders', $city), true);   //выбираем id таксиста по номеру заказа 
              
    if(!$infos || (isset($infos['Driver_No']) && $infos['Driver_No'] != 0)){//если таксист назначен либо такой строки нет в таблице (перенесена в таблицу orderscomplete)
        
        if(!$infos){
            
            $infos = make_array_from_query_i(select_driver_i($num, 'ArchiveOrders', $city), true);//выбираем id таксиста из другой таблицы
            
            if(isset($infos['Driver_No']) && $infos['Driver_No'] != 0 && $infos['Driver_No'] != "" && $infos['Driver_No'] != null){ // если таксист назначен
                
            //    $sign = make_array_from_query_i(select_where_i("Drivers", array('Driver_No' => $infos['Driver_No']), $city), true);
//                $sign = explode(" ", $sign['F']);
                
                $driver_info = make_array_from_query_i(select_driver_info_i($infos['Driver_No'], $infos['Signal'], $city), true);//узнаем необходимую информацию о таксисте
                
                $driver_info['pretime'] = $infos['Req_Start_Time'];
                //удаляем запятые лишние                           
                $drive = $driver_info;
                foreach($drive as $k=>$val){
                    $driver_info[$k] = str_replace(",", "", $val);
                } 
                        
            } elseif(isset($infos['Close_Reason']) && $infos['Close_Reason'] != 0 && $infos['Close_Reason'] != 4) {
                return 'cancel';
                
            } elseif(isset($infos['Close_Reason']) && $infos['Close_Reason'] == 4) {
                
                return 'nocar';
                
            } else {
                return false;
            }
            
        } else {
            
           // $sign = make_array_from_query_i(select_where_i("Drivers", array('Driver_No' => $infos['Driver_No']), $city), true);
//            $sign = explode(" ", $sign['F']);
                
            $driver_info = make_array_from_query_i(select_driver_info_i($infos['Driver_No'], $infos['Signal'], $city), true);//узнаем необходимую информацию о таксисте
            $driver_info['pretime'] = $infos['Req_Start_Time'];
            //удаляем запятые лишние                           
                $drive = $driver_info;
                foreach($drive as $k=>$val){
                    $driver_info[$k] = str_replace(",", "", $val);
                } 
            
        }
                
        $keys = make_array_from_query(select_where('kei_points', array ('old_order_id' => $num), true)); 
        
        $order_info['key_points'] = array();
        
        if(isset($keys[0])){
            foreach($keys as $key){
                $order_info['key_points'][] = $key['uid'];
                
                if($key['key_number'] == 1){
                    $order_info['town'] = $key['city'];
                }
            }  
        } else {
            
            $order_info['key_points'][] = $keys['uid'];
            $order_info['town'] = $keys['city'];
            
        }
        
        $order_info['ordertype'] = "1";
        $order_info['ordertype'] .= ($infos['Universal'] != 0)?",17":""; 
        $order_info['ordertype'] .= ($infos['Animal'] != 0)?",12":"";
        $order_info['ordertype'] .= ($infos['Baggage'] != 0)?",10":"";
        $order_info['ordertype'] .= ($infos['Condition'] != 0)?",4":"";
        
           
        $order_info['client_id'] = (isset($keys[0]))?$keys[0]['user_id']:$keys['user_id']; 
        $order_info['date'] = $infos['Creation_Time']; 
        $order_info['price'] = $infos['Cost'];  
        $order_info['phone'] = $infos['Phone'];  
        $order_info['driver'] = $infos['Driver_No'];  
        $order_info['pretime'] = $infos['Req_Start_Time'];
        
        $adr = explode(" ", $infos['Address']);
        $dest = explode(" ", $infos['Dest']);
        
        $street = "";
        $streetto = "";
        $a = 0;
        foreach($adr as $ad){
            if($a < count($adr)-1){
                $street .= $ad." ";
            } else {
                $house = $ad;
            }
            $a++;
        }
        
        $d = 0;
        foreach($dest as $de){
            if($d < count($dest)-1){
                $streetto .= $de." ";
            } else {
                $houseto = $de;
            }
            $d++;
        }
        
        $order_info['street'] = $street;
        $order_info['house'] = $house;
        $order_info['streetto'] = $streetto;
        $order_info['houseto'] = $houseto; 
        
         
        $order_info['key_points'] = implode(",", $order_info['key_points']);
           
        if(isset($driver_info['Car_No']) || (isset($infos['Close_Reason']) && $infos['Close_Reason'] == 0)){
            $order_info['driver_info'] = implode(', ', $driver_info);//добавляем отформатированную информацию о водителе
        }
        
        $order_info['old_num'] = $num;
        
        
         //проверяем не внесен ли уже этот заказ
        $id_o = make_array_from_query(select_where("orders", array('old_num' => $num), true), true); 
        if(!isset($id_o['uid'])){   
            
            if($city == 1){
                $order_info = arr_iconv_cp($order_info);
            }
            
            $new_order_id = insert_in_line('orders', $order_info, true);
        } else {
            return $id_o['uid'];
        }
        
        
        if(isset($keys[0])){       
            foreach($keys as $key){
                update_table('kei_points', array('order_id' => $new_order_id), array('uid' => $key['uid']), true);
            } 
        } else {
            update_table('kei_points', array('order_id' => $new_order_id), array('uid' => $keys['uid']), true);
        } 
        
        if(!$id_order && isset($order_info['driver_info'])){
            return $order_info['driver_info'];
        } else {
            return $new_order_id;
        }
    } elseif(isset($infos['Close_Reason']) && $infos['Close_Reason'] > 0 && $infos['Close_Reason'] != 4) {
                return 'cancel';
                
    } elseif(isset($infos['Close_Reason']) && $infos['Close_Reason'] == 4) {
                
                return 'nocar';
                
    } else {
        return false;
    }
}

function second_request_evos($num, $result, $id){
              
        $keys = make_array_from_query(select_where('kei_points', array ('old_order_id' => $num), true)); 
        
        $order_info['key_points'] = array();
        
        if(isset($keys[0])){
            foreach($keys as $key){
                $order_info['key_points'][] = $key['uid'];
                
                if($key['key_number'] == 1){
                    $order_info['town'] = $key['city'];
                    $order_info['ordertype'] = $key['ordertype'];
                    $order_info['street'] = $key['street'];
                    $order_info['house'] = $key['house'];
                } elseif($key['key_number'] == count($keys)){
                    $order_info['streetto'] = $key['street'];
                    $order_info['houseto'] = $key['house']; 
                }
                
            }  
        } else {
            
            $order_info['key_points'][] = $keys['uid'];
            $order_info['town'] = $keys['city'];
            $order_info['ordertype'] = $key['ordertype'];
            $order_info['street'] = $key['street'];
            $order_info['house'] = $key['house'];
        }
                
        $driver_info = explode(",", $result->order_car_info);
                     
        $order_info['client_id'] = (isset($keys[0]))?$keys[0]['user_id']:$keys['user_id'];
        $id = make_array_from_query(select_where('clients', array('uid' => $order_info['client_id']), true));   
           
        $order_info['date'] = date('Y.m.d H:i:m'); 
        $order_info['price'] = $result->order_cost;
        $order_info['phone'] = $id['phone'];  
        $order_info['os'] = $id['os']; 
        $order_info['pretime'] = $result->required_time;
       
        $order_info['key_points'] = implode(",", $order_info['key_points']);
         
        if(isset($driver_info[2])) { 
           $order_info['driver_info'] = $driver_info[0].",".$driver_info[2].",".$driver_info[1].", ,".$result->required_time;//добавляем отформатированную информацию о водителе
        } else {
           $order_info['driver_info'] = implode(" ", $driver_info);
        }
        
        $order_info['old_num'] = $num;
        
         //проверяем не внесен ли уже этот заказ
        $id_o = make_array_from_query(select_where("orders", array('old_num' => $num), true), true); 
        if(!isset($id_o['uid'])){   
            
            $order_info = arr_iconv_cp($order_info);
            
            $new_order_id = insert_in_line('orders', $order_info, true);
        } else {
            return $id_o['uid'];
        }
        
        if(isset($keys[0])){       
            foreach($keys as $key){
                update_table('kei_points', array('order_id' => $new_order_id), array('uid' => $key['uid']), true);
            } 
        } else {
            update_table('kei_points', array('order_id' => $new_order_id), array('uid' => $keys['uid']), true);
        } 
        
        if(isset($order_info['driver_info'])){
            return $order_info['driver_info'];
        } else {
            return $new_order_id;
        }

}

function add_popular_fav($arr){
     $fav = make_array_from_query(select_where('fav_adress', $arr, true));
     
     if(isset($fav['uid'])){
        
         update_table('fav_adress', array('popular' => $fav['popular']+1), array('uid' => $fav['uid']), true);
     
     }
}

function check_card($card, $phon, $city, $user = false){
    
    global $config;
   
    if(in_array($city, $config['first_base'])){
        
        $card = make_array_from_query(find_card($card, $city));
        
        if(empty($card['discountid'])){
            return false;
        } else {
            $type = make_array_from_query(select_where('discounts', array('num' => $card['discountid']), $city));
            
            if(isset($type['creditcard']) && $type['creditcard'] != 1){
                
                $rule = make_array_from_query(select_where('discount_det', array('discountid' => $card['discountid']), $city));
                
                $phone1 = $phon[2].$phon[3].$phon[4]."-".$phon[5].$phon[6].$phon[7]."-".$phon[8].$phon[9]."-".$phon[10].$phon[11];
                $phone2 = substr($phon, 2);
                
                $trips_count = make_array_from_query(find_trip_count($phone1, $phone2, $city));
                
                if(!isset($trips_count['usecount']) && $user !== false){
                    insert_in_line('refclients', array('phone' => $phone2, 'fname' => iconv('utf-8', 'cp1251', $user['name']), 'createdate' => date('Y-m-d H:i:m')), $city);
                    $trips_count = make_array_from_query(find_trip_count($phone1, $phone2, $city));
                }
                
                $trips_count = (isset($trips_count['usecount']))?$trips_count['usecount']:0;
                
                $res = array(
                    'rel' => 0,
                    'abs' => 0,                        
                );
                
                if(isset($rule['discountid'])){
                                                           
                    if(!empty($rule['relvalue'])){
                        
                        if(!empty($rule['useparams'])){
                            if(strpos($rule['useparams'], ">") !== false && $trips_count > preg_replace("/\D/", "", $rule['useparams'])){
                                $res['rel'] = $rule['relvalue'];
                            } elseif(strpos($rule['useparams'], "$") !== false && ($trips_count % preg_replace("/\D/", "", $rule['useparams'])) == 0){
                                $res['rel'] = $rule['relvalue'];
                            } 
                        } else {
                            $res['rel'] = $rule['relvalue'];
                        }                       
                        
                    } elseif(!empty($rule['absvalue'])){
                        
                        if(!empty($rule['useparams'])){
                            if(strpos($rule['useparams'], ">") !== false && $trips_count > preg_replace("/\D/", "", $rule['useparams'])){
                                $res['abs'] = abs($rule['absvalue']);
                            } elseif(strpos($rule['useparams'], "$") !== false && ($trips_count % preg_replace("/\D/", "", $rule['useparams'])) == 0){
                                $res['abs'] = abs($rule['absvalue']);
                            } 
                        } else {
                            $res['abs'] = abs($rule['absvalue']);
                        }
                        
                    }                   
                  
                  return $res;
                    
                } elseif(isset($rule[0]['discountid'])){
                                        
                    foreach($rule as $rul){
                        if(!empty($rul['relvalue'])){
                            
                            if(!empty($rul['useparams'])){
                                if(strpos($rul['useparams'], ">") !== false && $trips_count > (int) preg_replace("/\D/", "", $rul['useparams'])){
                                    $res['rel'] += $rul['relvalue'];
                                } elseif(strpos($rul['useparams'], "$") !== false && ($trips_count % preg_replace("/\D/", "", $rul['useparams'])) == 0){
                                    $res['rel'] += $rul['relvalue'];
                                } 
                            } else {
                                $res['rel'] += $rul['relvalue'];
                            } 
                            
                        } elseif(!empty($rul['absvalue'])){
                            
                            if(!empty($rul['useparams'])){
                                if(strpos($rul['useparams'], ">") !== false && $trips_count > preg_replace("/\D/", "", $rul['useparams'])){
                                    $res['abs'] += abs($rul['absvalue']);
                                } elseif(strpos($rul['useparams'], "$") !== false && ($trips_count % preg_replace("/\D/", "", $rul['useparams'])) == 0){
                                    $res['abs'] += abs($rul['absvalue']);
                                } 
                            } else {
                                $res['abs'] += abs($rul['absvalue']);
                            }
                            
                        }     
                    }
                    return $res;
                } else {
                    return false;
                }
                
            } else {
                return false;
            }
        }
        
    } else {
        
        $phone = $phon[2].$phon[3].$phon[4]."-".$phon[5].$phon[6].$phon[7]."-".$phon[8].$phon[9]."-".$phon[10].$phon[11];
        
        $card = make_array_from_query_i(find_card_i($card, $phone, $city));
        
         if(empty($card['Client_No'])){
            return false;
        } else {
            $res = array(
                    'rel' => 0,
                    'abs' => 0,                        
            );
            
            if(($card['IsActive'] == 1 || $card['IsLocked'] == 0) && $card['Category'] != 1){
                
                if($card['DiscountType'] == 0 ){
                   $rules = make_array_from_query_i(check_card_rule($city));
                    
                    $rules = explode(";", $rules['ParamValue']);
                    
                    foreach($rules as $rul){
                        if(!empty($rul)){
                            $rule = explode("=", $rul);
                            if($card['NumberOfTravels'] >= $rule[0]){
                                $res['rel'] = $rule[1];
                            }
                            
                        }
                    }
                    
                    //print_r($rules);
                    
                } elseif($card['DiscountType'] == 1 ){
                    $res['rel'] = $card['Discount'];
                } elseif($card['DiscountType'] == 2 ){
                    $res['abs'] = $card['Discount'];
                }
                
                return $res;
            } else {
                return false;
            }
            
        }
        
        //print_r($card);
        
    }
}


function orders_per_day($arr, $start){
    
    
    $start = $start." 23:59:59";
    $start_time = strtotime($start);
    
    $new_arr = array();
    $tim = array();
    $time = array();
    
    foreach($arr as $t){
        $tim[$t['uid']] = $t['date'];
    }
    
    sort($tim);
    
    foreach($tim as $k=>$t){
        $time[]['date'] = $t;
    }
//    echo "<pre>";
//    print_r($time);
//    echo "</pre>";
    
    $i = 0;
    for($s=0; $s < count($time); $s++ ){   
        
        $timestamp = strtotime($time[$s]['date']);
        
        if ( $timestamp === FALSE){
            echo "Строка (".$time[$s]['date'].") недопустима";
        } else{         
         
             if($timestamp < $start_time){
                $new_arr[$i][] = date('m/d', $timestamp);
                 //echo "f<br/>";
             } elseif($timestamp < strtotime(" + 24 hours", $start_time)) {
                if(isset($new_arr[0]))$i++;
                $s--;                
                //$new_arr[$i][] = date('y/m/d', $timestamp);
                $start_time = strtotime(" + 24 hours", $start_time);    
                // echo "s<br/>";     
             } else {
                //echo $i."s<br/>";
                $s--;
                //prev($arr);
                $start_time = strtotime(" + 24 hours", $start_time);
                //echo date('y/m/d', $timestamp)."---".date('y/m/d', $start_time)."l<br/>"; 
             }
        }
    }
    
    return $new_arr;
}
 /*-Lasy Loading-*/
function lasy_loading($count, $since_id, $max_id){
    
         $lasy = array();
    
         if(empty($count)){
            $lasy['count'] = 10;
         } else if($count > 100){
            $lasy['count'] = 100;
         } else {
            $lasy['count'] = $count;
         }
            
         if(!empty($since_id)){
            $lasy['since'] = $since_id;
         } else {
            $lasy['since'] = false;
         }
            
            
         if(!empty($max_id)){
            $lasy['max'] = $max_id;
         } else {
            $lasy['max'] = false;
         }
         
         return $lasy;
}

?>