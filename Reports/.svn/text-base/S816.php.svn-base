<?php

include '../fns_dotazy.php';

if(!$_GET['auftrVon']){$auftragVon='';}
if(!$_GET['auftrBis']){$auftragBis='';}
if(!$_GET['teil']){$teil='';}

$auftragVon = $_GET['auftrVon'];
$auftragBis = $_GET['auftrBis'];
$teil       = $_GET['teil'];
if($teil=='*'){$teil='%';}
if($auftragVon=='' || $auftragBis=='' || $teil==''){
echo "
<form action='S816.php' method='get'>
<label for='auftrVon' style='width:120px;'>Auftrag Von</label>
<input type='text' size='7' name='auftrVon' id='auftrVon' value='' /><br />
<label for='auftrBis' style='width:120px;'>Auftrag Bis</label>
<input type='text' size='7' name='auftrBis' id='auftrBis' value='' /><br />
<label for='teil' style='width:120px;'>Teil</label>
<input type='text' size='7' name='teil' id='teil' value='' /><br />
<input type='submit' name='OK' value='OK' />
</form>
";
}else{
dbConnect();
// Vytvoøí pohled na tabulku Dauftr

$query = "
create view hotovo_S816
as
SELECT `DRUECK`.`AuftragsNr`, `DRUECK`.`Teil`, `DRUECK`.`pos-pal-nr`, Sum(`DRUECK`.`Stück`) AS gutstk
FROM `DAUFTR` INNER JOIN `DRUECK` ON (`DAUFTR`.`pos-pal-nr` = `DRUECK`.`pos-pal-nr`) AND (`DAUFTR`.`abgnr` = `DRUECK`.`TaetNr`) AND (`DAUFTR`.`Teil` = `DRUECK`.`Teil`) AND (`DAUFTR`.`AuftragsNr` = `DRUECK`.`AuftragsNr`)
WHERE (((`DAUFTR`.`KzGut`)='G') AND ((`DAUFTR`.`AuftragsNr`) Between '$auftragVon' And '$auftragBis') AND ((`DAUFTR`.`auftragsnr-exp`) Is Null) AND ((`DAUFTR`.`Teil`) Like '$teil'))
GROUP BY `DRUECK`.`AuftragsNr`, `DRUECK`.`Teil`, `DRUECK`.`pos-pal-nr`
HAVING (((`DRUECK`.`AuftragsNr`) Between '$auftragVon' And '$auftragBis') AND ((`DRUECK`.`Teil`) Like '$teil'))
ORDER BY `DRUECK`.`AuftragsNr`, `DRUECK`.`Teil`, `DRUECK`.`pos-pal-nr`;
";
mysql_query($query) or die("hotovo-> ".mysql_error());

// Vytvoøí pohled na tabulku Drueck
$query = "
create view hotovo_aus_S816
as
SELECT `DRUECK`.`AuftragsNr`, `DRUECK`.`Teil`, `DRUECK`.`pos-pal-nr`, Sum(`DRUECK`.`Auss-Stück`) AS `auss` FROM `DRUECK` INNER JOIN `DAUFTR` ON (`DRUECK`.`pos-pal-nr` = `DAUFTR`.`pos-pal-nr`) AND (`DRUECK`.`TaetNr` = `DAUFTR`.`abgnr`) AND (`DRUECK`.`Teil` = `DAUFTR`.`Teil`) AND (`DRUECK`.`AuftragsNr` = `DAUFTR`.`AuftragsNr`) WHERE (((`DAUFTR`.`auftragsnr-exp`) Is Null) AND ((`DAUFTR`.`AuftragsNr`) Between $auftragVon And $auftragBis) AND ((`DAUFTR`.`Teil`) Like '$teil')) GROUP BY `DRUECK`.`AuftragsNr`, `DRUECK`.`Teil`, `DRUECK`.`pos-pal-nr` HAVING (((`DRUECK`.`AuftragsNr`) Between $auftragVon And $auftragBis) AND ((`DRUECK`.`Teil`) Like '$teil'));
";
mysql_query($query) or die("hotovo_aus-> ".mysql_error()."<br />".$query);

// Vytvoøí pohled na tabulku `DRUECK`
$query = "
create view hotovo_sum_S816
as
SELECT `hotovo_S816`.`AuftragsNr`, `hotovo_S816`.`Teil`, `hotovo_S816`.`pos-pal-nr`, `hotovo_S816`.`gutstk`, `hotovo_aus_S816`.`auss`
FROM `hotovo_S816` INNER JOIN `hotovo_aus_S816` ON (`hotovo_S816`.`pos-pal-nr` = `hotovo_aus_S816`.`pos-pal-nr`) AND (`hotovo_S816`.`Teil` = `hotovo_aus_S816`.`Teil`) AND (`hotovo_S816`.`AuftragsNr` = `hotovo_aus_S816`.`AuftragsNr`);
";
mysql_query($query) or die("hotovo_sum-> ".mysql_error());

// Vykoná dotaz na uvedené pohledy


$query = "
SELECT `DAUFTR`.`AuftragsNr`, `DAUFTR`.`Teil`, `DAUFTR`.`Stück` as `stueck`, `DAUFTR`.`pos-pal-nr`, `DKOPF`.`Gew`, `gew`*`stück` AS `vahacelkem`, `DAUFTR`.`auftragsnr-exp` as `aufexp`, `hotovo_sum_S816`.`gutstk`, `hotovo_sum_S816`.`auss`, `DAUFTR`.`KzGut`, `DAufKopf`.`Aufdat`, `DAUFTR`.`Termin`
FROM `DAufKopf` INNER JOIN ((`DKOPF` RIGHT JOIN `DAUFTR` ON `DKOPF`.`Teil` = `DAUFTR`.`Teil`) LEFT JOIN `hotovo_sum_S816` ON (`DAUFTR`.`AuftragsNr` = `hotovo_sum_S816`.`AuftragsNr`) AND (`DAUFTR`.`Teil` = `hotovo_sum_S816`.`Teil`) AND (`DAUFTR`.`pos-pal-nr` = `hotovo_sum_S816`.`pos-pal-nr`)) ON `DAufKopf`.`AuftragsNr` = `DAUFTR`.`AuftragsNr`
WHERE (((`DAUFTR`.`AuftragsNr`) Between $auftragVon And $auftragBis) AND ((`DAUFTR`.`Teil`) Like '$teil') AND ((`DAUFTR`.`pos-pal-nr`)>0) AND ((`DAUFTR`.`auftragsnr-exp`) Is Null) AND ((`DAUFTR`.`KzGut`)='G'))
ORDER BY `DAUFTR`.`AuftragsNr`, `DAUFTR`.`Teil`, `DAUFTR`.`pos-pal-nr`;
";

$dbresult = mysql_query($query) or die("finale-> ".mysql_error()."<br />".$query);

$i=0;
$u=1;
$pagCon =50;
$rows = mysql_affected_rows();

if($rows>=50){
$sumPag = (mysql_affected_rows()/50)+1;
}else{
$sumPag = 1;
}
  echo "
  <html>
  <head>
    <title>S816  T_nicht ausgeliefert</title>
    <style media='print, screen' type='text/css'>
    body{width:210mm;}
      table{width:210mm; font-size:3mm;}
      td{text-align:right; line-height:3mm;}
      th{border-top:3px double black; border-bottom:3px double black;}
      strong{font-style: italic;}
      .upper{font: normal normal bold 5mm 'Times New Roman';}
      .summ td{font-weight: bold;}
      .sumSmall td{font-weight:bold; line-height:5mm; vertical-align:top;}
      .cistic{position:absolute; top:280mm; clear:both; width:210mm; height:1mm; background-color:rgb(137,137,137); border:none; margin-left:0;}
    </style>
  </head>
  <body>
  <table style='border-top: 2mm solid rgb(137,137,137); border-bottom: 2mm solid rgb(137,137,137);'>
    <tr>
      <td style='vertical-align:middle; text-align:left; line-height:8mm;'><h1>S816  T_nicht ausgeliefert</h1></td>
      <td style='vertical-align:bottom; text-align:right;'><br />
          <strong>Auftrag Von:</strong> $auftragVon<br /> 
          <strong>Bis:</strong> $auftragBis</td>
    </tr>
  </table>

  ";
  
$altAuftrNr=0;
$altTeil =0;
$sumGew=0;
$sumStk = 0; 
$sumGut = 0; 
$sumAuss = 0;

    $absSummGew = 0;
    $absSumStk = 0;
    $absSumGut = 0;
    $absSumAuss = 0;
    

$i=0;

While($S816=mysql_fetch_assoc($dbresult)){
$auftrNr = $S816['AuftragsNr'];
$datum = split(" ", $S816['Aufdat']);
$teil = $S816['Teil'];
  if($altAuftrNr != $auftrNr and $i!=0){
    echo "
    <tr>
      <td></td>
      <td colspan='8' style='line-height:1mm;'><hr style='background-color:blue; border:none; height:1px; margin:0px;' /></td>
      </tr>
      <tr class='sumSmall'>
        <td></td>
        <td style='text-align:center; font-size:3mm; color:blue;'>Summe<br /></td>
        <td>".round($sumStk) ."</td>
        <td>".round($sumGut) ."</td>
        <td>".round($sumAuss) ."</td>
        <td colspan='4'>&nbsp;</td>
      </tr>
      <tr>
            <td colspan='9'><hr style='border-top:1px dotted black; border-bottom:none; border-left:none; border-bottom:right;' /></td>
      </tr>
      <tr class='summ'>
      <td style='text-align:center; font-size:4mm;'>Summe</td>
        <td colspan='6'>&nbsp;</td>
        <td>".round($sumGew) ."</td><td>&nbsp;</td>
      </tr>
    </table>";
    $absSummGew = $absSummGew + $sumGew;
    $absSumStk = $absSumStk + $sumStk;
    $absSumGut = $absSumGut + $sumGut;
    $absSumAuss = $absSumAuss + $sumAuss;
    
    $sumStk = 0;
    $sumGut = 0;
    $sumAuss = 0;
    $sumGew=0;
    
    $i=0;
  }
  if($altTeil != $teil and $i!=0){
    echo "
      <tr>
      <td></td>
      <td colspan='8' style='line-height:1mm;'><hr style='background-color:blue; border:none; height:1px; margin:0px;' /></td>
      </tr>
      <tr class='sumSmall'>
        <td></td>
        <td style='text-align:center; font-size:3mm; color:blue;'>Summe</td>
        <td>".round($sumStk) ."</td>
        <td>".round($sumGut) ."</td>
        <td>".round($sumAuss) ."</td>
        <td colspan='4'>&nbsp;</td>
      </tr>
    ";

    $absSumStk = $absSumStk + $sumStk;
    $absSumGut = $absSumGut + $sumGut;
    $absSumAuss = $absSumAuss + $sumAuss;
    
    $sumStk = 0;
    $sumGut = 0;
    $sumAuss = 0;
  }
  
  if($i==0){
    echo"<table border='0' style='width:210mm; margin-top:1mm;'>
    <tr>
      <td class='upper'>AuftragsNr</td>
      <td colspan='8' style='text-align:center; font-size:4mm;'><span>$auftrNr&nbsp;&nbsp;&nbsp;(".$datum[0].")</span> </td>
    </tr>   
        <tr style='font-style:italic; font-weight:bold; font-size:4mm;'>
          <th style='border:none;'></th>
          <th>Teil</th>
          <th>StkAuftrag</th>
          <th>G Taet</th>
          <th>Auss</th>
          <th>pos-pal-nr</th>
          <th>Gew</th>
          <th>GesGew</th>
          <th>AuftragsNr-Exp</th>
        </tr>
    ";
  }

    echo "
    <tr>
      <td>                               </td>
      <td>".        $S816['Teil']."      </td>
      <td>";
      $sumStk = $sumStk + $S816['stueck'];
      echo control($S816['stueck'])."   </td>
      <td>";
      $sumGut = $sumGut + $S816['gutstk'];
      echo control($S816['gutstk'])."   </td>
      <td>";
      $sumAuss = $sumAuss + $S816['auss'];
      echo control($S816['auss'])."     </td>
      <td>".        $S816['pos-pal-nr']."</td>
      <td>".        $S816['Gew']."       </td>
      <td>";
      $sumGew = $sumGew + $S816['vahacelkem'];                           
              echo control($S816['vahacelkem'])."</td>
      <td>".control($S816['aufexp'])."    </td>
    </tr>
    ";
    $altTeil = $teil;
    $altAuftrNr = $auftrNr;
$i++;
}
    $absSummGew = $absSummGew + $sumGew;
    $absSumStk = $absSumStk + $sumStk;
    $absSumGut = $absSumGut + $sumGut;
    $absSumAuss = $absSumAuss + $sumAuss;
    echo "
    <tr>
      <td></td>
      <td colspan='8'><hr style='background-color:blue; border:none; height:1px;' /></td>
      </tr>
    <tr class='sumSmall'>
        <td></td>
        <td style='text-align:center; font-size:3mm; color:blue;'>Summe</td>
        <td>".round($sumStk) ."</td>
        <td>".round($sumGut) ."</td>
        <td>".round($sumAuss) ."</td>
        <td colspan='4'>&nbsp;</td>
      </tr>
    <tr>
      <td colspan='9'>
        <hr style='border-top:1px dotted black; border-bottom:none; border-left:none; border-bottom:right;' />
      </td>
    </tr>
    <tr class='summ'>
      <td>Summe</td><td colspan='6'>&nbsp;</td>
      <td>".round($sumGew) ."</td><td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan='2'>Gesamtsumme</td>
      <td>".round($absSumStk) ."</td>
      <td>".round($absSumGut) ." </td>
      <td>".round($absSumAuss) ."</td>
      <td></td>
      <td></td>
      <td style='border:1px solid black;'>".round($absSummGew) ."</td>
    </tr>
  </table>";
    


$query = "DROP VIEW `hotovo_S816`, `hotovo_aus_S816`, `hotovo_sum_S816`";
mysql_query($query) or die(mysql_error());
}

function control($value){
  //if($value==''){$value=0;}
  if(is_numeric($value)){$value= round($value); $value = str_replace(".", ",", $value);}
  return $value;
}
?>
