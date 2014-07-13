<?php
    session_start();
    require_once ('config.php');
    require_once ('config_bd.php');
    require_once ('bd.php');
    require_once ('display.php');
    
    $arr = make_array_from_query(select_where('new_orders', array('city_id' => 2), true));
    
    if($_GET['order'] == 1){
        update_table('orders', array('driver' => 57, 'endtask' => 1, 'pretime' => date('Y-m-d H:i:m')), array('num' => $arr['num_row']), 2);
    } elseif($_GET['order'] == 2) {
        update_table('orders', array('meet' => 'Отказ', 'endtask' => 6), array('num' => $arr['num_row']), 2);
    } elseif($_GET['order'] == 3) {
        update_table('orders', array('endtask' => 5), array('num' => $arr['num_row']), 2);
    }
    
    
    
?>