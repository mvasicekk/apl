<?php
  require "../fns_dotazy.php";
dbConnect();

$sql = "select Name, Vorname, Schicht from dpers where (PersNr = '".$_GET['persnr']."')";
$res = mysql_query($sql) or die(mysql_error());
$zaznam = mysql_fetch_array($res);
if(mysql_affected_rows()>0)
	echo $zaznam["Name"].",".$zaznam["Vorname"].";".$zaznam["Schicht"];
else
	echo "nopersnr";
?>
