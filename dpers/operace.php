<?php
mysql_connect('abyserver', 'root', 'nuredv');
mysql_select_db('apl');

$sqlDpers = "select * from dpers where PersNr = '".$_GET['cislo']."'";
$resDpers = mysql_query($sqlDpers) or die(mysql_error());
$zaznam = mysql_fetch_array($resDpers);

$sqlDetail = "Select * from dpersdetail1 where PersNr = '".$_GET['cislo']."'";
$resDetail = mysql_query($sqlDetail) or die(mysql_error());
$zaznam2 = mysql_fetch_array($resDetail);

$sqlUrlaub = "SELECT * FROM DUrlaub1 WHERE PersNr='".$_GET['cislo']."'";
$resUrlaub = mysql_query($sqlUrlaub) or die(mysql_error());
$zaznam3 = mysql_fetch_array($resUrlaub);


echo "vypis('".$zaznam["Name"]."','".$zaznam["Vorname"]."','".$zaznam["Schicht"]."','".$zaznam["austritt"]."','".$zaznam["eintritt"]."','".$zaznam["ppdatum"]."','".$zaznam["komm_ort"]."','".$zaznam["premie_za_kvalitu"]."','".$zaznam["premie_za_vykon"]."','".$zaznam["premie_za_prasnost"]."','".$zaznam["premie_za_3_mesice"]."',
'".$zaznam2["lohnkoef"]."','".$zaznam2["dobaurcita"]."','".$zaznam2["zkusebni_doba_dobaurcita"]."','".$zaznam2["mzda_podle_smen"]."','".$zaznam2["k1"]."','".$zaznam2["kom1"]."','".$zaznam2["k2"]."','".$zaznam2["kom2"]."','".$zaznam2["k3"]."','".$zaznam2["kom3"]."','".$zaznam2["k4"]."','".$zaznam2["kom4"]."','".$zaznam2["k5"]."'
,'".$zaznam2["kom5"]."','".$zaznam2["k6"]."','".$zaznam2["kom6"]."','".$zaznam2["k7"]."','".$zaznam2["kom7"]."','".$zaznam2["k8"]."','".$zaznam2["k9"]."','".$zaznam2["k10"]."','".$zaznam2["kom10"]."','".$zaznam2["k11"]."','".$zaznam2["k12"]."','".$zaznam2["kom12"]."','".$zaznam2["k20"]."','".$zaznam2["kom20"]."','".$zaznam2["k21"]."'
,'".$zaznam2["kom21"]."','".$zaznam2["k22"]."','".$zaznam2["kom22"]."','".$zaznam2["k23"]."','".$zaznam2["kom23"]."','".$zaznam2["k24"]."','".$zaznam2["kom24"]."','".$zaznam3["jahranspruch"]."','".$zaznam3["rest"]."','".$zaznam3["genom"]."');";
?>
