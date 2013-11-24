<?
include "../fns_dotazy.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
    
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <title>
      Lager Bestand - Datum
    </title>

<style type="text/css">
body{width: 1000px;}
td{width: 50px;}
h2{margin-bottom: 5px; margin-top: 5px; height: 30px;}
h3{margin-left: 400px; margin-top: 5px; height: 25px;}
.bigger{width: 150px;}
.yellow{background-color: rgb(255,255,0); font-weight: bold;}
.grey{background-color: rgb(120,120,120); font-weight: bold; color: rgb(255,255,255);}
.main{font-size: 13px;}
.aus{float: right; width: 250px; margin-left: 10px; font-size: 13px;}
.lager {border: 1px solid rgb(164,164,164); font-size: 13px;}
.bes {width:300px;}
.first {color: rgb(0,51,255); font-size: 20px;}
.lag{width: 70px;}
    </style>
<style type="text/css" media="print">
/* <![CDATA[ */
  input{visibility: hidden;}
/* ]]> */
</style>    
  </head>
  <body>
  <?
  
dbConnect();
$teil = $_GET["teil"];

function inventurDatum($teil){
$cas = date("Y-m-d")." 23:59:59";
$sql = "SELECT max(datum_inventur) as last_inventur FROM dlagerstk WHERE ((teil='$teil' ) AND (datum_inventur<='$cas'));";
$res = mysql_query($sql) or die("Datum> ".mysql_error());
$zaznam = mysql_fetch_array($res);
return $zaznam["last_inventur"];
}

function teilLagerStk($teil, $lager, $inventurDatum){
$sql = "SELECT stk FROM dlagerstk WHERE (((teil)='$teil') AND ((lager)='$lager') and (datum_inventur<='$inventurDatum'));";
$res = mysql_query($sql) or die("Stk> ".mysql_error());
$zaznam = mysql_fetch_array($res);
if($zaznam["stk"]==""){
return 0;
}else{
return $zaznam["stk"];
}
}

function teilLagerPlus($teil, $lager, $inventurDatum){
$casNow = date("Y-m-d h:m:s");
$sql = "SELECT DLagerBew.teil, Sum(DLagerBew.gut_stk) AS stk, DLagerBew.lager_nach
FROM DLagerBew
WHERE (((DLagerBew.date_stamp) Between '$inventurDatum' And '$casNow'))
GROUP BY DLagerBew.teil, DLagerBew.lager_von
HAVING (((DLagerBew.teil)='$teil') AND ((DLagerBew.lager_nach)='$lager'));
";

$res = mysql_query($sql) or die("Stk-Minus> ".mysql_error());
$zaznam = mysql_fetch_array($res);
if($zaznam["stk"]==""){
return 0;
}else{
return $zaznam["stk"];
}
}

function teilLagerMinus($teil, $lager, $inventurDatum){
$casNow = date("Y-m-d h:m:s");
$sql = "SELECT DLagerBew.teil, Sum(DLagerBew.gut_stk) AS stk, DLagerBew.lager_von
FROM DLagerBew
WHERE (((DLagerBew.date_stamp) Between '$inventurDatum' And '$casNow'))
GROUP BY DLagerBew.teil, DLagerBew.lager_von
HAVING (((DLagerBew.teil)='$teil') AND ((DLagerBew.lager_von)='$lager'));
";

$res = mysql_query($sql) or die("Stk-Minus> ".mysql_error());
$zaznam = mysql_fetch_array($res);
  if($zaznam["stk"]==""){
    return 0;
  }else{
    return $zaznam["stk"];
  }
}

function teilLagerAus($teil, $lager, $inventurDatum){
$casNow = date("Y-m-d h:m:s");
$sql = "SELECT DLagerBew.teil, Sum(DLagerBew.auss_stk) AS stk, DLagerBew.lager_nach
FROM DLagerBew
WHERE (((DLagerBew.date_stamp) Between '$inventurDatum' And '$casNow'))
GROUP BY DLagerBew.teil, DLagerBew.lager_von
HAVING (((DLagerBew.teil)='$teil') AND ((DLagerBew.lager_nach)='$lager'));";

$res = mysql_query($sql) or die("Stk-Aus> ".mysql_error());
$zaznam = mysql_fetch_array($res);
  if($zaznam["stk"]==""){
    return 0;
  }else{
    return $zaznam["stk"];
  }
}
$inventurDatum = inventurDatum($teil);
echo "<h2>Lagerbestand - Datum</h2>";
echo "<h3>von: $inventurDatum &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
Bis: ".date("Y-m-d h:m:s")."<br />(letzte Inventur)</h3>";


$resStkLager = mysql_query("select * from DLager");
while($stkLager = mysql_fetch_array($resStkLager)){ 
Switch ($stkLager["Lager"]){
    case "0D": $stk0D= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minus0D= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plus0D= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "1R": $stk1R= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minus1R= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plus1R= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "2T": $stk2T= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minus2T= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plus2T= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "3P": $stk3P= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum);
               $minus3P= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum);
               $plus3P= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum);
               break;
               
    case "4R": $stk4R= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum);
               $minus4R= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plus4R= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "5K": $stk5K= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum);
               $minus5K= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plus5K= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "6F": $stk6F= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minus6F= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plus6F= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "8E": $stk8E= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minus8E= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plus8E= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "8X": $stk8X= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minus8X= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plus8X= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "9R": $stk9R= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minus9R= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plus9R= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "A2": $stkA2= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $aussStkA2= teilLagerAus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "A4": $stkA4= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $aussStkA4= teilLagerAus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "A6": $stkA6= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $aussStkA6= teilLagerAus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "B2": $stkB2= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $aussStkB2= teilLagerAus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "B4": $stkB4= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $aussStkB4= teilLagerAus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "B6": $stkB6= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $aussStkB6= teilLagerAus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "C4": $stkC4= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minusC4= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plusC4= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "XX": $stkXX= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minusXX= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plusXX= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
               
    case "XY": $stkXY= teilLagerStk($teil, $stkLager["Lager"], $inventurDatum); 
               $minusXY= teilLagerMinus($teil, $stkLager["Lager"], $inventurDatum); 
               $plusXY= teilLagerPlus($teil, $stkLager["Lager"], $inventurDatum); 
               break;
  }

}

$summ0D = $stk0D + $plus0D - $minus0D;
$summ1R = $stk1R + $plus1R - $minus1R;
$summ2T = $stk2T + $plus2T - $minus2T;
$summ3P = $stk3P + $plus3P - $minus3P;
$summ4R = $stk4R + $plus4R - $minus4R;
$summ5K = $stk5K + $plus5K - $minus5K;
$summ6F = $stk6F + $plus6F - $minus6F;
$summ8E = $stk8E + $plus8E - $minus8E;
$summ8X = $stk8X + $plus8X - $minus8X;
$summ9R = $stk9R + $plus9R - $minus9R;

$summC4 = $stkC4 + $plusC4 - $minusC4;
$summXX = $stkXX + $plusXX - $minusXX;
$summXY = $stkXY + $plusXY - $minusXY;
$summiA = $summ0D + $summ1R + $summ2T + $summ3P + $summ4R + $summ5K + $summ6F + $summ8E;


echo "<table border='1' class='main'><tr class='grey'><td class='bigger'>$teil</td>
<td>0D</td><td>1R</td><td>2T</td><td>3P</td><td>4R</td><td>5K</td><td>6F</td>
<td>8E</td><td class='bigger'>Summe i.A.</td><td>XX</td><td>XY</td><td>8X</td>
<td>9R</td></TR>
<tr><td class='bigger'>$inventurDatum</td><td>$stk0D</td><td>$stk1R</td>
<td>$stk2T</td><td>$stk3P</td><td>$stk4R</td><td>$stk5K</td><td>$stk6F</td>
<td>$stk8E</td><td></td><td>$stkXX</td><td>$stkXY</td><td>$stk8X</td><td>$stk9R</td></TR>
<tr><td class='bigger'>Beweg Plus</td><td>$plus0D</td><td>$plus1R</td>
<td>$plus2T</td><td>$plus3P</td><td>$plus4R</td><td>$plus5K</td><
td>$plus6F</td><td>$plus8E</td><td></td><td>$plusXX</td><td>$plusXY</td>
<td>$plus8X</td><td>$plus9R</td></TR>
<tr><td class='bigger'>Beweg Minus</td><td>$minus0D</td><td>$minus1R</td>
<td>$minus2T</td><td>$minus3P</td><td>$minus4R</td><td>$minus5K</td>
<td>$minus6F</td><td>$minus8E</td><td></td><td>$minusXX</td><td>$minusXY</td>
<td>$minus8X</td><td>$minus9R</td></TR>
<tr class='yellow'><td class='bigger'>Summe Teil</td><td>$summ0D</td>
<td>$summ1R</td><td>$summ2T</td><td>$summ3P</td><td>$summ4R</td><td>$summ5K</td>
<td>$summ6F</td><td>$summ8E</td><td>$summiA</td><td>$summXX</td><td>$summXY</td>
<td>$summ8X</td><td>$summ9R</td></TR>
</table>
<br />
<table border='1' class='aus'>
<tr><td>B2:</td><td>$aussStkB2</td><td>$stkB2</td></tr>
<tr><td>B4:</td><td>$aussStkB4</td><td>$stkB4</td></tr>
<tr><td>B6:</td><td>$aussStkB6</td><td>$stkB6</td></tr>
<tr><td>Ges:</td><td>".$aussStkB2 += $aussStkB4 += $aussStkB6."</td></tr>
</table>

<table border='1' class='aus'>
<tr><td>A2:</td><td>$aussStkA2</td><td>$stkA2</td></tr>
<tr><td>A4:</td><td>$aussStkA4</td><td>$stkA4</td></tr>
<tr><td>A6:</td><td>$aussStkA6</td><td>$stkA6</td></tr>
<tr><td>Ges:</td><td>".$aussStkA2 += $aussStkA4 += $aussStkA6."</td></tr>
</table>";

$res = mysql_query("SELECT * FROM `dlager`") or die(mysql_error());
echo "<table class='lager'>";
echo "<tr class='first'><td class='lag'>Lager</td><td class='bes'>LagerBeschreibung</td></tr>";
while($lager = mysql_fetch_array($res)){
echo "<tr><td class='lag'>".$lager["Lager"]." </td><td class='bes'> ".$lager["LagerBeschreibung"]."</td></tr>";
}
echo "</table>";
mysql_close();
?>
<input type="button" value="Zpět/Zurück" id="konec" style="width: 100px; margin-left: 20px; margin-bottom:3px;" onClick="location.href='./dlagstk.php'">
<input type="button" value="Ende/Konec" id="konec" style="width: 100px; margin-left: 20px; margin-bottom:3px;" onClick="location.href='../index.php'">
</body>
</html>
