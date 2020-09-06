<?php

require_once('dbutils.php');

if(isset($_POST['date'])){
	$date = $_POST['date'];
}
else{
	$date = strtotime("last monday", strtotime("tomorrow"));
}

if(isset($_POST['day'])){
	$day = $_POST['day'];
}
else{
	$day = 0;
}


function printcat(string $daydate){
    $result = easyQuery('SELECT content, link, category FROM zadania WHERE date=?', 's', $daydate);

    while($row=$result->fetch_assoc()){
        echo '<a target="blank" href="'.$row['link'].'">• '.$row['category'].' - ' .$row['content'].'</a>';
    }
}

function countcat(string $daydate){
    $result = easyQuery('SELECT COUNT(id) AS count FROM zadania WHERE date=?', 's', $daydate);
    return $result->fetch_assoc()['count'];
}

?>

<script> var date = <?php echo $date; ?>;  var day = <?php echo $day; ?> </script>
<div class="navPanel"><div class="buttonBar">
  <div class="homeworkButton small" onclick="location.href='dodajZadanieDomowe.php'">+</div>
  <div class="homeworkButton small" id="info" style="font-family: serif;">i</div>
  <div class="homeworkButton big">
    <span id="previous">⮜</span>
    <span><?php echo '<script> console.log("wczytałem dla daty = '.date("d.m", $date).'");</script>'; echo date("d.m", $date); $date = strtotime("+4 days", $date); echo " / "; echo date("d.m", $date); ?> </span>
    <span id="next">⮞</span></div>
</div></div>

<div class="titlePanel">Zadania Domowe</div>
<div class="homeworkRow">
  <div class="dayPanel blue" id="4"><div class="day">Poniedziałek</div><div class="number"><div class="numberBackground"><?php echo countcat(date("Y-m-d", strtotime("-4 days", $date))); ?></div></div></div>
  <div class="dayPanel green" id="3"><div class="day">Wtorek</div><div class="number"><div class="numberBackground"><?php echo countcat(date("Y-m-d", strtotime("-3 days", $date))); ?></div></div></div>
</div>
<div class="homeworkRow">
  <div class="dayPanel red" id="2"><div class="day">Środa</div><div class="number"><div class="numberBackground"><?php echo countcat(date("Y-m-d", strtotime("-2 days", $date))); ?></div></div></div>
  <div class="dayPanel yellow" id="1"><div class="day">Czwartek</div><div class="number"><div class="numberBackground"><?php echo countcat(date("Y-m-d", strtotime("-1 days", $date))); ?></div></div></div>
</div>
<div class="homeworkRow">
  <div class="dayPanel jadeite" id="0"><div class="day">Piątek</div><div class="number"><div class="numberBackground"><?php echo countcat(date("Y-m-d", strtotime("-0 days", $date))); ?></div></div></div>
</div>
<div id="dayPopup" class="green"><div class="dayPopupBackground"><div id="homeworkTitle"></div><div id="homeworkContent"><?php echo printcat(date("Y-m-d", strtotime("-".$day." days", $date))); ?></div><div id="close">Zamknij mnie</div></div></div>
</div>

<script> onLoad(); </script>
