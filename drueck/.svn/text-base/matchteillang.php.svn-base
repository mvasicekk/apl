<?php
  require "../fns_dotazy.php";
dbConnect();

$sql = "select teil,teilbez,kunde from dkopf where (teillang rlike '.*".$_GET['teillang'].".*')";
$res = mysql_query($sql) or die(mysql_error());
$zaznam = mysql_fetch_array($res);
if(mysql_affected_rows()>0)
	echo $zaznam["teil"].";".$zaznam["teilbez"].";".$zaznam["kunde"];
else
	echo "NOTEIL";
?>
