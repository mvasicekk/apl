<?php

include '../fns_dotazy.php';

if(!$_GET['auftrNr']){$auftrag='';}
$auftrag=$_GET['auftrNr'];

if($auftrag==''){
echo "
<form action='d606.php' method='get'>
<input type='text' size='7' name='auftrNr' id='auftrNr' value='' />
<input type='submit' name='OK' value='OK' />
</form>
";
}else{
dbConnect();
// Vytvoøí pohled na tabulku Dauftr
$query = "
create view view_d607_dauftr
as
SELECT dauftr.AuftragsNr, dauftr.`pos-pal-nr`as import_pal,aufdat, max(if(kzgut='G',`pal-nr-exp`,0)) as export_pal,dauftr.Teil, sum(if(kzgut='G',`Stück`,0)) as import_stk,sum(if(kzgut='G',`stk-exp`,0)) as export_stk, sum(if(`taetkz-nr`='P',vzkd,0)) as S0011P,sum(if(kzgut='G',`Stück`,0))*sum(if(`taetkz-nr`='P',vzkd,0)) as sumS0011P, sum(if(`taetkz-nr`='P',1,0)) as cnt_S0011P,sum(if(`taetkz-nr`='T',vzkd,0)) as S0011T, sum(if(kzgut='G',`Stück`,0))*sum(if(`taetkz-nr`='T',vzkd,0)) as sumS0011T, sum(if(`taetkz-nr`='T',1,0)) as cnt_S0011T,sum(if(Stat_Nr='S0041',vzkd,0)) as S0041, sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0041',vzkd,0)) as sumS0041, sum(if(Stat_Nr='S0041',1,0)) as cnt_S0041, sum(if(Stat_Nr='S0051',vzkd,0)) as S0051, sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0051',vzkd,0)) as sumS0051, sum(if(Stat_Nr='S0051',1,0)) as cnt_S0051, sum(if(Stat_Nr='S0061',vzkd,0)) as S0061, sum(if(kzgut='G',`Stück`,0))*sum(if(`Stat_Nr`='S0061',vzkd,0)) as sumS0061, sum(if(Stat_Nr='S0061',1,0)) as cnt_S0061, sum(if(kzgut='G',`Stück`,0))*sum(vzkd) as sumvzkd, sum(if(kzgut='G',`Stück`,0))*dkopf.gew as imp_gew FROM DAUFTR JOIN dkopf using (teil) JOIN `dtaetkz-abg` ON dauftr.abgnr = `dtaetkz-abg`.`abg-nr` JOIN daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr where (((DAUFTR.`termin`) = '$auftrag')) group BY dauftr.AuftragsNr, import_pal,dauftr.Teil;";
mysql_query($query) or die(mysql_error());

// Vytvoøí pohled na tabulku Drueck
$query = "
create view view_d607_drueck
as
  SELECT drueck.AuftragsNr, drueck.`pos-pal-nr`as import_pal,drueck.Teil, sum(if(`taetkz-nr`='T',drueck.`Stück`,0))  as sum_stk_T, sum(if(`taetkz-nr`='P',drueck.`Stück`,0))  as sum_stk_P, sum(if(`taetkz-nr`='St',drueck.`Stück`,0))  as sum_stk_St, sum(if(`taetkz-nr`='G',drueck.`Stück`,0))  as sum_stk_G, sum(if(`taetkz-nr`='E',drueck.`Stück`,0))  as sum_stk_E, sum(if(auss_typ=2,`auss-Stück`,0)) as auss2, sum(if(auss_typ=4,`auss-Stück`,0)) as auss4, sum(if(auss_typ=6,`auss-Stück`,0)) as auss6 FROM DRUECK JOIN `dtaetkz-abg`  ON drueck.TaetNr=`dtaetkz-abg`.`abg-nr` JOIN dauftr on drueck.auftragsnr=dauftr.auftragsnr and drueck.teil=dauftr.teil and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.taetnr=dauftr.abgnr where (((dauftr.`termin`) = '$auftrag')) group BY drueck.AuftragsNr, import_pal,drueck.Teil;";
mysql_query($query) or die(mysql_error());

// Vytvoøí pohled na tabulku drueck
$query = "
create view view_d607_drueck_gesamt
as
SELECT drueck.AuftragsNr,  drueck.teil,drueck.`pos-pal-nr` as import_pal, sum(drueck.`Stück`)  as sum_stk_Gtat FROM DRUECK JOIN dauftr on  (drueck.auftragsnr=dauftr.auftragsnr) and (drueck.teil=dauftr.teil) and (drueck.`pos-pal-nr`=dauftr.`pos-pal-nr`) and (drueck.taetnr=dauftr.abgnr) where (((dauftr.`termin`) = '$auftrag') and (dauftr.kzgut='G')) group BY drueck.AuftragsNr,drueck.teil,import_pal";
mysql_query($query) or die(mysql_error());

// Vykoná dotaz na uvedené pohledy


$query = "
SELECT view_d607_dauftr.AuftragsNr, view_d607_dauftr.import_pal, view_d607_dauftr.export_pal, view_d607_dauftr.Teil, view_d607_dauftr.import_stk, view_d607_drueck.sum_stk_T, view_d607_drueck.sum_stk_P, view_d607_drueck.sum_stk_St, view_d607_drueck.sum_stk_G, view_d607_drueck.sum_stk_E, view_d607_drueck.auss2, view_d607_drueck.auss4, view_d607_drueck.auss6, view_d607_dauftr.S0011P, view_d607_dauftr.sumS0011P, view_d607_dauftr.cnt_S0011P, view_d607_dauftr.S0011T, view_d607_dauftr.sumS0011T, view_d607_dauftr.cnt_S0011T, view_d607_dauftr.S0041, view_d607_dauftr.sumS0041, view_d607_dauftr.cnt_S0041, view_d607_dauftr.S0051, view_d607_dauftr.sumS0051, view_d607_dauftr.cnt_S0051, view_d607_dauftr.S0061, view_d607_dauftr.sumS0061, view_d607_dauftr.cnt_S0061, view_d607_dauftr.imp_gew, view_d607_dauftr.sumvzkd, view_d607_dauftr.export_stk, view_d607_dauftr.aufdat, `sum_stk_Gtat`-`import_stk` AS GDiff
FROM (view_d607_dauftr LEFT JOIN view_d607_drueck ON (view_d607_dauftr.Teil = view_d607_drueck.Teil) AND (view_d607_dauftr.import_pal = view_d607_drueck.import_pal) AND (view_d607_dauftr.AuftragsNr = view_d607_drueck.AuftragsNr)) LEFT JOIN view_d607_drueck_gesamt ON (view_d607_dauftr.Teil = view_d607_drueck_gesamt.teil) AND (view_d607_dauftr.import_pal = view_d607_drueck_gesamt.import_pal) AND (view_d607_dauftr.AuftragsNr = view_d607_drueck_gesamt.AuftragsNr);
";

$dbresult = mysql_query($query) or die(mysql_error());
$i=0;
$u=1;
$pagCon =50;
$rows = mysql_affected_rows();

if($rows>=50){
$sumPag = round((mysql_affected_rows()/50)+1);
}else{
$sumPag = 1;
}

$sumGDiff = 0;
$sumTr = 0;
$sumP = 0;
$sumSt = 0;
$sumE = 0;
$sumF = 0;
$sumVzKd = 0;
While($D607=mysql_fetch_assoc($dbresult)){
$datum = split(" ", $D607['aufdat']);
  if($i==0){
  echo "<html>
  <head>
    <title>D607    Auftragsübersicht (P) </title>
    <style type='text/css' media='screen, print'>
    /* <![CDATA[ */

    .heading{border:none; vertical-align: bottom; margin-bottom:5px;}
    .heading td{border:none;}
    body{width:280mm;}
    table{width:280mm; border-top:2px solid black; clear:left; margin-top:0px;}
    th{ font-size:2mm; font-family:Arial; font-weight:bold; border:1px solid black;}
    td{font-size:2mm; font-family:Arial; font-weight:normal; text-align:right; border-left:1px solid black; border-right:1px solid black; border-bottom:1px solid black;}
    .down{width:8mm; border-bottom:2px solid black;}
    .slim{width:1mm; border-right:none; font-size:1mm; vertical-align: top; text-align:left;}
    h1{margin:0px 50px 10px 20px; float:left;}
    h3{float:left;}
    .footer,.footerEnd{height:10mm; vertical-align:middle;}
    .footer .right{float:right;}
    .footer .left{float:left;}
    
    .footerEnd .right{float:right;}
    .footerEnd .left{float:left;}
    

    
    .sum td{border:none; font-size:2mm; font-weight:bold;}
    /* ]]> */
    </style>
    <style type='text/css' media='print'>
    /* <![CDATA[ */
    .button{display:none;}
    .footerEnd{position:absolute; top:". $sumPag * 200 ."mm; width:280mm;}
    .footerEnd .right{float:right;}
    .footerEnd .left{float:left;}
    /* ]]> */
    </style>
  </head>
    
    <body>
    <table class='heading'>
      <tr>
        <td>
          <span style='font-size:3mm; font-weight:bold;'>
            Abydos s.r.o. 
          </span>
        </td>
        <td>
          <h1>D607    Auftragsübersicht (P)</h1>
        </td>
        <td>
          <h3>Import Datum:  ".$datum[0]."
            
            <span style='margin-left:5mm;'>
              Auftrag: (P) ".$D607['AuftragsNr']."
              
            </span></h3>
        </td>
      </tr>
    </table>
      <table border='0' cellspacing='0'>
        <tr>
          <th rowspan='2' style='border-bottom:2px solid black;'>IM<br />Auftr</th>
          <th rowspan='2' style='border-bottom:2px solid black;'>Teil</th>
          <th colspan='3' style='border-right:2px solid black;'>Pallete</th>
          <th colspan='10'>Stueckzahl</th>
          <th rowspan='2' style='border-bottom:2px solid black; border-right:2px solid black;'>Gew<br /> (to)<br /> IMP</th>
          <th colspan='3' style='border-right:2px solid black;'>S0011 (T)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0011 (P)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0041 (St)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0051 (E)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0061 (F)</th>
          <th rowspan='2' style='border-bottom:2px solid black; border-right:2px solid black;'>GESAMT<br />VzKd</th>
          </tr>
          <tr>
          <th class='down'>IMP</th>
          <th class='down'>EXP</th>
          <th class='down' style='border-right:2px solid black;'>Stk. EXP</th>
          <th class='down'>IM</th>
          <th class='down'>Tr</th>
          <th class='down'>PU</th>
          <th class='down'>St</th>
          <th class='down'>G</th>
          <th class='down'>E</th>
          <th class='down'>(2)</th>
          <th class='down'>(4)</th>
          <th class='down'>(6)</th>
          <th class='down'>G- <br />IMP</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th></tr>";
  $i=1;
  }
  if($i==$pagCon){
    echo "</table>
      <div class='footer'><br />
        <span class='left'>".date('j.n.Y H:i:s')."</span><span class='right'>Strana $u/$sumPag</span>
      </div>    
    <table border='0' cellspacing='0'>
    <tr>
          <th rowspan='2' style='border-bottom:2px solid black;'>IM<br />Auftr</th>
          <th rowspan='2' style='border-bottom:2px solid black;'>Teil</th>
          <th colspan='3' style='border-right:2px solid black;'>Pallete</th>
          <th colspan='10'>Stueckzahl</th>
          <th rowspan='2' style='border-bottom:2px solid black; border-right:2px solid black;'>Gew<br /> (to)<br /> IMP</th>
          <th colspan='3' style='border-right:2px solid black;'>S0011 (T)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0011 (P)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0041 (St)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0051 (E)</th>
          <th colspan='3' style='border-right:2px solid black;'>S0061 (F)</th>
          <th rowspan='2' style='border-bottom:2px solid black; border-right:2px solid black;'>GESAMT<br />VzKd</th>
          </tr>
          <tr>
          <th class='down'>IMP</th>
          <th class='down'>EXP</th>
          <th class='down' style='border-right:2px solid black;'>Stk. EXP</th>
          <th class='down'>IM</th>
          <th class='down'>Tr</th>
          <th class='down'>PU</th>
          <th class='down'>St</th>
          <th class='down'>G</th>
          <th class='down'>E</th>
          <th class='down'>(2)</th>
          <th class='down'>(4)</th>
          <th class='down'>(6)</th>
          <th class='down'>G- <br />IMP</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th>
          <th colspan='2' class='down'>VzKd<br />min</th>
          <th class='down' style='border-right:2px solid black;'>VzKd<br />Ges</th></tr>
    ";
    $i = 1;
    $u++;
    $pagCon =$pagCon +50;
  }
    echo "<tr>
            <td>
              ".control($D607['AuftragsNr'])."</td>
            <td>
              ".$D607['Teil']."</td>
            <td>
              ".control($D607['import_pal'])."</td>
            <td>
              ".control($D607['export_pal'])."</td>
            <td style='border-right:2px solid black;'>
              ".control($D607['export_stk'])."</td>
            <td>                           
              ".control($D607['import_stk'])."</td>
            <td>                           
              ".control($D607['sum_stk_T'])."</td>
            <td>                           
              ".control($D607['sum_stk_P'])."</td>
            <td>                           
              ".control($D607['sum_stk_St'])."</td>
            <td>                           
              ".control($D607['sum_stk_G'])."</td>
            <td>
              ".control($D607['sum_stk_E'])."</td>
            <td>                           
              ".control($D607['auss2'])."</td>
            <td>                           
              ".control($D607['auss4'])."</td>
            <td>                           
              ".control($D607['auss6'])."</td>
            <td>".            
          control($D607['imp_gew'])."</td>
            <td>";
            $sumGDiff = $sumGDiff + control($D607['GDiff']);                           
              echo control($D607['GDiff'])."</td>
            <td class='slim'>                           
              ".control($D607['cnt_S0011T'])."</td>
            <td style='border-left:none;'>                           
              ".control($D607['S0011T'])."</td>
            <td>";
            $sumTr = $sumTr + control($D607['sumS0011T']);                           
              echo control($D607['sumS0011T'])."</td>
            <td class='slim'>                           
              ".control($D607['cnt_S0011P'])."</td>
            <td style='border-left:none;'>                           
              ".control($D607['S0011P'])."</td>
            <td>";     
            $sumP = $sumP + control($D607['sumS0011P']);                      
              echo control($D607['sumS0011P'])."</td>
            <td class='slim'>                           
              ".control($D607['cnt_S0041'])."</td>
            <td style='border-left:none;'>
              ".control($D607['S0041'])."</td>
            <td>";
            $sumSt = $sumSt + control($D607['sumS0041']);                           
              echo control($D607['sumS0041'])."</td>
            <td class='slim'>                           
              ".control($D607['cnt_S0051'])."</td>
            <td style='border-left:none;'>                           
              ".control($D607['S0051'])."</td>
            <td> ";
            $sumE = $sumE + control($D607['sumS0051']);                          
              echo control($D607['sumS0051'])."</td>
            <td class='slim'>                           
              ".control($D607['cnt_S0061'])."</td>
            <td style='border-left:none;'>                           
              ".control($D607['S0061'])."</td>
            <td>";
            $sumF = $sumF + control($D607['sumS0061']);                           
              echo control($D607['sumS0061'])."</td>
            <td>";
            $sumVzKd = $sumVzKd + control($D607['sumvzkd']);                           
              echo control($D607['sumvzkd'])."</td>
            </tr>";

    $i++;

}

echo "
<tr class='sum'>
  <td></td><td></td><td colspan='3' style='text-align:left;'>SUMME Vz min</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
  <td></td><td></td><td>$sumGDiff</td><td></td><td></td><td>$sumP</td><td></td><td></td><td>$sumTr</td><td></td><td></td><td>$sumSt</td>
  <td></td><td></td><td>$sumE</td><td></td><td></td><td>$sumF</td><td>$sumVzKd</td>
</tr>
</table>
<br /><br />
            <h4 style='margin-bottom:0px; margin-left:150px;'> Erstellt: _________________________________________</h4>
              <span style='margin-left:350px;'>Datum/Unterschrift</span>
    <br /><br /><input type='button' value='Zurueck/Zpìt' class='button' onclick=\"location.href='./d605-php.php?auftrNr='\" />&nbsp;&nbsp;<input type='button' value='Ende/Konec' class='button' onclick=\"location.href='./index.php\" />
<div class='footerEnd'>
        <span class='left'>".date('j.n.Y H:i:s')."</span><span class='right'>Strana $u/$sumPag</span>
      </div> 
            </body>
            </html>";

$drop = "DROP VIEW `view_d607_dauftr`, `view_d607_drueck`, `view_d607_drueck_gesamt`";
mysql_query($drop) or die(mysql_error());
}

function control($value){
if($value==''){$value=0;}
if(is_numeric($value)){$value= round($value, 2); $value = str_replace(".", ",", $value);}
return $value;
}
?>

