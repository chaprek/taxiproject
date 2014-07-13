    <!DOCTYPE HTML>
    <html>
    <head>
        <meta charset="cp1251"/>
        <script src="js/jquery-1.8.2.min.js"></script>
        <script src="js/scripts.js"></script>
        
        
        <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>

<div class="wrap">
<a href="/" class="back">Назад</a>
<h1>Страничка пользователей</h1>

<div class="block_filtr">
<form action="" method="get">
<input type="hidden" name="menu" value="users" />
    <input name="phone" id="phone" value="xxx-xxx-xx-xx" />   
    <input type="submit" value="Отправить" />
</form>
</div>
<a href="?menu=users&table=1" class="show_table">Показать список клиентов</a>


<div class="users_table">
<? if(isset($all_users)){
        $al_u = (isset($all_users[0]))?$all_users[0]:$all_users;
    
    ?>
   
   
    <table>
    <tr>
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
                echo "<td>".$val."</td>";
            }
            ?>
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
  <? }?>

</div>


 </div>

<?php


?>

   </body>
        </html>