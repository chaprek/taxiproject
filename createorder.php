<?php
	session_start();
	require_once ('config.php');
	require_once ('config_bd.php');
	require_once ('bd.php');
	require_once ('bd_i.php');
	require_once ('display.php');
	
	header('Content-Type: application/json; charset=utf-8');
	  
  
	  
	if(isset($_POST['authToken'])){
		$auth = array('authToken' => $_POST['authToken']);
	} else {
		$auth = array('authToken' => 13);        
	}
	
	$id = make_array_from_query(select_where('clients', $auth, true)); 
	
	/*-WEB ORDER-*/
	if(isset($_POST['webOrder'])){
		$id = make_array_from_query(select_where('clients', array('phone' => $_POST['phone']), true)); 
				
		if(!isset($id['uid']) && !isset($id[0]['uid'])){
			
			foreach(json_decode(str_replace("'", "", $_POST['key_point'])) as $k){
				if($k->key_number == 1){
					$city = $k->city->uid;           
				}
			}
			
			$ins_arr = array(
				"name" => $_POST['name'],
				"phone" => $_POST['phone'],
				"city" => $city
			);
			
			$client_id = insert_in_line("clients", $ins_arr, true);
			$id = make_array_from_query(select_where('clients', array('phone' => $_POST['phone']), true));
		}
		
	}
	/*-END WEB ORDER-*/
	
	$error = false; 
	$lot_orders = false; 
	$all = array();
	 
	if(isset($id['uid'])){
		
		//ограничение колличества заказов
		
		$count_orders = make_array_from_query(select_where('num_orders', array('user_id' => $id['uid']), true)); 
		   
		if(!isset($count_orders['count'])) {            
			insert_in_line("num_orders", array('user_id' => $id['uid'], 'time' => time()), true);
		} else {
					
			if($count_orders['count'] < $config['count_orders'] && (time() - $count_orders['time']) < $config['time_orders'] ){
				update_table('num_orders', array('count' => $count_orders['count']+1), array('user_id' => $id['uid']), true);
			} elseif((time() - $count_orders['time']) > $config['time_orders']){
				update_table('num_orders', array('count' => 0, 'time' => time()), array('user_id' => $id['uid']), true);
			} elseif($count_orders['count'] >= $config['count_orders'] && (time() - $count_orders['time']) < $config['time_orders']){
				$lot_orders = true;
				$error = true; 
				
				$all['status'] = array(
				 'code' => 11,
				 'message' => iconv('cp1251', 'utf-8', 'Не более 5-ти заказов в час')
				);
			}
		}
		
	if(!$lot_orders){
		
		if(isset($_POST['key_point'])){
			$arr = json_decode(str_replace("'", "", $_POST['key_point'])); 
		} else {
			$arr = null;
		}
		
	if($arr != null){ 
			
		foreach($arr as $k){
			if($k->key_number == 1){
				$base = (in_array($k->city->uid, $config['first_base']))?true:false;
				$city = $k->city->uid;           
			}
		}
		
		/*-Подготовка массива для создания заказа-*/
		if($base){
			$createorder = array(
				'preorder' => ((isset($_POST['time']) && $_POST['time'] != 0)?1:0), 
				'phone' => $id['phone'][2].$id['phone'][3].$id['phone'][4]."-".$id['phone'][5].$id['phone'][6].$id['phone'][7]."-".$id['phone'][8].$id['phone'][9]."-".$id['phone'][10].$id['phone'][11], 
				'driveupprice' => ((isset($_POST['additioanalmoney']))?str_replace("-", "", $_POST['additioanalmoney']):0),
				'client' => $id['uid'], 
				'clientname' => iconv('utf-8', 'cp1251', $id['name']), 
				'cash' => 1, 
				'oper' => $config['oper'], 
				'ordertime' => 'NOW()',
				'ordersum' => ((isset($_POST['price']))?$_POST['price']:0),
				'ordertype' => (isset($_POST['services']))?$_POST['services']:1  
			);    
			$createorder['paysum'] = $createorder['ordersum'] + $createorder['driveupprice'];
			if(isset($_POST['time']) && $_POST['time'] != 0){
				$createorder['pretime'] = date('Y-m-d H:i:m', $_POST['time']);
			}
			if(strpos($createorder['ordertype'], "1,") === false && $createorder['ordertype'] != 1){
				$createorder['ordertype'] = "1,".$createorder['ordertype'];
			}              
			
			
		} else {
				$createorder = array(
					'Creation_Time' => date('Y.m.d H:i:m'), 
					'Phone' => $id['phone'][2].$id['phone'][3].$id['phone'][4]."-".$id['phone'][5].$id['phone'][6].$id['phone'][7]."-".$id['phone'][8].$id['phone'][9]."-".$id['phone'][10].$id['phone'][11], 
					'AddCost' => ((isset($_POST['additioanalmoney']))?str_replace("-", "", $_POST['additioanalmoney']):0), 
					'ClientName' => ($city != 1)?iconv('utf-8', 'cp1251', $id['name']):$id['name']                   
				); 
				if((isset($_POST['time']) && $_POST['time'] != 0)){
					$createorder['Req_Start_Time'] = date('Y-m-d H:i:m', $_POST['time']);
					$createorder['Type'] = 1;
				}
				$createorder['Cost'] = ((isset($_POST['price']))?$_POST['price']:0) + $createorder['AddCost']; 
				
				if(isset($_POST['services'])){
				   $services = explode(",", $_POST['services']);
				}
				
				if(is_array($services)){
					foreach($services as $val){
						
						switch($val){
							case 1:
							$createorder['IsViaCity'] = 1;                    
							break;
							case 4:
							$createorder['Condition'] = 1;                    
							break;
							case 10:
							$createorder['Baggage'] = 1;                    
							break;
							case 12:
							$createorder['Animal'] = 1;                    
							break;
							case 17:
							$createorder['Universal'] = 1;                    
							break;
						}
					}    
				}        
		}
			
		$createrout = array();
		
		$keys = array();
		 
		$k=0;
		 
		foreach($arr as $key){
			
			if($key->key_number == 1){
				
				if($base){
					
					$keys[$k]['street'] = $createorder['street'] = $fs = (isset($key->street))?iconv('utf-8', 'cp1251', addslashes($key->street)):"";
					$keys[$k]['house'] =  $createorder['house'] = $fh = (isset($key->house))?iconv('utf-8', 'cp1251', $key->house):"";
					$keys[$k]['entranse'] =  $createorder['porch'] = (isset($key->entranse))?iconv('utf-8', 'cp1251', $key->entranse):""; 
					$keys[$k]['city'] = $createorder['town'] = (isset($key->city->uid))?iconv('utf-8', 'cp1251', $key->city->uid):"";
					
				   // $fs = iconv('utf-8', 'cp1251', $key->street);
//                    $fh = iconv('utf-8', 'cp1251', $key->house);
					
					$createorder['route'] = "$fs\t $fh\t 0\t -1\t 1\t 0\t 30.12.1899\t 0\t 0\t 0\t 0\t 0\t 0";
					
					//ADD POPULAR ADRESS
					$favarr = array('street' => $createorder['street'], 'house' => $createorder['house'], 'user_id' => $id['uid']);
					add_popular_fav($favarr);
					
				} else {
					
					if($city != 1){
						$createorder['Address'] = iconv('utf-8', 'cp1251', addslashes($key->street).", ".$key->house);
						$createorder['Apartment'] = (isset($key->flat) && !empty($key->flat))?iconv('utf-8', 'cp1251', $key->flat):0;
						
						$createrout[] = array(
							'Address_No' => $key->key_number,
							'Address' => iconv('utf-8', 'cp1251', addslashes($key->street).", ".$key->house)
						);
					} else {
						$createorder['Address'] = addslashes($key->street).", ".$key->house;
						$createorder['Apartment'] = (isset($key->flat) && !empty($key->flat))?$key->flat:0;
						
						$createrout[] = array(
							'Address_No' => $key->key_number,
							'Address' => addslashes($key->street).", ".$key->house
						);
					}
					$keys[$k]['street'] = (isset($key->street))?iconv('utf-8', 'cp1251', addslashes($key->street)):"1";
						$keys[$k]['house'] = (isset($key->house))?iconv('utf-8', 'cp1251', $key->house):"1";
						$keys[$k]['entranse'] = $createorder['Entrance'] = (isset($key->entranse) && !empty($key->entranse))?iconv('utf-8', 'cp1251', $key->entranse):"1"; 
						$keys[$k]['city'] = (isset($key->city->uid))?iconv('utf-8', 'cp1251', $key->city->uid):"1";
						$keys[$k]['user_id'] = $id['uid'];
					//ADD POPULAR ADRESS
					$favarr = array('street' => $keys[$k]['street'], 'house' => $keys[$k]['house'], 'user_id' => $id['uid']);
					add_popular_fav($favarr);
					
				}
				
				$keys[$k]['comment'] = (isset($key->comment))?iconv('utf-8', 'cp1251', $key->comment):""; 
				
				$keys[$k]['key_number'] = (isset($key->key_number))?$key->key_number:0;
				$keys[$k]['flat'] = (isset($key->flat))?iconv('utf-8', 'cp1251', $key->flat):"";
				$keys[$k]['lat'] = (isset($key->gps->lat))?iconv('utf-8', 'cp1251', $key->gps->lat):"";
				$keys[$k]['lng'] = (isset($key->gps->lng))?iconv('utf-8', 'cp1251', $key->gps->lng):""; 
				$keys[$k]['user_id'] = iconv('utf-8', 'cp1251', $id['uid']); 
				 
												   
			 } else if($key->key_number == count($arr)) {
				
				
				if($base){
					$fs = $keys[$k]['street'] = $createorder['streetto'] = (isset($key->street))?iconv('utf-8', 'cp1251', addslashes($key->street)):"";
					$fh = $keys[$k]['house'] = $createorder['houseto'] = (isset($key->house))?iconv('utf-8', 'cp1251', $key->house):"";
					
				  //  $fs = iconv('utf-8', 'cp1251', $key->street);
//                    $fh = iconv('utf-8', 'cp1251', $key->house);
					$createorder['route'] .= "
$fs \t $fh \t 0\t -1\t ".$createorder['ordertype']."\t 0\t 30.12.1899\t 0\t 0\t 0\t 0\t 0\t 0";


					//ADD POPULAR ADRESS
					$favarr = array('street' => $keys[$k]['street'], 'house' => $keys[$k]['house'], 'user_id' => $id['uid']);
					add_popular_fav($favarr);
				
				} else {
					$keys[$k]['street'] =  (isset($key->street))?iconv('utf-8', 'cp1251', addslashes($key->street)):"1";
					$keys[$k]['house'] =  (isset($key->house))?iconv('utf-8', 'cp1251', $key->house):"1";
					
					if($city != 1){
						$createorder['Dest'] = iconv('utf-8', 'cp1251', addslashes($key->street).", ".$key->house);
						 
						$createrout[] = array(
							'Address_No' => $key->key_number,
							'Address' => iconv('utf-8', 'cp1251', addslashes($key->street).", ".$key->house)
						);
					} else {
						$createorder['Dest'] = addslashes($key->street).", ".$key->house;
						 
						$createrout[] = array(
							'Address_No' => $key->key_number,
							'Address' => addslashes($key->street).", ".$key->house
						);
					}
					
					//ADD POPULAR ADRESS
					$favarr = array('street' => $keys[$k]['street'], 'house' => $keys[$k]['house'], 'user_id' => $id['uid']);
					add_popular_fav($favarr);
				}

				$keys[$k]['entranse']  = (isset($key->entranse))?iconv('utf-8', 'cp1251', $key->entranse):""; 
				$keys[$k]['comment'] = (isset($key->comment))?iconv('utf-8', 'cp1251', $key->comment):""; 
				
				$keys[$k]['key_number'] = (isset($key->key_number))?$key->key_number:0;
				$keys[$k]['flat'] = (isset($key->flat))?iconv('utf-8', 'cp1251', $key->flat):"";
				$keys[$k]['city'] = (isset($key->city->uid))?iconv('utf-8', 'cp1251', $key->city->uid):"";
				$keys[$k]['lat'] = (isset($key->gps->lat))?iconv('utf-8', 'cp1251', $key->gps->lat):"";
				$keys[$k]['lng'] = (isset($key->gps->lng))?iconv('utf-8', 'cp1251', $key->gps->lng):""; 
				$keys[$k]['user_id'] = iconv('utf-8', 'cp1251', $id['uid']); 
				
			 } else {
				
				$fs = $keys[$k]['street'] = (isset($key->street))?iconv('utf-8', 'cp1251', addslashes($key->street)):"";
				$fh = $keys[$k]['house'] = (isset($key->house))?iconv('utf-8', 'cp1251', $key->house):"";
				$keys[$k]['entranse']  = (isset($key->entranse))?iconv('utf-8', 'cp1251', $key->entranse):""; 
				$keys[$k]['comment'] = (isset($key->comment))?iconv('utf-8', 'cp1251', $key->comment):""; 
				
				$keys[$k]['key_number'] = (isset($key->key_number))?$key->key_number:0;
				$keys[$k]['flat'] = (isset($key->flat))?iconv('utf-8', 'cp1251', $key->flat):"";
				$keys[$k]['city'] = (isset($key->city->uid))?iconv('utf-8', 'cp1251', $key->city->uid):"";
				$keys[$k]['lat'] = (isset($key->gps->lat))?iconv('utf-8', 'cp1251', $key->gps->lat):"";
				$keys[$k]['lng'] = (isset($key->gps->lng))?iconv('utf-8', 'cp1251', $key->gps->lng):""; 
				$keys[$k]['user_id'] = iconv('utf-8', 'cp1251', $id['uid']); 
				
				//$fs = iconv('utf-8', 'cp1251', addslashes($key->street));
//                $fh = iconv('utf-8', 'cp1251', $key->house);
				
				if($base){
					$createorder['route'] .= "
$fs \t $fh \t 0\t -1\t 1\t 0\t 30.12.1899\t 0\t 0\t 0\t 0\t 0\t 0";
				} else {
					$createrout[] = array(
						'Address_No' => $key->key_number,
						'Address' => ($city != 1)?iconv('utf-8', 'cp1251', addslashes($key->street).", ".$key->house):addslashes($key->street).", ".$key->house
					);
				}
				
				
					//ADD POPULAR ADRESS
					$favarr = array('street' => $keys[$k]['street'], 'house' => $keys[$k]['house'], 'user_id' => $id['uid']);
					add_popular_fav($favarr);
				
			 }
			 $keys[$k]['ordertype'] = (isset($_POST['services']))?$_POST['services']:'';
			 $k++;       
		}      
		
		//тестовые юзеры
		if($id['phone'] != '380000000000' && $id['phone'] != '381111111111'){
			//отправляем заказ
			if($base){    
				require_once("base/db".$city.".php");
				$row_num = insert_in_line("orders", $createorder, $city);
			} else {            
				require_once("createorder_sb.php");
			}
			
			if($row_num){
				
				foreach($keys as $ke){
					$ke['old_order_id'] = $row_num;
					$ke['time'] = time();
					insert_in_line("kei_points", $ke, true);
				}
							
				insert_in_line("new_orders", array('num_row' => $row_num, 'pushToken' => $id['pushToken'], 'city_id' => $city, 'user_id' => $id['uid'], 'time' => time()), true);
			  }
		  } else {
				$row_num = 38;
		  }
		  
		  
		} else {
			$all['status'] = array(
				 'code' => 4,
				 'message' => 'Wrong cod'
			);
			$error = true;
		}
		
	  }  
   }
	if(!$error){
		$all['status'] = array(
			'code' => (isset($id['uid']))?'0':'1',
			'message' => (isset($id['uid']))?'User Ok':'No User'    
		);
		if(isset($row_num)){
		   $all['orderId'] = (string) $row_num;  
		   $all['car_wait_time'] = "10"; 
		   $all['operator_phones'] = $config['phones'][$city]; 
		}
	}
	
	 if(!empty($php_errormsg)){
			$all['status']['debugInfo'] = $php_errormsg; 
	}
	
   echo jdecoder(json_encode($all));
			
		return;
		
		
		
?>