<?php
  require "../fns_dotazy.php";
dbConnect();

$sql = "select PersNr, Name, Vorname, Schicht from dpers where (PersNr = '".$_GET['cislo']."')";
$res = mysql_query($sql) or die(mysql_error());
$zaznam = mysql_fetch_array($res);

echo "vypis('".$zaznam["Name"]."','".$zaznam["Vorname"]."','".$zaznam["Schicht"]."');";
?>
