<?php
    session_start();
    require_once ('../config.php');
    require_once ('../config_bd.php');
    require_once ('../bd.php');
    require_once ('../display.php');
    
    //print_r($_POST);
    
    $uid = $_POST['uid'];
    
    $cond = $_POST;
    
    unset($cond['uid']);
    unset($cond['count']);
    unset($cond['table']);
    
    //print_r($cond);

    update_table($_POST['table'], $cond, array('uid' => $uid), true);
    
    header('Location: '.$_SERVER['HTTP_REFERER']);

    echo $php_errormsg;

?>