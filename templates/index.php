    <!DOCTYPE HTML>
    <html>
    <head>
        <meta charset="cp1251"/>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script src="js/jquery-1.8.2.min.js"></script>
        
         <link rel="stylesheet" href="css/jquery-ui.css" />
       
        <script src="js/jquery-ui.js"></script>
        
        <script src="js/scripts.js"></script>
        
         <script>
            $(function() {
            $( "#from" ).datepicker({
            defaultDate: "+1w",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function( selectedDate ) {
            $( "#to" ).datepicker( "option", "minDate", selectedDate );
            }
            });
            $( "#to" ).datepicker({
                defaultDate: "+1w",
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function( selectedDate ) {
                $( "#from" ).datepicker( "option", "maxDate", selectedDate );
            }
            });
            });
         </script>
        
</head>
<body>

<div class="wrap">
<div class="header">
    <a class="logo" href="/"><img src="/img/logo.png" /></a>
    
    <div class="menu">
        <div class="ord <?=($user == 1)?'act':''?>" data-menu="1" data-text="<?=$show_tables[1]?>"><p>ЗАКАЗЫ</p></div>
        <div class="use <?=($user == 2)?'act':''?>" data-menu="2" data-text="<?=$show_tables[2]?>"><p>ПОЛЬЗОВАТЕЛИ</p></div>
        <div class="rat <?=($user == 3)?'act':''?>" data-menu="3" data-text="<?=$show_tables[3]?>"><p>Тарифы</p></div>
        <div class="clear"> </div>
    </div>
    <div class="clear"> </div>
    <a href="?table=<?= $user?>" class="show_table"><p><?=$show_tables[$user]?></p></a>

</div>
<div class="filtrs_line">
<div class="title"><h1>Страничка заказов</h1></div>

<div class="filtrs" id="ord"  style="display: <?=($user == 1)?'block':'none'?>;">
    <img src="img/eye.png" />
    <span class="per_day act" data-filtr="per_day">колличество заказов по дням</span>
    <span class="city_phone" data-filtr="city_phone">по городу и телефону</span>
   <!-- <span class="system" data-filtr="system">по системе</span>-->
</div>
<div class="filtrs" id="use" style="display: <?=($user == 2)?'block':'none'?>;">
    <img src="img/eye.png" />
    <span class="city_phone" data-filtr="city_phone_use">по телефону</span>
   <!-- <span class="system" data-filtr="system_use">по системе</span>-->
</div>
<div class="clear"></div>


<div class="display_filtrs" id="per_day" >
    <form action="" method="get">
    <input type="hidden" name="table" value="1" />
        <span>с</span>
        <input type="text" value="" id="from" name="min" />
        <span>по</span>
        <input type="text" value="" id="to" name="max" />
        <button>Отправить</button>
    </form>
</div>

<div class="display_filtrs" id="city_phone" >
<form action="" method="get">
<input type="hidden" name="table" value="1" />
    <select name="town">
        <option value="0">Выберите город</option>
        <?
            foreach($cities as $city){
                echo "<option value='".$city['uid']."' >".$city['name']."</option>";
            }
        ?>
    </select>
  <input name="phone" id="phone" value="xxx-xxx-xx-xx" />  
    <button>Отправить</button>
    </form>
</div>

<!--<div class="display_filtrs" id="system">
<form action="" method="get">
<input type="hidden" name="table" value="1" />
<input type="text" value="выберите систему" id="sys" name="sys" />
    <button>Отправить</button>
    </form>
</div>

<div class="display_filtrs" id="system_use">
<form action="" method="get">
<input type="hidden" name="table" value="2" />
<input type="text" value="выберите систему" id="os" name="os" />
    <button>Отправить</button>
    </form>
</div>-->
<div class="display_filtrs" id="city_phone_use">
<form action="index.php" method="get">
<input type="hidden" name="table" value="2" />
<input type="text" value="xxx-xxx-xx-xx" id="phone" name="phone_u" />
    <button>Отправить</button>
    </form>
</div>

</div>



<?
        if(isset($orders)){
            $orders_per_day = orders_per_day($orders, $_GET['min']);
        }
 ?>
<? if(isset($orders)){?>
<div class="orders_pd">
<?

$width = 0;
for($i = 0; $i < count($orders_per_day); $i++){
    ?>
    <div data-height='<?= count($orders_per_day[$i]) ?>' style="left: <?= $width += 35?>px;"><span><?= $orders_per_day[$i][0]?></span></div>
    <?
}
?>
</div>
<?}?>

<? if(isset($all_orders)){
    $al_o = (isset($all_orders[0]))?$all_orders[0]:$all_orders;
    ?>
<form action="./controllers/cms.php" method="post">
 <input type="hidden" name="table" value="orders" />


    <table>
    <tr>
    <th>Редактирование</th>
        <?
    foreach($al_o as $k=>$val){
        echo "<th>".$k."</th>";
    }
    ?>
    </tr>
    <?
    if(isset($all_orders[0])){
    for($i=0; $i < count($all_orders); $i++){
         ?>
        <tr>
        <?
        foreach($all_orders[$i] as $k=>$val){
        
        if($k == 'uid'){
            ?>
            <td class="edit">редактировать</td>
        <? } ?>
            <td data-id="<?= $k ?>"><?= $val ?></td>
        <? } ?>
        </tr>
        <?
    }
    } else {
       foreach($all_orders as $k=>$val){
            echo "<td>".$val."</td>";
        } 
    }
?>
    </table>
    <button class="send" style="display: none;">Отправить</button>
    </form>
  <? }?>



<? if(isset($all_users)){
        $al_u = (isset($all_users[0]))?$all_users[0]:$all_users;
    
    ?>
    <div class="users_table">
 <form action="./controllers/cms.php" method="post">
 <input type="hidden" name="table" value="clients" />
    <table>
    <tr>
    <th>Редактирование</th>
        <?
    foreach($al_u as $k=>$val){
        echo "<th>".$k."</th>";
    }
    ?>
    </tr>
    <?
    if((isset($all_users[0]))){
        
        for($i=0; $i < count($all_users); $i++){
             ?>
            <tr>
            <?
            foreach($all_users[$i] as $k=>$val){
                
                if($k == 'uid'){
                    ?>
                    <!-- <input type="hidden" name="uid" value="<?=$val?>" />-->
                    <td class="edit">редактировать</td>
                <? } ?>
                   
                    <td data-id="<?= $k ?>"><?= $val ?></td>
                
                <? } ?>
            </tr>
            <?
        }
        
    } else {
        foreach($al_u as $k=>$val){
                echo "<td>".$val."</td>";
            }
    }
?>
    </table>
    <button class="send" style="display: none;">Отправить</button>
    </form>
</div>
  <? }?>


<? if(isset($tariffs)){?>
    <div class="users_table">
 <form action="./controllers/cms.php" method="post">
 <input type="hidden" name="table" value="tariffs" />
    <table>
    <tr>
    <th>Редактирование</th>
        <?
    foreach($tariffs[0] as $k=>$val){
        echo "<th>".$k."</th>";
    }
    ?>
    </tr>
    <?
        for($i=0; $i < count($tariffs); $i++){
             ?>
            <tr>
            <?
            foreach($tariffs[$i] as $k=>$val){
                
                if($k == 'uid'){
                    ?>
                    <!-- <input type="hidden" name="uid" value="<?=$val?>" />-->
                    <td class="edit">редактировать</td>
                    <?
                }
                
                echo "<td data-id='".$k."'>".$val."</td>";
            }
            ?>
            </tr>
            <?
        }
        
?>
    </table>
    <button class="send" style="display: none;">Отправить</button>
    </form>
</div>
  <? }?>


 </div>



   </body>
        </html>