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
<a href="/" class="back">Назад</a>
<h1>Страничка заказов</h1>

<h2>Колличество заказов по дням</h2>


<div class="block_filtr">
<form action="" method="get">
<input type="hidden" name="menu" value="orders" />

<input type="text" id="from" name="min" />
<input type="text" id="to" name="max" />
    <input type="submit" value="Отправить" />
</form>
</div>


<a href="?menu=orders&table=1" class="show_table">Показать таблицу заказов</a>



<h2>Фильтр по городу и телефону</h2>
<div class="block_filtr">
<form action="" method="get">
<input type="hidden" name="menu" value="orders" />
    <select name="town">
        <option value="0">Выберите город</option>
        <?
        foreach($citys as $id=>$city){
            echo "<option value='".$id."' >".$city."</option>";
        }
        
        ?>
    </select>
  <input name="phone" id="phone" value="xxx-xxx-xx-xx" />   
    <input type="submit" value="Отправить" />
</form>
</div>


<?
        if(isset($orders)){
            $orders_per_day = orders_per_day($orders, $_GET['min']);
        }
 ?>
<? if(isset($orders)){?>
<div class="orders">
<?
$width = 0;
for($i = 0; $i < count($orders_per_day); $i++){
    ?>
    <div data-height='<?= count($orders_per_day[$i]) ?>' style="left: <?= $width += 35?>px;"></div>
    <?
}
?>
</div>
<?}?>

<? if(isset($all_orders)){
    $al_o = (isset($all_orders[0]))?$all_orders[0]:$all_orders;
    ?>

    <table>
    <tr>
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
            echo "<td>".$val."</td>";
        }
        ?>
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
  <? }?>
 </div>

<?php


//echo "<pre>";
//print_r($orders);
//echo "</pre>";


?>

   </body>
        </html>