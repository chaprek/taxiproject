<?php

class Calculation {

    private $host = "95.69.245.34";
    private $port = "339";

    public function tariffAction($keys, $city_id, $card, $phone = '380635753431')
    {
    
     global $config;
       
     if($city_id > 0){     
         $this->host = $config['disp_url'][$city_id]['host'];
         $this->port = $config['disp_url'][$city_id]['port'];
     }     
     
     if (!function_exists('fsockopen'))
     {
        return false;
     }
     
     $fp = fsockopen(
          $this->host, 
          $this->port, 
          $errno, 
          $errstr
     );
  
     if (!$fp)
     {   
        return false;      
     }
     
        $keys = json_decode($keys); 
     
            if($keys != null){
                $req = array();
                
                $count = count($keys);
                $routeall = ''; 
                 
                foreach($keys as $key){
                    
                    if(isset($key->key_number)){
                        if($key->key_number == 1){
                            $street = (isset($key->street))?addslashes($key->street):"";
                            $house = (isset($key->house))?$key->house:"";
                            $zone = (isset($key->zone))?$key->zone:"";
                                                        
                            $route1 = "$street\t $house\t $street $house\t -1\t 1\t $zone\t 30.12.1899\t<route>";  
                    
                        } else if($key->key_number == count($keys)) {
                            $streetto = (isset($key->street))?addslashes($key->street):"";
                            $houseto = (isset($key->house))?$key->house:"";
                            $zone = (isset($key->zone))?$key->zone:"";
                    
                            $routelast = "$streetto\t $houseto\t $streetto $houseto\t -1\t 1\t $zone\t 30.12.1899\t<route>";

                        } else {
                            $fs = (isset($key->street))?addslashes($key->street):"";
                            $fh = (isset($key->house))?$key->house:"";
                            $zone = (isset($key->zone))?$key->zone:"";
                                                        
                            $routeall .= "$fs\t $fh\t $fs $fh\t -1\t 1\t $zone\t 30.12.1899\t<route>";
                       
                        }
                    }
                }
                
                $route = $route1.$routeall.$routelast;
                
            } else {   
                return false;      
            }
                     
     $query =  'ALGORITMVERSION=2<br>'.
                  'Command=calcorder<br>'.
                  'ROXY=OPTIMA<br>';
                  
                  if(!empty($phone)){
                    $query .= 'phone='.$phone[2].$phone[3].$phone[4]."-".$phone[5].$phone[6].$phone[7]."-".$phone[8].$phone[9]."-".$phone[10].$phone[11];
                  }
                                    
        $query .= 'PreTime=08.05.2014 14:26:39<br>';
        $query .= 'DCARD='.$card.'<br>'.
                  'in_order_state=1<br>'.
                  'in_order_type='.((!empty($_POST['services']))?$_POST['services']:1).'<br>'.
                  'in_cash_type=1<br>'.
                  'in_suburb_type=0<br>'.
                  'route='.$route."<br>";  
                  
//                  echo "<pre>";
//                echo $query;
//                echo "</pre>";

$fpon=fopen("post_acces.log","a");
        fwrite($fpon,$query."\n");
        fclose($fpon);


                
    $response = '';
  
     fwrite($fp, iconv("UTF-8", "windows-1251", $query ));
     
     while (!feof($fp))
     {
        
      $response .= fread($fp, 1024);
     }
      
     fclose($fp);

        $fpon=fopen("post_acces.log","a");
        fwrite($fpon,$response."---Calculation---\n");
        fclose($fpon);

        $response = iconv( "windows-1251","UTF-8", $response);
             
        $response = explode('<br>', $response);
        
        $res = array();  
             
        for($i=0; $i<count($response); $i++)
        {
            $tmp = explode('=', $response[$i]);
            $res[$tmp[0]] = (!empty($tmp[1]))?$tmp[1]:0;    
        }
        
           
      $result = array(
        'tariff' => !empty($res['pr_outprice']) ? $res['pr_outprice'] : 0,
        'waitprice'=>$res['out_wait_price'],
        'puredriveprice'=>$res['out_pure_drive_price'],
        'distance'=>$res['out_dist_total'],
        'distcity'=>$res['out_dist_city'],
        'distsuburb'=>$res['out_dist_suburb'],
        'driveupprice'=>$res['out_driveup_price'],
        'discount'=>$res['DC_out_total_discount'],
        'error' => false
      );
      
      
      return $result;
      
    }
}




  //$keys = array(
//        array(
//          "uid" => "1231223к23к2332423432",
//          "key_number" => "1",
//          "comment" => "комент как подъехать",
//          "street" => "Митна пл.",
//          "house" => "1",
//          "entranse" => "п. 1",
//          "flat" => "",
//          "city" => array(
//            "uid" => "5",
//            "name" => "Львів"
//          ),
//          "gps" => array(
//            "lat" => "49.8392568",
//            "lng" => "24.0363097"
//          )
//        ),
//        array(
//          "uid" => "13123123213123123",
//          "comment" => "комент как подъехать",
//          "key_number" => "2",
//          "street" => "Княгині Ольги вул.",
//          "house" => "3",
//          "entranse" => "корп. 2",
//          "flat" => "",
//          "city" => array(
//            "uid" => "5",
//            "name" => "Львів"
//          ),
//          "gps" => array(
//            "lat" => "49.8185497",
//            "lng" => "24.004013"
//          )
//        )
//    );
//
//
//        $price = new Calculation();
//
//        echo json_encode($price->tariffAction(json_encode($keys), 5, ""));
//
?>