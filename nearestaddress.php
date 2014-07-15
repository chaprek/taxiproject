<?php
	session_start();
	require_once ('config.php');
	require_once ('config_bd.php');
	require_once ('bd.php');
	require_once ('display.php');
	
	header('Content-Type: application/json; charset=utf-8');
 
	$error = false;
	
	$all = array();
	$alll = array();
	$all_streets = array();
	
	$radius = (isset($_GET['radius']))?$_GET['radius']:200;
	
	if(isset($_GET['lat']) && isset($_GET['lng'])){
		
		$data="http://maps.googleapis.com/maps/api/geocode/json?latlng=".$_GET['lat'].",".$_GET['lng']."&language=ru&region=ua&sensor=false";
		$curl  =  curl_init($data); 
		//  Устанавливаем параметры  соединения 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		$json  =  curl_exec($curl);
		curl_close($curl);
		 
		/*-Узнаем город-*/  
		if($resp=json_decode($json, true)){
			if(isset($resp['results'][0]['address_components'])){
				foreach($resp['results'][0]['address_components'] as $re){
					if($re['types'][0] == 'locality' && $re['types'][1] == 'political'){
						$cityname = $re['short_name'];
					}
					if($re['types'][0] == 'street_number'){
						$street_number = $re['short_name'];
					} else {
						$street_number = 1  ;
					}
					if($re['types'][0] == 'route'){
						$route = $re['short_name'];
					}
				}
				if(!empty($cityname)){
				$address = $route.", ".$street_number;
				}
			}
		}  
		
		if(!empty($cityname)){
		
			/*-Узнаем id города-*/
			$city = make_array_from_query(select_where('cities', array('name' => arr_iconv_cp($cityname), 'lang' => 'ru'), true));      
		
				/*-MAIN ADDRESS-*/
			if(!empty($city['uid'])){

				if(in_array($city['uid'], $config['first_base'])){
				
					$data="https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$_GET['lat'].",".$_GET['lng']."&language=ru&radius=".$radius."&sensor=false&key=AIzaSyDl76KuHuAAg0thNBPloO3hrybv4_x5wPg";
					
					//$data="https://maps.googleapis.com/maps/api/place/autocomplete/json?input=".urlencode("Киев деле")."&location=50.471535,30.479865&radius=200&components=country:ua&sensor=false&key=AIzaSyDl76KuHuAAg0thNBPloO3hrybv4_x5wPg";
					
					$curl  =  curl_init($data); 
					//  Устанавливаем параметры  соединения 
					curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
					
					$json  =  curl_exec($curl);
					
					curl_close($curl); 
					
					$streets = json_decode($json, true);
					
					if(is_array($streets['results'])){
						
						foreach($streets['results'] as $k=>$street){
							
							$distance = distance($_GET['lat'], $_GET['lng'], $street['geometry']['location']['lat'], $street['geometry']['location']['lng']);
							
							if(isset($street['types'][1]) && $street['types'][1] == "political"){
							} else {
								$all_streets[$distance] = array(
									"address" => $street['vicinity'],
									"type" => "street",
									"distance" => $distance,
									'gps' => array(
										'lat' => $street['geometry']['location']['lat'],
										'lng' => $street['geometry']['location']['lng'] 
									)
								);
							}
							
						}
					}
					
				} else {
					require_once("apievos/apievos.php");

					$WebOrder = new weborders($config['evos_url'][$city['uid']]);
					$cmd = $WebOrder->searchGeoDataByPos($_GET['lat'], $_GET['lng'], $radius);
					
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
					
					$places = json_decode($cmd, true);
					
					/*-ADD STREETS-*/
					
					if(is_array($places['geo_streets']['geo_street'])){
						foreach($places['geo_streets']['geo_street'] as $place){
							foreach($place['houses'] as $house){
								
								$distance = distance($_GET['lat'], $_GET['lng'], $house['lat'], $house['lng']);
								
								if($street_number != $house['house']){
									$all_streets[$distance] = array(
										 "address" => mb_ucasefirst($place['name']).", ".$house['house'],
										 "type" => "street",
										 "distance" => $distance,
										 'gps' => array(
											  'lat' => $house['lat'],
											  'lng' => $house['lng']
										  )
									);
								}
							}
						}
					}
					
					/*-ADD OBJECTS-*/
					if(is_array($places['geo_objects']['geo_object'])){
						foreach($places['geo_objects']['geo_object'] as $place){
							
							$distance = distance($_GET['lat'], $_GET['lng'], $place['lat'], $place['lng']);
								
							$type = explode(" ", $place['name']);
									
							if(in_array($type[0], $type_array)){
								foreach($type_array as $k=>$val){
									if($type[0] == $val){
										$type_add = $k;
									}
								}
							} else {
								$type_add = 'other';
							}    
								
							$all_streets[$distance] = array(
								"address" => $place['name'],
								"type" => $type_add,
								"distance" => $distance,
								'gps' => array(
									 'lat' => $place['lat'],
									 'lng' => $place['lng']
								)
							);
						}
					}
				}
			} else {
				$all_streets[0] = array(
					"address" => $address,
					"type" => "street",
					"distance" => 0,
					'gps' => array(
						'lat' => $_GET['lat'],
						'lng' => $_GET['lng']
					)
				);
			}

		}
	
		ksort($all_streets);

		// $fp=fopen("price_log.log","a");
		// fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---PRICE_RESULT_LOGS---\n". $_SERVER['REQUEST_URI']."\n".jdecoder(json_encode($all_streets))."\n-\n-\n");
		// fclose($fp);


				
		foreach($all_streets as $str){
			$all[] = $str;
		}
			
		$alll['data'] = $all;
	
	} else {
		$error = true;
	}
	
	if(!$error){
		
		$alll['status'] = array(
			'code' => '0',
			'message' => 'User Ok'
		);
		
	} else {
		
		$alll['status'] = array(
			'code' => '4',
			'message' => 'Wrong cod'    
		);
		
	}  
	
	if(!empty($php_errormsg)){
		$alll['status']['debugInfo'] = $php_errormsg; 
	}
	
	//print_r($alll);
	
	echo jdecoder(json_encode($alll));
			
	return;   
		
?>