<?php

		$data="http://maps.googleapis.com/maps/api/directions/json?mode=driving&sensor=false&language=ru";
		
		$is_gps = false;
		
		if(isset($_POST['key_point'])){
		
			$keys = json_decode($_POST['key_point']); 
			 
			if($keys != null){
				
				$req = array();
				
				$count = count($keys);
				
				$s=0;
				foreach($keys as $key){
					
					if(isset($key->key_number)){
						if($key->key_number == 1){
							$req[0] = "&origin=".str_replace(" ", "", $key->city->name).",".str_replace(" ", "_", str_replace("'", "", $key->street)).",".str_replace(" ", "", $key->house);
						} else if($s == $count - 1) {
							$req[$key->key_number - 2] .="&destination=".str_replace(" ", "", $key->city->name).",".str_replace(" ", "_", str_replace("'", "", $key->street)).",".str_replace(" ", "", $key->house);
						} else {
							$req[$key->key_number - 1] = "&origin=".str_replace(" ", "", $key->city->name).",".str_replace(" ", "_", $key->street).",".str_replace(" ", "", $key->house);
							$req[$key->key_number - 2] .="&destination=".str_replace(" ", "", $key->city->name).",".str_replace(" ", "_", str_replace("'", "", $key->street)).",".str_replace(" ", "", $key->house);
						}
						
						if(!empty($key->gps->lat)){
							$is_gps = true;
						}
						
						$s++;
					}
				}
				
			} else {
				$data .= "&origin=49.438888,31.478888";
				$data.="&destination=49.431617,30.479936"; 
			} 
		
			
			
		} else {
			$data .= "&origin=49.438888,31.478888";
			$data.="&destination=49.431617,30.479936"; 
		}
		
		$all = array(); 
		
		if(!$is_gps){//если в адрессах нет координат то просчитываем через АПИ Такси Диспетчер
			
			require_once ('calculation.php');//подключаем класс просчета по такси диспетчер
			
			$id = array();

			if(!empty($_POST['authToken'])){ // узнаем есть ли у пользователя скидочная карта
				$id = make_array_from_query(select_where('clients', array('authToken' => $_POST['authToken']), true));
			}
			if(!empty($id['phone'])){
				$phone = $id['phone'];
			} else {
				$phone = '';
			}
				$usercard = (!empty($id['cardnumber']))?$id['cardnumber']:"";
				
				$fp=fopen("price_log.log","a");
		        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---PRICE_USER_LOGS---\n". $_SERVER['REQUEST_URI']."\n".json_encode($id)."\n-\n-\n");
		        fclose($fp);

				$price = new Calculation();
	
				$result = $price->tariffAction($_POST['key_point'], $city_id, $usercard, $phone);

				$fp=fopen("price_log.log","a");
		        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---PRICE_FB_RESULT_LOGS---\n". $_SERVER['REQUEST_URI']."\n".json_encode($result)."\n-\n-\n");
		        fclose($fp);
							
				$all['price'] = 1*$result['tariff'];
				$all['discount'] = 1*$result['discount'];
				$all['car_wait_time'] = 10;
			
		} else {
		
				
				$price = 0;
		
			foreach($req as $res){
				
				$request = $data.$res;
				
				$curl  =  curl_init($request); 
				//  Устанавливаем параметры  соединения 
				curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
				
				$json  =  curl_exec($curl);
				
				curl_close($curl);
				$resp=json_decode($json);
				
				if(!$resp){
					$error = true;
				} else {
					$price += mb_substr($resp->routes[0]->legs[0]->distance->text, 0, -5);    
				}
				 
				}
				//подключаем базу
				require_once("base/db".$city_id.".php");
				
				// Вычисляем стоимость
				
				$serv = (isset($_POST['services']))?$_POST['services']:0;
				$old_price = $all['price'] = 1*distance_count($price, $serv, $city_id);
				
				if(isset($_POST['authToken'])){
					
					$id = make_array_from_query(select_where('clients', array('authToken' => $_POST['authToken']), true));
					
					if(!empty($id['phone'])){
						$card = check_card($id['cardnumber'], $id['phone'], $id['city'], $id);
						
						if(is_array($card)){
							if($card['rel'] != 0){
								$all['price'] *= (1 - $card['rel']/100);
							}
							if($card['abs'] != 0){
								$all['price'] = $all['price'] - $card['abs'];
							}
						}
					}
				}
				
				
				$rates = make_array_from_query(select_where('tariffs', array('city_id' => $city_id), true));
				$rat = array();
				foreach($rates as $val){
					$rat[$val['type']] = $val['rate'];
				}
				
				 $all['price'] = 1*(($all['price'] > $rat['222'])?ceil($all['price']):ceil($rat['222']));//проверяем больше ли сумма минимального тарифа
				
				if(isset($card) && is_array($card) && count($rat)>0){
					$data="";
					foreach($card as $key=>$val){
							if(is_string($val) && strlen($val)>2000 )
									$val=substr($val,0,2000);
							$data.=$key."=>".$val."\n";
					}
				}
								
				$all['discount'] = ceil($old_price - $all['price']);
				$all['car_wait_time'] = 10;
		}
				
		$mess = (!$error)?"Ok":"Error distance";
		$code =(!$error)?0:10;

?>