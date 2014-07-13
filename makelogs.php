<?php

/*-логи POST запросов-*/
if(isset($_POST) && count($_POST)>0){ 
        $data="";
        foreach($_POST as $key=>$val){
                if(is_string($val) && strlen($val)>2000 )
                        $val=substr($val,0,2000);
                $data.=$key."=>".$val."\n";
        }
        //вместо /home/user/data/www/site.ru/ указываем свой путь от корня сервера, куда должен писаться лог
        $fp=fopen("post_acces.log","a");
        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n---POST_LOGS---\n". $_SERVER['REQUEST_URI']."\n".$lang."\n".$data."\n-\n-\n");
        fclose($fp);
        $data="";
        reset($_POST);
}

/*-логи HEADER запросов-*/
/*-
if(isset($_SERVER) && count($_SERVER)>0){ 
        $data="";
        foreach($_SERVER as $key=>$val){
                if(is_string($val) && strlen($val)>2000 )
                        $val=substr($val,0,2000);
                $data.=$key."=>".$val."\n";
        }
        //вместо /home/user/data/www/site.ru/ указываем свой путь от корня сервера, куда должен писаться лог
        $fp=fopen("header_acces.log","a");
        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n". $_SERVER['REQUEST_URI']."\n".$lang."\n".$data."---------------------------\n");
        //fclose($fp);
        $data="";
}-*/


/*-логи key_points -*/

//if(isset($_GET['key_point']) || isset($_POST['key_point'])){ 
//    
//        $keys = (isset($_GET['key_point']))?$_GET['key_point']:$_POST['key_point'];
//        
//        $fp=fopen("keys_logs.log","a");
//        fwrite($fp,date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."\n". $_SERVER['REQUEST_URI']."\n".$keys."\n"."---------------------------\n");
//        //fclose($fp);
//        $data="";
//}

?>