<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>
  <style media="print, screen">
  /* <![CDATA[ */

    div{margin:0px; padding:0px;}
  .page{margin:0px; padding:0px 0px 10px 0px;}
  .left, .right{float:left; width:92mm; border: 1px solid black; padding:5px;}
  .right{margin-left:8mm;}
  .complet{width:204mm; margin:auto;}
  .topl, .topm, .topr{width:27mm; float:left; padding:2px;}
  .topm{border-left:1px solid black; border-right:1px solid black;}
  .middle{width:84mm; clear:left; float:left; padding:5px; border-top:1px solid black; border-bottom:1px solid black;}
  
  .botl, .botr{max-width:42mm; min-width:36mm; float:left; padding:5px;}
  .botr{border-left:1px solid black;}
  .cistic{visibility:hidden; clear:both; max-height:15mm; page-break-after:always;}
  /* ]]> */
  </style>

  </head>
  <body>
  <?
  $kunde=$_GET['kunde'];
  echo "kunde = $kunde";
  If($kunde==''){
  echo "<form action='./vysacky.php' method='get'>
  <input type='text' size='4' name='kunde' id='kunde' />
  <input type='submit' name='OK' value='OK' />
  </form>";
  
  }elseif($kunde<>'none' or $kunde<>0){
  require "../fns_dotazy.php";
  dbConnect();
  //$sql = "select";
  $sql = "select * from `dkopf` where `Kunde`='$kunde' order by `teillang`";
  $res = mysql_query($sql) or die(mysql_error());
  $i=1;

  while($zaznam = mysql_fetch_array($res)){
if($i==1){
echo "<div class='page'>";
}
  
  if($i%2){
echo "<div class='complet'>
  <div class='left'>
    <div class='topl'>
      <strong>Z�kazn�k/Kunde:</strong><br />".$zaznam['Kunde']."
    </div>
    <div class='topm'>
      <strong>V�ha/Gew.</strong><br />Neto: ".$zaznam['Gew']." kg<br />Bruto: ".$zaznam['BrGew']." kg
    </div>
    <div class='topr'>
        <strong>Reg�l/Regal</strong><br />".$zaznam['Muster-Platz']."
    </div>
    <div class='middle'>
        <strong>Jm�no d�lu/Teil Bez.:</strong><br />".$zaznam['Teilbez']."
    </div>
    <div class='botl'>
      <strong>Orig ��slo/Nummer</strong><br />".$zaznam['teillang']."
    </div>
    <div class='botr'>
      <strong>Aby ��slo/Nummer</strong><br />".$zaznam['Teil']."
    </div>
  </div>";

}else{
echo "<div class='right'>
     <div class='topl'>
      <strong>Z�kazn�k/Kunde:</strong><br />".$zaznam['Kunde']."
    </div>
    <div class='topm'>
      <strong>V�ha/Gew.</strong><br />Neto: ".$zaznam['Gew']." kg<br />Bruto: ".$zaznam['BrGew']." kg
    </div>
    <div class='topr'>
        <strong>Reg�l/Regal</strong><br />".$zaznam['Muster-Platz']."
    </div>
    <div class='middle'>
        <strong>Jm�no d�lu/Teil Bez.:</strong><br />".$zaznam['Teilbez']."
    </div>
    <div class='botl'>
      <strong>Orig ��slo/Nummer</strong><br />".$zaznam['teillang']."
    </div>
    <div class='botr'>
      <strong>Aby ��slo/Nummer</strong><br />".$zaznam['Teil']."
    </div>
</div>";
 }
if($i==12){
echo "</div><hr class='cistic' />";
$i=0;
}
$i++;
}
mysql_close();
}
?>
  </body>
</html>
