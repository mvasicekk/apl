<?php
  require "../fns_dotazy.php";
dbConnect();

  $persnr = $_POST["PersNr"];
  $schicht = $_POST["Schicht"];
  $datum = $_POST["Datum"];
  $name = $_POST["name"];
  $vorname =  $_POST["vorname"];
  $austritt =  $_POST["austritt"];
  $eintritt =  $_POST["eintritt"];
  $ppdatum =  $_POST["probezeit"];
  $dobaurcita =  $_POST["dobaurcita"];
  $zkusebniDobaUrcita =  $_POST["zkDobaUrcita"];
  $komm_ort = $_POST["kommort"];
  
  $jahranspr =  $_POST["jahr"];
  $rest =  $_POST["rest"];
  $genomen =  $_POST["genomen"];
  
  $premieZaVykon =  $_POST["vykon"];
  $premieZaKvalitu =  $_POST["kvalita"];
  $premieZaPrasnost =  $_POST["prasnost"];
  $premieZa3Mesice =  $_POST["mesice"];
  
  $lohkoef =  $_POST["lohnkoef"];
  $podleSmen =  $_POST["podleSmen"];
  
  $k1 =  $_POST["k1"];
  $k2 =  $_POST["k2"];
  $k3 =  $_POST["k3"];
  $k4 =  $_POST["k4"];
  $k5 =  $_POST["k5"];
  $k6 =  $_POST["k6"];
  $k7 =  $_POST["k7"];
  $k8 =  $_POST["k8"];
  $k9 =  $_POST["k9"];
  $k10 = $_POST["k10"];
  $k11 = $_POST["k11"];
  $k12 = $_POST["k12"];
  
  $k20 = $_POST["k20"];
  $k21 = $_POST["k21"];
  $k22 = $_POST["k22"];
  $k23 = $_POST["k23"];
  $k24 = $_POST["k24"];  
  
  
  $kom1 =  $_POST["kom1"];
  $kom2 =  $_POST["kom2"];
  $kom3 =  $_POST["kom3"];
  $kom4 =  $_POST["kom4"];
  $kom5 =  $_POST["kom5"];
  $kom6 =  $_POST["kom6"];
  $kom7 =  $_POST["kom7"];

  $kom10 = $_POST["kom10"];

  $kom12 = $_POST["kom12"];
  
  $kom20 = $_POST["kom20"];
  $kom21 = $_POST["kom21"];
  $kom22 = $_POST["kom22"];
  $kom23 = $_POST["kom23"];
  $kom24 = $_POST["kom24"];  
  
  mysql_connect('abyserver', 'root', 'nuredv');
  mysql_select_db('apl');
  
  $sqlControl= "Select persnr from dpers where persnr = $persnr";
  $resControl = mysql_query($sqlControl) or die("Control: ". mysql_error());
  
  if(msql_affected_rows($resControl)>0){
  $sqldpers = "update dperspokusna set `Name` = '$name' , `Vorname` = '$vorname', `austritt` = '$austritt', `Schicht` = '$schicht',  `eintritt` = '$eintritt', `ppdatum` = '$ppdatum', `premie_za_vykon` = '$premieZaVykon', `premie_za_kvalitu` = '$premieZaKvalitu', `premie_za_prasnost` = '$premieZaPrasnost', `premie_za_3_mesice` = '$premieZa3Mesice', `komm_ort` = '$komm_ort' where PersNr = $persnr";
    $resdpers = mysql_query($sqldpers) or die("Dpers: " .mysql_error());
    
    $sqldpersdetail = "update dperspokusnadetail1 set `k1` = '$k1', `k2` = '$k2', `k3` = '$k3', `k4` = '$k4', `k5` = '$k5'
    , `k6` = '$k6', `k7` = '$k7', `k8` = '$k8', `k9` = '$k9', `k10` = '$k10', `k11` = '$k11', `k12` = '$k12', `k20` = '$k20', `k21` = '$k21', `k22` = '$k22', `k23` = '$k23', `k24` = '$k24'
    , `kom1` = '$kom1', `kom2` = '$kom2', `kom3` = '$kom3', `kom4` = '$kom4', `kom5` = '$kom5', `kom6` = '$kom6', `kom7` = '$kom7', `kom10` = '$kom10', `kom12` = '$kom12', `kom20` = '$kom20', `kom21` = '$kom21', `kom22` = '$kom22', `kom23` = '$kom23', `kom24` = '$kom24', `lohnkoef` = '$lohnkoef', `monatrest` = '$monatrest', `dobaurcita` = '$dobaurcita', 
    `zkusebni_doba_dobaurcita` = '$zkusebniDobaUrcita', `mzda_podle_smen` = '$podleSmen' where PersNr = $persnr";
    $resdpersdetail = mysql_query($sqldpersdetail) or die("DpersDetail: " .mysql_error());
  }else{
  $sqldpers = "insert into dperspokusna (`PersNr`, `Name`, `Vorname`, `austritt` , `Schicht`,  `eintritt`, `ppdatum`, `premie_za_vykon`, `premie_za_kvalitu`, `premie_za_prasnost`, `premie_za_3_mesice`, `komm_ort`)
  values($persnr, '$name' , '$vorname', '$austritt', '$schicht', '$eintritt', '$ppdatum', '$premieZaVykon', '$premieZaKvalitu', '$premieZaPrasnost', '$premieZa3Mesice', '$komm_ort')";
    $resdpers = mysql_query($sqldpers) or die("Dpers: " .mysql_error());
    
    $sqldpersdetail = "insert into dperspokusnadetail1 (`persnr`, `k1`, `k2`, `k3`, `k4`, `k5`, `k6`, `k7`, `k8`, `k9`, `k10`, `k11`, `k12`, `k20`, `k21`, `k22`, `k23`, `k24`, `kom1`, `kom2`, `kom3`, `kom4`, `kom5`, `kom6`, `kom7`, `kom10`, `kom12`, `kom20`, `kom21`, `kom22`, `kom23`, `kom24`, `lohnkoef`, `monatrest`, `dobaurcita`,`zkusebni_doba_dobaurcita`, `mzda_podle_smen`) values($persnr, '$k1', '$k2', '$k3', '$k4', '$k5', '$k6', '$k7', '$k8', '$k9', '$k10', '$k11', '$k12', '$k20', '$k21', '$k22', '$k23', '$k24', '$kom1', '$kom2', '$kom3', '$kom4', '$kom5', '$kom6', '$kom7', '$kom10', '$kom12', '$kom20', '$kom21', '$kom22', '$kom23', '$kom24', '$lohnkoef', '$monatrest', '$dobaurcita', 
'$zkusebniDobaUrcita', '$podleSmen')";
    $resdpersdetail = mysql_query($sqldpersdetail) or die("DpersDetail: ".mysql_error());
  
  }
  mysql_close();
?>
