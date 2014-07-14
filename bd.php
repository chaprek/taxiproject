<?php

function select_base($db){
     global $first_query;
              
    if($db === true){
        global $dbl;
        return $dbl;
    } elseif(is_numeric($db)){
        if($first_query){
        //include_once("base/db".$db.".php");
        }
        $db = "db".$db;
        global $$db;
        return $$db;
    } else {
        return false;
    }
}

function insert_in_line($table, $arr, $db = false){
    
    $db = select_base($db);
    if(!$db) return false;
         
    $query = "INSERT INTO `".$table."` SET ";
    
    foreach($arr as $k=>$val){
        if($val == 'NOW()'){
            $query .= $k." = ".$val.", ";
        } else {
            $query .= $k." = '".$val."', ";
        }
        
        
    }
    
    $query = substr($query, 0, -2);
    mysql_query($query, $db) or die("Invalid query: " . mysql_error());//insert order  
    
    $fp=fopen("post_acces.log","a");
    fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n------QUERY------\n". $_SERVER['REQUEST_URI']."\n".$query."");
    fclose($fp);
        
    return mysql_insert_id($db);   
}
function delete_row($table, $cond, $db = false){
    
    $db = select_base($db);
    if(!$db) return false;
        
    $query = "DELETE FROM `".$table."` WHERE ";
    
    $i = 0;
    foreach($cond as $k=>$val){
        $query .= $k." = '". $val.(($i < count($cond) - 1)?"' AND ":"'");
        $i++;
    }
    
    return mysql_query($query, $db);
}
function select_all($table, $db = false){
    
    $db = select_base($db);
    if(!$db) return false;
    
    $ok = mysql_query("SELECT * FROM `$table` ", $db);
    
    $ret = ($ok)?$ok:false;
    
   return $ret;
}
function select_all_clients($phone = false){
    
    global $dbl;    
    
    //$query = "SELECT clients.*, COUNT(orders.client_id) FROM `clients`, `orders` WHERE orders.client_id = clients.id";
    
    $query = "SELECT clients.*, COUNT(orders.client_id) as count FROM clients left join orders on orders.client_id = clients.uid ";
    
    
    if($phone){
        $query .= " WHERE clients.phone = '$phone'";
    }
    $query .= " group by clients.uid";
    
    return mysql_query($query, $dbl);
}



function select_where($table, $cond, $db = false){
    
    $db = select_base($db);
    if(!$db) return false;
    
    $query = "SELECT * FROM `".$table."` WHERE ";
    
    $i = 0;
    foreach($cond as $k=>$val){
        $query .= $k." = '". $val.(($i < count($cond) - 1)?"' AND ":"'");
        $i++;
    }
    
    //echo $query."<br/>";    
   return mysql_query($query, $db);
}
/*-Список улиц по буквам-*/
function select_streets($cond, $db = false){
            
     $db = select_base($db);
     if(!$db) return false;
            
     //$query = "select name, '0' as type from refstreets where name like '%".iconv('utf-8', 'cp1251', $cond)."%' union all select name, '1' as type from refplaces where name like '%".iconv('utf-8', 'cp1251', $cond)."%' order by name limit 30";
     $query = "select name, '0' as type from refstreets where name like '%".iconv('utf-8', 'windows-1251', $cond)."%' and name is not null group by name order by name limit 30";
     //echo $query."<br/>";    
     return mysql_query($query, $db);
}

function select_hadress($table, $cond, $count = false, $since = false, $max = false ){
    
    global $dbl;
    
    $order = " ORDER BY uid DESC";  
                
    if($count){
        $limit = " LIMIT ".$count;
     }        
    
    if($since){
        $since = "AND `uid` > ".$since;        
    } else {
        $since = "";
    }
           
    if($max && !$since){                  
        $max = "AND `uid` < ".$max;      
    } else {
        $max = "";
    }
    
    $query = "SELECT * FROM `".$table."` WHERE ";
    
    $i = 0;
    foreach($cond as $k=>$val){
        $query .= " `".$k."` = '". $val.(($i < count($cond) - 1)?"' AND ":"'");
        $i++;
    }
    
    $query .=  " GROUP BY street ".$since.$max.$order.$limit;
    
   return mysql_query($query, $dbl);
}

function update_table($table, $cond, $where, $db = false){
    
    $db = select_base($db);
    if(!$db) return false;
    
    $query = "UPDATE `".$table."` SET ";
    
    foreach($cond as $k=>$c){ 
        $query .= $k."='".$c."', ";
    }
    
    $query = substr($query, 0, -2);    
      
    $query .= " WHERE ";  
    //    
//    foreach($where as $t=>$w){ 
//        $query .= $t." = '".$w."'";
//    }
    
    $i = 0;
    foreach($where as $k=>$val){
        $query .= $k." = '". $val.(($i < count($where) - 1)?"' AND ":"'");
        $i++;
    }
    
    
    
    //echo $query;
    
    mysql_query($query, $db);
    $tt = mysql_affected_rows($db);
        
    return $tt;
}

function make_array_from_query($query, $decod = false){
    
    if($query){
        
        $array = array();
        $i = 0;
        while($result = mysql_fetch_assoc($query)){
                         
            foreach($result as $k=>$col){
                if($decod){
                    $array[$i][$k] = $col;
                } else {
                    if(!mb_detect_encoding($col, 'UTF-8', true)){
                        $array[$i][$k] = iconv('cp1251', 'UTF-8', trim($col));
                    } else {
                        $array[$i][$k] = trim($col);
                    }
                }
                //$array[$i][$k] = ($decod)?iconv('cp1251', 'utf-8', $col):$col;
            } 
            $i++; 
        }
        
        $array = (count($array) == 1)?$array[0]:$array;
    } else {
        $array = false;    
    }
    
    return $array;
}

/*-CLIENT_SERVER_REQUEST-*/

function select_user_orders($id, $count = false, $since = false, $max = false){
    global $dbl;
    
    $order = " ORDER BY uid DESC";  
                
    if($count){
        $limit = " LIMIT ".$count;
     }        
    
    if($since){
        $since = "AND `uid` > ".$since;        
    } else {
        $since = "";
    }
           
    if($max && !$since){                  
        $max = "AND `uid` < ".$max;      
    } else {
        $max = "";
    }
                                                                                                                          
    $query = "SELECT uid, date, price, meet, pretime, key_points, ordertype, driver_info FROM `orders` WHERE `client_id` = ".$id." ".$since.$max.$order.$limit;

    return mysql_query($query, $dbl);
}

function select_user_order($id, $orderId){
    global $dbl;
                                                                                                                        
    $query = "SELECT uid, date, price, meet, town, pretime, key_points, ordertype, driver_info FROM `orders` WHERE `client_id` = ".$id." AND `uid`=".$orderId;

    return mysql_query($query, $dbl);
}

function select_key_points($keys){
    global $dbl;
    
    $query = "SELECT * FROM `kei_points` WHERE ";
    
    $keys = explode(',', $keys);
    
    $i=1;
    
    foreach($keys as $key){
       $query .= "`uid` = ".$key." ".(($i < count($keys))?"OR ":"");
        $i++;
    }

    return mysql_query($query, $dbl);
}

function select_services($keys){
    global $dbl;
    
    $query = "SELECT uid, name, type FROM `services` WHERE ";
    
    $keys = explode(',', $keys);
        
    if(is_array($keys)){
        $i=1;
        foreach($keys as $key){
           $query .= "`uid` = ".$key." ".(($i < count($keys))?"OR ":"");
            $i++;
        }
    } else {
        $query .= "`uid` = ".$key." ";
    }
    
    
    return mysql_query($query, $dbl);
}

function select_driver($num, $table = 'orders', $db = false){
    
    $db = select_base($db);
    if(!$db) return false;    
        
    $num_name = ($table == 'orders')?"num":"oldnum";
    
    $query = "SELECT driver, pretime, endtask FROM `".$table."` WHERE ".$num_name." = '".$num."' ";
    
    return mysql_query($query, $db);
}

function select_order_info($num, $table = 'orderscomplete', $db = false){
    
    $db = select_base($db);
    if(!$db) return false; 
        
    $num_name = ($table == 'orders')?"num":"oldnum";    
        
    $query = "SELECT town, street, house, paysum, streetto, ordertime, houseto, route, ordertype, driver, meet, client, phone, endtask
    FROM `".$table."` WHERE ".$num_name." = ".$num." ";
                        
    return mysql_query($query, $db);
}

function select_driver_info($num, $db = false){
    
    $db = select_base($db);
    if(!$db) return false; 
        
    $query = "SELECT refcars.carnumber, refcars.model, refcars.color, refdrivers.pager 
    FROM `refcars`, `refdrivers` WHERE refdrivers.num = ".$num." AND refdrivers.car = refcars.carnumber";
            
    return mysql_query($query, $db);
}
/*-
function select_orders($min, $max, $db = false){    
    
    $db = select_base($db);
    if(!$db) return false; 
    
    $query = "SELECT completetime FROM `orderscomplete` WHERE 
    (completetime > '$min 00:00:00') AND (completetime < '$max 23:59:59') ";
        
   return mysql_query($query, $db);
}-*/

function select_orders($min, $max){    
    
    global $dbl;
    
    $query = "SELECT * FROM `orders` WHERE date > '".$min." 00:00:00' AND (date < '".$max." 23:59:59') ";
        
   return mysql_query($query, $dbl);
}

function select_phone_by_id($id){    
    
    global $dbl;
    
    $query = "SELECT phone FROM `clients` WHERE id = $id ";
        
   return mysql_query($query, $dbl);
}

/*-CHECK CARDNUMBER-*/
function find_card($num, $db = false){
    
    $db = select_base($db);
    if(!$db) return false; 
        
    $query = "SELECT * FROM `dcards` WHERE dcards.num = ".$num." OR dcards.pwd = ".$num."";
            
    return mysql_query($query, $db);
}

function find_trip_count($phone1, $phone2, $db = false){
    $db = select_base($db);
    if(!$db) return false; 
        
    $query = "SELECT usecount FROM `refclients` WHERE phone = '".$phone1."' OR phone = '".$phone2."'";
                      
    return mysql_query($query, $db);
    
}

//function check_card_type($num, $db = false){
//    
//    $db = select_base($db);
//    if(!$db) return false; 
//        
//    $query = "SELECT * FROM `discounts` WHERE discounts.num = ".$num."";
//            
//    return mysql_query($query, $db);
//}
//
//function check_card_rule($num, $db = false){
//    
//    $db = select_base($db);
//    if(!$db) return false; 
//        
//    $query = "SELECT * FROM `discount_det` WHERE discount_det.discountid = ".$num."";
//            
//    return mysql_query($query, $db);
//}
?>