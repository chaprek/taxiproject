<?php
class weborders
{
	private $cfg;	

/* ========================================================================== */
/* ========================================================================== */

	public function __construct($url = array('http://46.151.86.228:6969/api/', 'http://159.224.156.123:6969/api/'))
	{
		$this->cfg	= $this->getConfig($url);
	}
	
/* ========================================================================== */
/* ========================================================================== */

	private function getConfig($url)
	{
	   
       $logpass = (!in_array('http://109.86.42.147:6969/api/', $url) && !in_array('http://46.151.86.228:6970/api/', $url))?'anon':'guest';
       
		$result = array
		(
			'url' => $url[0],
            'url_second' => $url[1],
			'time_out' => 30,
			'def_login' => $logpass,
			'def_pwd' => $logpass
		);
		
		return $result;
	}
	
/* ========================================================================== */
/* ========================================================================== */

	protected function getBasicAuthentication($login, $pwd)
	{				
		$SHA512Hash = hash('sha512', $pwd);

		return base64_encode($login.':'.$SHA512Hash);
	}
	
/* ========================================================================== */
/* ========================================================================== */

	protected function _request($headers, $url, $get = true)
	{				
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($get){
		  curl_setopt($ch, CURLOPT_POST, 0);		
		} else {
		  curl_setopt($ch, CURLOPT_PUT, 1);
		}
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->cfg['time_out']);
		
		$result = curl_exec($ch);
		
		//Возвращаем ложь, если запрос выполнен с ошибками
		if (curl_errno($ch) != 0) {	$result	= false; }
		
		curl_close($ch);

		return $result;
	}
	
/* ========================================================================== */
/* ========================================================================== */

	protected function _requestPost($headers, $url, $data)
	{				
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->cfg['time_out']);
		
		$result = curl_exec($ch);
		
		//Возвращаем ложь, если запрос выполнен с ошибками
		if (curl_errno($ch) != 0) {	$result	= false; }
		
		curl_close($ch);

		return $result;
	}
	
/* ========================================================================== */
/* ========================================================================== */
	public function getVersion( )
	{
		//Подготавливаем headers
		$headers = array
			(
				'Accept: application/json'
			);
		
		//Подготавливаем строку запроса
		$url = $this->cfg['url'].'version';
		
		$result = $this->_request($headers, $url);
		
		return $result;
	}
	
/* ========================================================================== */
/* ========================================================================== */

	public function searchGeoDataByPos( $lat, $lon, $radius )
	{
		//Подготавливаем headers
		$headers = array
			(
				'Accept: application/json',
				'Authorization: Basic '.$this->getBasicAuthentication($this->cfg['def_login'], $this->cfg['def_pwd'])
			);
		
		//Подготавливаем строку запроса
		$url = $this->cfg['url'].'geodata/search?lat='.$lat.'&lng='.$lon.'&r='.$radius;
		
		$result = $this->_request($headers, $url);
		
		return $result;
	}
	
/* ========================================================================== */
/* ========================================================================== */	

	public function searchGeoDataByName($search)
	{
		//Подготавливаем headers
		$headers = array
			(
				'Accept: application/json',
				'Authorization: Basic '.$this->getBasicAuthentication($this->cfg['def_login'], $this->cfg['def_pwd'])
			);
		
		//Подготавливаем строку запроса
		$url = $this->cfg['url'].'geodata/search?q='.urlencode($search);
		
		$result = $this->_request($headers, $url);
		
		return $result;
	}
    
/* ========================================================================== */
/* ========================================================================== */	

	public function checkorder($check)
	{
		//Подготавливаем headers
		$headers = array
			(
				'Accept: application/json',
				'Authorization: Basic '.$this->getBasicAuthentication($this->cfg['def_login'], $this->cfg['def_pwd'])
			);
		
		//Подготавливаем строку запроса
		$url = $this->cfg['url'].'weborders/'.$check;
		
		$result = $this->_request($headers, $url);
		
		return $result;
	}

/* ========================================================================== */
/* ========================================================================== */	

	public function cancelorder($check)
	{
		//Подготавливаем headers
		$headers = array
			(
				'Accept: application/json',
				'Authorization: Basic '.$this->getBasicAuthentication($this->cfg['def_login'], $this->cfg['def_pwd'])
			);
		
		//Подготавливаем строку запроса
		$url = $this->cfg['url'].'weborders/cancel/'.$check;
		
		$result = $this->_request($headers, $url, false);
		
		return $result;
	}
	
/* ========================================================================== */
/* ========================================================================== */

	public function orderPrice($route, $serv, $card, $phone)
	{
		//Подготавливаем headers
		$headers = array
			(
				'Accept: application/json',
                'Content-Type: application/json; charset=utf-8',
				'Authorization: Basic '.$this->getBasicAuthentication($this->cfg['def_login'], $this->cfg['def_pwd'])
			);
		
		//Подготавливаем строку запроса
		$url = $this->cfg['url'].'weborders/cost';
        $url_second = $this->cfg['url_second'].'weborders/cost';
		
        $data = array(
        	"user_full_name" => "Чумак Александр",
        	"user_phone" => $phone,
        	"client_sub_card" => $card,
        	"required_time" => null,
        	"reservation" => false,
        	"route_address_entrance_from" => null,
        	"comment" => "",
        	"add_cost" => 0,
        	"wagon" => $serv[17],
        	"baggage" => $serv[10],
        	"animal" => $serv[12],
        	"conditioner" => $serv[4],
        	"courier_delivery" => false,
        	"route_undefined" => $serv[1],
        	"route" => $route,
        	"taxiColumnId" => 0
            );
        

        $fp=fopen("post_acces.log","a");
        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---OREDERPRICE_SB_INPUT---\n". $_SERVER['REQUEST_URI']."\n\n".json_encode($data)."\n-\n-\n\n");
        fclose($fp);

		$result = $this->_requestPost($headers, $url, json_encode($data));
        
        if(!$result){
            $result = $this->_requestPost($headers, $url_second, json_encode($data));
        }
        
        $fp=fopen("post_acces.log","a");
        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---OREDERPRICE_SB_RESPONCE---\n". $_SERVER['REQUEST_URI']."\n\n".$result."\n-\n-\n\n");
        fclose($fp);
		
		return $result;
	}
	
/* ========================================================================== */
/* ========================================================================== */

	public function createOrder($route, $serv, $user)
	{
		//Подготавливаем headers
		$headers = array
			(
				'Accept: application/json',
                'Content-Type: application/json; charset=utf-8',
				'Authorization: Basic '.$this->getBasicAuthentication($this->cfg['def_login'], $this->cfg['def_pwd'])
			);
		
		//Подготавливаем строку запроса
		$url = $this->cfg['url'].'weborders';
	    $url_second = $this->cfg['url_second'].'weborders';
         
        $data = array(
        	"user_full_name" => $user['name'],
        	"user_phone" => $user['phone'],
        	"client_sub_card" => $user['card'],
        	"required_time" => $user['req_time'],
        	"reservation" => $user['reserv'],
        	"route_address_entrance_from" => $user['entrance'],
        	"comment" => "",
        	"add_cost" => $user['addcost'],
        	"wagon" => $serv[17],
        	"baggage" => $serv[10],
        	"animal" => $serv[12],
        	"conditioner" => $serv[4],
        	"courier_delivery" => false,
        	"route_undefined" => $serv[1],
        	"route" => $route,
        	"taxiColumnId" => 0
            );
            
            
        $fp=fopen("post_acces.log","a");
        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---CREATEOREDER_SB_INPUT---\n". $_SERVER['REQUEST_URI']."\n\n".json_encode($data)."\n-\n-\n\n");
        fclose($fp);
        
		$result = $this->_requestPost($headers, $url, json_encode($data));
        
        if(!$result){
            $result = $this->_requestPost($headers, $url_second, json_encode($data));
        }
        
        
        $fp=fopen("post_acces.log","a");
        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---CREATEOREDER_SB_RESPONCE---\n". $_SERVER['REQUEST_URI']."\n\n".$result."\n-\n-\n\n");
        fclose($fp);
		
		return $result;
	}
	
/* ========================================================================== */
/* ========================================================================== */

}
	//Создаем класс
	//$WebOrder = new weborders();
	//Получаем ответ на запрос
	//$cmd = $WebOrder->orderPrice();
    //$cmd = $WebOrder->createOrder();
    //$cmd = $WebOrder->checkorder('08df9a05337648d9bd703f8887dcb5b5');
    //$cmd = $WebOrder->cancelorder('08df9a05337648d9bd703f8887dcb5b5');
	//$cmd = $WebOrder->searchGeoDataByName('Васил');
	//Отображаем ответ
	//echo 'answer: '.$cmd;

?>
