<?php
  require "../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');
$sql = "select Name, Vorname, Schicht from dpers where (PersNr = '".$_GET['persnr']."' and (dpersstatus='MA' or dpersstatus='DOHODA'))";
$res = mysql_query($sql) or die(mysql_error());
$zaznam = mysql_fetch_array($res);
if(mysql_affected_rows()>0)
	echo $zaznam["Name"].",".$zaznam["Vorname"].";".$zaznam["Schicht"];
else
	echo "nopersnr";
?>
