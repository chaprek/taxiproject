<?php
	require_once ('config.php');
	require_once ('config_bd.php');
	require_once ('bd.php');
	require_once ('bd_i.php');
	require_once ('display.php');
	require_once ('sample_push_custom.php');
		
	//send_push('69dce1b3c582d45ecb3e06ff9f4857a37715e1adeef614e055fabed08a614a1c', 'Prihod Pusha');
		
	if(isset($_GET['authToken'])){
		$auth = array('authToken' => $_GET['authToken']);
	} else {
		$auth = array('authToken' => 13);        
	}
	
	$all = array();
	$orderStatus = "";
	
	$city_id = (isset($_GET['city_id']))?$_GET['city_id']:0;  
	$order_id = (isset($_GET['id']))?$_GET['id']:0;
	
	$id = make_array_from_query(select_where('clients', $auth, true));
	 
	 
	$error = false;   
	$id_order = 0;
	
	if(isset($id['phone']) && $id['phone'] != '380000000000' && $id['phone'] != '381111111111'){
		$arr = make_array_from_query(select_where('new_orders', array('num_row' => $order_id), true));
	} else {
		$arr['num_row'] = 1;
	}
	
	
	
	 if(isset($id['uid'])){  
		
	  if(isset($arr['num_row']) && $id['phone'] != '381111111111'){ 
		
		if(time() - $arr['time'] < $config['timeout_orders']  && $id['phone'] != '380000000000'){
		 
		   if(in_array($city_id, $config['first_base'])){
			
				//ïîäêëþ÷àåì áàçó
				require_once("base/db".$city_id.".php");
			   require_once ('ordercheck_fb.php');
		   } else {
			   require_once ('ordercheck_sb.php');  
		   }
	 
		 } else {
			/*-Åñëè ïðîøëî áîëüøå 5-òè ìèíóò âîçâðàùàåì ÷òî íåò ìàøèíû-*/
				$all['orderId'] = "";
				$all['orderStatus'] = "ORDER_NO_CAR";
				
				$id_order = "";                                
				$orderStatus = "2";
				$adn = "Íåò ìàøèíû";
						 
				$all['status'] = array(
					'code' => '0',
					'message' => 'User Ok'
				);
				
				//òåñòîâûé þçåð
				if($id['phone'] != '380000000000'){
					delete_row('new_orders', array('num_row' => $order_id), true);
						
					if(in_array($city_id, $config['first_base'])){
						
						//ïîäêëþ÷àåì áàçó
						require_once("base/db".$city_id.".php");
						
						update_table('orders', array('meet' => 'Îòêàç', 'endtask' => 6), array('num' => $order_id), $city_id);
					} else {                    
						require_once("apievos/apievos.php");
						$WebOrder = new weborders($config['evos_url'][$city_id]);
	
						$cmd = $WebOrder->cancelorder($order_id);
					}  
				}
					 
										
				$json = jdecoder(json_encode(arr_iconv($all)));
	
				echo $json;
				
				//âìåñòî /home/user/data/www/site.ru/ óêàçûâàåì ñâîé ïóòü îò êîðíÿ ñåðâåðà, êóäà äîëæåí ïèñàòüñÿ ëîã
				$fp=fopen("checkord.log","a");
				fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n". $_SERVER['REQUEST_URI']."\n".$json."----------timeout-----------------\n");
				fclose($fp);
				 
				 if($arr['pushToken'] != false && $config['os']){
					send_push($arr['pushToken'], iconv('cp1251', 'utf-8', $adn), $id_order, $orderStatus);
				 }
					
				return;
				 
		}
	 
	 
		/*-Åñëè  çàêàçà óæå íåò â íîâûõ çàêàçà èùåì åãî â òàáëèöå çàêàçîâ-*/     
	 } else {
		$id_o = make_array_from_query(select_where("orders", array('old_num' => $order_id), true), true);
		$id_order = (isset($id_o['uid']))?$id_o['uid']:0;
		$orderStatus = (isset($id_o['uid']))?"ORDER_READY":"NO_ORDER";
		
		if(empty($id_o['driver_info'])){
			
			$all['orderId'] = "";
			$all['orderStatus'] = "ORDER_NO_CAR";
			$all['status'] = array(
				'code' => '4',
				'message' => 'Wrong cod'
			);
			
			$json = jdecoder(json_encode(arr_iconv($all)));
	
			echo $json;
		
			//âìåñòî /home/user/data/www/site.ru/ óêàçûâàåì ñâîé ïóòü îò êîðíÿ ñåðâåðà, êóäà äîëæåí ïèñàòüñÿ ëîã
			$fp=fopen("checkord.log","a");
			fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n". $_SERVER['REQUEST_URI']."\n".$json."---------nocar------------------\n");
			fclose($fp);
		
			return;
		}
		
	 }
	 
	 }
  
	if($id_order > 0){
		$all['orderId'] = (string) $id_order;
	} else {
		$all['orderId'] = "";
	}
   
	$all['orderStatus'] = $orderStatus;
   
	$all['status'] = array(
		'code' => (isset($id['uid']))?(($city_id !== 0)?'0':'4'):'1',
		'message' => (isset($id['uid']))?(($city_id !== 0)?'User Ok':'Wrong cod'):'No User'    
	);
	
	if(!empty($php_errormsg)){
			$all['status']['debugInfo'] = $php_errormsg; 
	}
	
	$json = jdecoder(json_encode(arr_iconv($all)));
	
	echo $json;
	
		//âìåñòî /home/user/data/www/site.ru/ óêàçûâàåì ñâîé ïóòü îò êîðíÿ ñåðâåðà, êóäà äîëæåí ïèñàòüñÿ ëîã
		$fp=fopen("checkord.log","a");
		fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n". $_SERVER['REQUEST_URI']."\n".$json."-------carrr--------------------\n");
		fclose($fp);
			
		return;
		 
?>