<?php
  require "../fns_dotazy.php";
dbConnect();

$sql = "select teillang,teilbez,kunde from dkopf where ((teil rlike '.*".$_GET['teil'].".*') and (kunde=175))";
$res = mysql_query($sql) or die(mysql_error());
$zaznam = mysql_fetch_array($res);

if(mysql_affected_rows()>0)
	echo $zaznam["teillang"].";".$zaznam["teilbez"].";".$zaznam["kunde"];
else
	echo "NOTEIL";
?>
