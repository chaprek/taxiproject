<?php

$client_order = array('ordertype' => '1,12,17', 'preorder' => '0', 'phone' => '063-575-34-39', 'street' => 'ул. П.Панча', 
                    'house' => '100', 'porch' => '1', 'streetto' => 'ул. Боженка', 'houseto' => '12', 'clientname' => 'Chis', 
                    'pretime' => '14.13', 'cash' => '0', 'driveupprice' => '3.00' );

$auth = array(
            'name' => 'chis',
            'phone' => '380635753431'            
        );
                          
//$data = "/sendsms?name=nenub&phone=380635753431";
//$data = "/login?name=nenub&phone=380635753431&smscode=321";
//$data = "/orders?authToken=5b21228f9f5fdf3caac20cdbdca429a";
//$data = "/historyaddresses?authToken=9c239bba359debbf42dd34535317b708";
//$data = "authToken=5b21228f9f5fdf3caac20cdbdca4e29a&name=nenu&phone=380635753431&services=10,12,17";

$json = '{
          "key_point" : [ 
         {
          "key_number" : "1",
          "street" : "улица КануСабу",
          "house" : "5",
          "entranse" : "2",
          "gps" : {
            "lat" : "50.4546600",
            "lng" : "30.5238000"
          },
          "city" : {
            "uid" : "4",
            "name" : "Одесса"
          }
        },
        {
          "key_number" : "2",
          "street" : "пер. Богдана Трезвого 12",
          "house" : "12",
          "entranse" : "3",
          "gps" : {
            "lat" : "50.0000000",
            "lng" : "36.2500000"
          },
          "city" : {
            "uid" : "2",
            "name" : "Днепропетровск"
          }
        }
        ]
        }';


//$json = '{
//          "street" : "пер. Богдана Хмельницкого 12",
//          "house" : "12",
//          "entranse" : "корп. 2",
//          "flat" : "",
//          "city" : {
//            "uid" : "2",
//            "name" : "Днепропетровск"
//          },
//          "gps" : {
//            "lat" : "50.312313123",
//            "lng" : "50.45454545"
//          }
//        }';

//$data = "key_point=".$json."&services=14,10&additioanalmoney=20&time=2013-09-19 17:14:28&authToken=ec8f49818bc575ed37933edb18fac4fd";
//$data = "key_point=".$json."&authToken=5b21228f9f5fdf3caac20cdbdca4e29a";
$data = "authToken=f1c336339b77533763645d226e437e35&services[]=10&services[]=4&key_point=".$json;

//echo $data;
$curl  =  curl_init("http://195.16.88.187/orderprice"); 
//  Устанавливаем параметры  соединения 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_POST, 1);
//$data = "distance=17.8&cond=".serialize(array(17,12))."";
//curl_setopt($curl, CURLOPT_COOKIE, "login=taximoto");

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//  Получаем  содержимое  страницы 
$content  =  curl_exec($curl);

curl_close($curl);


print_r($content); 
?>