<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>
  <style media="print, screen">
  /* <![CDATA[ */
body{width:206mm; margin:auto;}
    div{margin:0px; padding:0px;}
  .page{margin:0px; padding:0px 0px 10px 0px;}
  .left, .right{float:left; width:92mm; border: 2px solid black; padding:5px;}
  .right{margin-left:8mm;}
  .complet{width:204mm; margin:auto;}
 
   .topl{width:60mm; float:left; padding:2px;}
  .topr{width:20mm; float:left; padding:2px; border-left:1px solid black; }
  .middle{width:95mm; clear:left; float:left; padding:5px; border-top:1px solid black; border-bottom:1px solid black;}
  
  strong{font-size:13px;}
  .botr{width:50mm; float:left;  padding:5px;}
  .botl{width:25mm; float:left; clear:left; padding:5px;}
  .botr span{ float:right;}
  .botr{border-left:1px solid black;}
  .cistic{visibility:hidden; clear:both; max-height:15mm; page-break-after:always;}
  .header{text-align:center; margin:0px 10px 30px 10px; padding:10px;}
  .header strong{margin:10px; font: normal normal bold 30px 'Times New Roman';}
  .header em{margin-left:160px;}
  /* ]]> */
  </style>
<style media='print'>
.noprint{display:none;}
</style>
  </head>
  <body>
  <span class='header'>Abydos s.r.o<strong>Lager Zettel / Vysacky</strong><em><?echo date("Y-n-d H:i:s");?></em></span><br /><br />
  <?
  if(empty($_GET['kunde'])){$kunde='none';}else{$kunde=$_GET['kunde'];}
  if(empty($_GET['regal'])){$regal='none';}else{$regal=$_GET['regal'];}
  if(empty($_GET['teil'])){$teil='none';}else{$teil=$_GET['teil'];}
  if(empty($_GET['start'])){$start=0;}else{$start=$_GET['start'];}
  If($kunde=='none' and $regal=='none'){
  echo "<form action='./vysacky.php' method='get'>
  <label for='kunde'>Kunde Nr.&nbsp;</label><input type='text' size='4' name='kunde' id='kunde' /><br />
  <label for='regal'>Regal Nr.&nbsp;</label><input type='text' size='4' name='regal' id='regal' /><br />
  <label for='teil'>Teil Nr.&nbsp;</label><input type='text' size='8' name='teil' id='teil' /><br />
  <input type='hidden' value='0' name='start' id='start' />
  <input type='submit' name='OK' value='OK' />
  </form>";
  
  }else{
  $sql = "select * from `dkopf` where (";
    if($kunde == 'none'){$kunde='%';}else{$sql .= "(`Kunde` like '$kunde') and"; $i=1;}
    if($regal == 'none'){$regal='%';}else{$sql .= "(`Muster-Platz` like '$regal') and"; $i=2;}
    if($teil == 'none'){$teil='%';}else{$sql .= "(`Teil` like '$teil')  ";$i=0;}
    switch($i){
    case '1':$sql = substr($sql,0, -4);break;
    case '2':$sql = substr($sql,0, -4);break;    
    }
    $start2 = $start +10;
    $sql2 = $sql.") order by `teillang` limit $start2,10";
    $sql .= ") order by `teillang` limit $start,10";
  require "../fns_dotazy.php";
  dbConnect();
  mysql_query("set names utf8");
  //Starej select
  //echo $sql = "select * from `DKOPF` where ((`Kunde` like '$kunde') and (`Muster-Platz` like '$regal') and (`Teil` like '$teil')) order by `teillang`";
 
  $res = mysql_query($sql) or die(mysql_error());
  $i=1;
echo "<div class='page'>";
  while($zaznam = mysql_fetch_array($res)){
if($i==1){

}
  
  if($i%2){
echo "<div class='complet'>
  <div class='left'>
    <div class='topl'>
      <strong>Deska/Plate:</strong><br /><span style='font-size:25px; font-weight:bold;'>".$zaznam['teillang']."</span>
    </div>
    <div class='topr'>
        <strong>Zákazník/Kunde:</strong><br />".$zaznam['Kunde']."
    </div>
    <div class='middle'>
        <strong>Teil/Díl:</strong><br /><span style='font-size:22px; font-weight:bold;'>".$zaznam['Teil']."</span><br />".$zaznam['Teilbez']."
    </div>
    <div class='botl'>
      <strong>Gew. - Anl./Váha:</strong><br /><span>".control($zaznam['Gew'])." kg</span>
    </div>
    <div class='botr'>
      <strong>Regál/Regal</strong><br /><span style='font-size:25px; font-weight:bold;'>&#8203;".$zaznam['Muster-Platz']."</span>
    </div>
  </div>";

}else{
echo "<div class='right'>
     <div class='topl'>
      <strong>Deska/Plate:</strong><br /><span style='font-size:25px; font-weight:bold;'>".$zaznam['teillang']."</span>
    </div>
    <div class='topr'>
        <strong>Zákazník/Kunde:</strong><br />".$zaznam['Kunde']."
    </div>
    <div class='middle'>
        <strong>Teil/Díl:</strong><br /><span style='font-size:22px; font-weight:bold;'>".$zaznam['Teil']."</span><br />".$zaznam['Teilbez']."
    </div>
    <div class='botl'>
      <strong>Gew. - Anl./Váha:</strong><br /><span>".control($zaznam['Gew'])." kg</span>
    </div>
    <div class='botr'>
      <strong>Regál/Regal</strong><br /><span style='font-size:25px; font-weight:bold;'>&#8203;".$zaznam['Muster-Platz']."</span>
    </div>
</div>";
 }

 $i++;
}
 echo "</div>";
 $oldStart = $start;

 mysql_free_result($res);

 $res_str = mysql_query($sql2);
 $pocet = mysql_affected_rows()/10;

$next = $start+10;
if($start==0){$back=0;}else{$back = $oldStart-10;}
if($pocet==0){$next = $oldStart;}else{$next= $oldStart+10;}
echo "<span class='noprint'><a href='./vysacky.php?kunde=$kunde&regal=$regal&teil=$teil&start=$back&OK=OK'>Forherige Seite</a> .... <a href='./vysacky.php?kunde=$kunde&regal=$regal&teil=$teil&start=$next&OK=OK'>Naechste Seite</a></span>";
mysql_close();
}

function control($value){
if($value==''){$value=0;}

if(is_numeric($value)){
  if($value<=10){
    $value= number_format($value,3,',',' ');
  }elseif(($value>10) && ($value<100)){
    $value= number_format($value,2,',',' ');
  }elseif($value>=100){
    $value= number_format($value,1,',',' ');
  }

}
return $value;
}
?>
  </body>
</html>
