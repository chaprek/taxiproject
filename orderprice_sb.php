<?php

	require_once("apievos/apievos.php");


	$WebOrder = new weborders($config['evos_url'][$city_id]);
	//Получаем ответ на запрос

//print_r($config['evos_url'][$city_id]);
		$data="http://maps.googleapis.com/maps/api/geocode/json?sensor=false&language=ru&address=";
	
		$route = array();
	
		if(isset($_POST['key_point'])){
		
			$keys = json_decode($_POST['key_point']); 
			 
			if($keys != null){
				
				$count = count($keys);
																
				foreach($keys as $key){
					
					if(isset($key->key_number)){
						if(empty($key->gps->lat)){
							if(strpos($key->street, "_") === false){
								$route[$key->key_number - 1]['name'] = mb_strtoupper(trim(str_replace("_", " ", $key->street)), "UTF-8");
								$route[$key->key_number - 1]['number'] = $key->house;
							} else {
								$route[$key->key_number - 1]['name'] = trim(str_replace("_", " ", $key->street));                                
							}
						} else {
							$route[$key->key_number - 1]['name'] = "".str_replace(" ", "", $key->city->name).",".str_replace(" ", "_", str_replace("'", "", $key->street)).",".str_replace(" ", "", $key->house);
							$route[$key->key_number - 1]['lat'] = $key->gps->lat;
							$route[$key->key_number - 1]['lng'] = $key->gps->lng;
						}
					}
				}  
			} 
		}
		$all = array(); 
		
		$price = 0;

		/*-Сортировка точек от первой-*/
		ksort($route);
	
		if(isset($_POST['authToken'])){
			$id = make_array_from_query(select_where('clients', array('authToken' => $_POST['authToken']), true));
		}
		  
		$card = (isset($id['cardnumber']))?$id['cardnumber']:"";
		$phone = (isset($id['phone']))?$id['phone']:"";
			
		$serv = 0;
		  
		if(isset($_POST['services'])){
	
			if(!is_array($_POST['services'])){
			   $cond = explode(',', $_POST['services']);
			} else {
			   $cond = $_POST['services'];
			}    
				 
			$serv = array(
				 '17' => false,
				 '10' => false,
				 '12' => false,
				 '4' => false,
				 '1' => false
			);
				 
			foreach($cond as $val){                 
				$serv[$val] = true;
			}    
				
			$serv['1'] = (count($route) > 1 )?false:true;
		}
				 
		  
		$cmd = $WebOrder->orderPrice($route, $serv, $card, $phone);
		  
		if(json_decode($cmd) != null){

			$fp=fopen("post_acces.log","a");
			fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---ORDERPRICE_SB_LOG---\n". $_SERVER['REQUEST_URI']."\n".$cmd."\n-\n-\n");
			fclose($fp);
			
			$all['price'] = 1*json_decode($cmd)->order_cost;
		} else {
			$all['price'] = 0;
			$error = true;
		}
		
		$all['discount'] = 0;
		$all['car_wait_time'] = 10;
				
		$mess = (!$error)?"Ok":"Error distance";
		$code =(!$error)?0:10;

?>