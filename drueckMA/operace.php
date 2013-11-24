<?php

  require "../fns_dotazy.php";
dbConnect();

switch ($_GET['akce']) {
case 'palNr': palNr($_GET['auftr']);  break;
case 'teilNr': teilNr($_GET['auftr'],$_GET['pal']);  break;
case 'tatigkeit': tatigkeit();  break;
case 'pers': persNr($_GET['pers']); break;
case 'min': minuty($_GET['teil'],$_GET['tat']); break;
case 'ausTyp': ausTyp($_GET['art']); break;
case 'timeCop': timeCop($_GET['persNr']); break;
}

function palNr($auftrNr){
$sql = "Select `AuftragsNr`, `pos-pal-nr` from Dauftr where `AuftragsNr`='$auftrNr' group by `pos-pal-nr`";

$res = mysql_query($sql) or die(mysql_error());

  if(mysql_affected_rows()>0){
    $vypis ="naplnPalety('0";
    while($zaznam = mysql_fetch_array($res)){
    $vypis = $vypis.",".$zaznam["pos-pal-nr"];
    }
    $vypis = $vypis." ')";
  }else{$vypis="document.getElementById('palnr').options.length = 0; alert('Zadané èíslo zakázky je chybné! Die Auftrags Nummer ist nicht korekt!');";}
                        echo $vypis;                        

}

function teilNr($auftr, $pal){
$sql = "select `AuftragsNr`, `pos-pal-nr`, `Teil` from Dauftr where `AuftragsNr`='$auftr' and `pos-pal-nr`=$pal group by `pos-pal-nr`";
$res = mysql_query($sql) or die(mysql_error());

  if(mysql_affected_rows()>0){
    $vypis ="naplnDily('";
    while($zaznam = mysql_fetch_array($res)){
    $vypis = $vypis.$zaznam["Teil"].",";
    }
    $vypis = $vypis."0')";
  }else{$vypis="Alert('Zadané èíslo Palety je chybné! Die Paleten Nummer ist nicht korekt!')";}
                        echo $vypis;                        

}

function tatigkeit(){
$sql = "SELECT `abg-nr` FROM `Dtaetkz-abg` WHERE `dtaetkz`='I' ORDER BY `abg-nr`";
$res = mysql_query($sql) or die(mysql_error());

  if(mysql_affected_rows()>0){
    $vypis ="naplnOperace('0";
    while($zaznam = mysql_fetch_array($res)){
    $vypis = $vypis.",".$zaznam["abg-nr"];
    }
    $vypis = $vypis." ')";
  }else{$vypis="Alert ('Zadané èíslo operace je chybné! Die Auftrags Nummer ist nicht korekt!');";}
                        echo $vypis;                        

}

function persNR($persNr){

$sql = "Select `name`, `vorname`, `schicht`, `PersNr` from Dpers where `PersNr` = $persNr";
$res = mysql_query($sql) or die(mysql_error());

if(mysql_affected_rows()>0){

    $zaznam = mysql_fetch_array($res);
    $vypis ="naplnPers('".$zaznam["name"]." ".$zaznam["vorname"]."','".$zaznam["schicht"]."')";

  }else{$vypis="Alert ('Neexistuje ¾ádný zamìstnanec s tímto èíslem! Es gibt keinen angestelten mit dieser Personal Nummer!');";}

echo $vypis; 

}

function minuty($teil, $tat){
$sql = "select `TaetNr-Aby`, `vz-min-aby` as `vzaby`,`vz-min-kunde` as `vzkd`,`TaetBez-Aby-T` as `bez` from dpos where `teil`='$teil' and `TaetNr-Aby`='$tat'";

$res = mysql_query($sql) or die(mysql_error());

if(mysql_affected_rows()>0){

    $zaznam = mysql_fetch_array($res);
    $vypis ="naplnMin('".$zaznam["vzaby"]."','".$zaznam["vzkd"]."','".$zaznam["bez"]."')";

  }else{$vypis="Alert ('Tato operace nemá ¾ádnzý údaj o èase! Diese Tätigkeit hat keine Zeit angabe!');";}

echo $vypis; 

}


function ausTyp($art){
$sql = "select `auss-art`, `auss-typ` from `auss_typen` where (`auss-art`=$art) order by `auss-typ` desc";
$res = mysql_query($sql) or die(mysql_error());
$vypis ="naplnAusTyp('";
while($zaznam = mysql_fetch_array($res)){    
    $vypis =$vypis.$zaznam["auss-typ"].",";
  }
  
  if (substr($vypis,-1)=="'"){
  $vypis = $vypis."');";
  }else{
  $vypis = substr($vypis, 0, -1);
  $vypis = $vypis."');";}

echo $vypis; 
}

function timeCop($persNr){
  $today = date("Y-n-j")." 00:00:00";
  
  $sql = "SELECT `PersNr`, `Datum`, `verb-bis` FROM drueck WHERE `PersNr`=$persNr and `Datum` = '$today' ORDER BY `verb-bis` DESC";
  $res = mysql_query($sql) or die(mysql_error());
  $zaznam = mysql_fetch_array($res);
  $vypis = "timeCopDat('".$zaznam['verb-bis']."');";
  echo $vypis;

}

                        mysql_close();
?>
