<?php
	session_start();
	header('Content-Type: application/json; charset=utf-8');
	require_once ('config.php');
	require_once ('config_bd.php');
	require_once ('bd.php');
	require_once ('bd_i.php');
	require_once ('display.php');
	
	
	$error = false;
	   
	if(isset($_POST['key_point'])){
		$keys = json_decode($_POST['key_point']); 
	}
	
	$city_id = "";

	foreach($keys as $key){
		if(isset($key->key_number)){
			if($key->key_number == 1){
				$city_id = $key->city->uid;
				$isstreet = $key->street;
				break;
			}
		}
	}
	
	$fp=fopen("price_log.log","a");
	fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---PRICE_POST_LOGS---\n". $_SERVER['REQUEST_URI']."\n".json_encode($_POST)."\n-\n-\n");
	fclose($fp);


	if(!empty($city_id) && !empty($isstreet)){
		if(in_array($city_id, $config['first_base'])){
			require_once("orderprice_fb.php");
		} else {
			require_once("orderprice_sb.php");
		}
	} else {
		$mess = "Error distance";
		$code = 10;
	}

	$all['status'] = array(
		'code' => $code,
		'message' => $mess
	);
	
	if(!empty($php_errormsg)){
			$all['status']['debugInfo'] = $php_errormsg; 
	}
	
	$fp=fopen("price_log.log","a");
	fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---PRICE_RESULT_LOGS---\n". $_SERVER['REQUEST_URI']."\n".jdecoder(json_encode($all))."\n-\n-\n");
	fclose($fp);

	echo jdecoder(json_encode($all));

		return;
		
?>