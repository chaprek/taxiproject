<?php

    if(isset($_GET['login']) && $_GET['login'] == 'taximoto'){
        $_COOKIE['login'] = 'taximoto';
        SetCookie("login","taximoto");
    }
    
    if(!isset($_COOKIE['login']) || $_COOKIE['login'] != 'taximoto'){
        display('auth');
        return;
    }
    
              
      /*-Display CMS-*/          
                $date['user'] = (isset($_GET['table']))?$_GET['table']:1;
                
                /*-Текст для кнопки-*/
                $date['show_tables'] = array(
                    '1' => 'ПОКАЗАТЬ ТАБЛИЦУ ЗАКАЗОВ',
                    '2' => 'ПОКАЗАТЬ СПИСОК КЛИЕНТОВ',
                    '3' => 'ПОКАЗАТЬ ТАБЛИЦУ ТАРИФОВ'
                );
                
                
                $date['cities'] = make_array_from_query(select_where('cities', array('lang' => 'ru'), true), true);
                                
                if(isset($_GET['table'])){
                    
                    if($_GET['table'] == 1){ // orders
                        
                        if(isset($_GET['max'])){ // by date
                            $date['orders'] = make_array_from_query(select_orders($_GET['min'], $_GET['max']), true);
                        } elseif(isset($_GET['town'])) { // by town and phone
                            
                            $ar = array();
                            
                            if($_GET['town'] !=0){
                                $ar['town'] = $_GET['town'];
                            } 
                            
                            if($_GET['phone'] !='xxx-xxx-xx-xx') {
                                $ar['phone'] = $_GET['phone'];
                            }
                            
                            $date['all_orders'] = make_array_from_query(select_where("orders", $ar, true), true);
                            
                        } else { // default
                            $date['all_orders'] = make_array_from_query(select_all("orders", true), true);
                        }
                        
                    } elseif($_GET['table'] == 2){ // users
                        
                        $phone_u = (isset($_GET['phone_u']) &&  $_GET['phone_u'] !='xxx-xxx-xx-xx')?$_GET['phone_u']:false;
                            
                        $date['all_users'] = make_array_from_query(select_all_clients($_GET['phone_u']), true);                            
                        
                    } elseif($_GET['table'] == 3){ // rates
                        
                        $date['tariffs'] = make_array_from_query(select_all("tariffs", true), true);
                        
                    }
                    
                } else { // default
                    $date['all_orders'] = make_array_from_query(select_all("orders", true), true);
                }
                                
            
            display('index', $date);
?>