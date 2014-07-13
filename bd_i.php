<?php

function select_base_i($db){
     
    if(is_numeric($db)){
        $db = "db".$db;
        global $$db;
        return $$db;
    } else {
        return false;
    }
}

function insert_in_line_i($table, $arr, $ret = false, $db = false){
    
    $dbh = select_base($db);
    if(!$dbh) return false;    
    
    $query ='INSERT INTO "'.$table.'"';
                    
    $cols = " (";
    $vals = " VALUES(";
    
    $i = 0;
    
    foreach($arr as $k=>$val){
        
        $cols .= ' "'.$k.'"'.(($i < count($arr) - 1)?",":"");
        $vals .= " '".$val."'".(($i < count($arr) - 1)?",":"");
        
        $i++;
    }
    
    $cols .= ") ";
    $vals .= ")";
    
    $return = ($ret)?' RETURNING "Order_No"':'';
        
    $query .= $cols.$vals.$return; 
        
        if($table == 'RunOrders'){
        $fp = fopen('counter.txt', 'w');
        $test = fwrite($fp, $query);    
        fclose($fp);
        }
 
    //echo $query;
    $no = ibase_query($dbh, $query) or die("Invalid query: "); 
    if($ret){
        $row = ibase_fetch_assoc($no);
        return $row['Order_No'];   
    } else {
        return;
    }


}

function select_where_i($table, $cond, $db = false){
        
    $dbh = select_base($db);
    if(!$dbh) return false;  
        
    $query = 'SELECT * FROM "'.$table.'" WHERE ';
      
    $i = 0;
    foreach($cond as $k=>$val){
        $query .= '"'.$k.'" = \''. $val.(($i < count($cond) - 1)?'\' AND ':'\'');
        $i++;
    }
                           
    return ibase_query($dbh, $query);
}


function update_table_i($table, $cond, $where, $db = false){
    
    $dbh = select_base($db);
    if(!$dbh) return false; 
    
    $query = 'UPDATE "'.$table.'" SET ';
    
    foreach($cond as $k=>$c){ 
        $query .= '"'.$k.'" = \''.$c.'\', ';
    }
    
    $query = substr($query, 0, -2);    
        
    foreach($where as $t=>$w){ 
        $query .= ' WHERE "'.$t.'" = \''.$w.'\'';
    }
    
    //echo $query;
    
    return ibase_query($dbh, $query);
}

function make_array_from_query_i($query, $decod = false){
    
    if($query){
        
        $array = array();
        $i = 0;
        while($result = ibase_fetch_assoc($query)){
                       
            foreach($result as $k=>$col){
                if($decod){
                    $array[$i][$k] = $col;
                } else {
                    $array[$i][$k] = iconv('cp1251', 'utf-8', $col);
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

function select_driver_i($num, $table = 'RunOrders', $db = false){
        
    $dbh = select_base($db);
    if(!$dbh) return false;  
        
    $query = 'SELECT "Driver_No", "Creation_Time", "Signal", "Address", "Phone", "Req_Start_Time", "Cost", "Universal", "Dest", "Animal", "ClientName", "Condition", "Baggage", "IsViaCity", "Close_Reason" FROM "'.$table.'" WHERE "OrderUID" = \''.$num.'\' ';
                   
    return ibase_query($dbh, $query);
}

function select_driver_info_i($num, $sign, $db = false){
    
    $dbh = select_base($db);
    if(!$dbh) return false; 
        
    $query = 'SELECT "Car_No", "Marka", "Color", "MPhone" 
                FROM "Cars", "Drivers" WHERE "Driver_No" = \''.$num.'\' AND "Signal" = \''.$sign.'\'';
            
    return ibase_query($dbh, $query);
}

/*-SELECT CARD-*/
function find_card_i($num, $num2, $db = false){
        
    $dbh = select_base($db);
    if(!$dbh) return false;  
        
    $query = 'SELECT * FROM "Clients" WHERE "NickName" = \''.$num.'\' OR "NickName" = \''.$num.'\' ';
                           
    return ibase_query($dbh, $query);
}

function check_card_rule($db = false){
        
    $dbh = select_base($db);
    if(!$dbh) return false;  
        
    $query = 'SELECT * FROM "Settings" WHERE "ParamName" = \'FlexibleDiscounts\' ';
                           
    return ibase_query($dbh, $query);
}

?>