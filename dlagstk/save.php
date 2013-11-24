<?php
  require "../fns_dotazy.php";
dbConnect();

$resLager = mysql_query("select * from dlager");

$teil = $_POST["Teil"];
$datum = $_POST["datum"];


//echo "teil=$teil<br>";
//echo "datum=$datum<br>";

while($lager=mysql_fetch_array($resLager))
{
	$sqlcontroll = "select * from dlagerstk where teil='$teil' and lager ='".substr($_POST["Lag".$lager["Lager"]],0,2)."';";	
	//echo "sqlcontroll = $sqlcontroll<br>";
	
	$rescontroll = mysql_query($sqlcontroll);
  	if(mysql_affected_rows()>0)
  	{
    	$sql = "Update dlagerstk set stk=".$_POST[$lager["Lager"]."Stk"].",`datum_inventur`='$datum' where teil='$teil' and lager ='".substr($_POST["Lag".$lager["Lager"]],0,2)."';";
    	//echo "sql=$sql<br>";
    	$res = mysql_query($sql) or die("Update:".mysql_error());
  	}
  	else
  	{
    	$sql = "insert into dlagerstk (`teil`, `lager`, `stk`, `datum_inventur`) values ('$teil' ,'".substr($_POST["Lag".$lager["Lager"]],0,2)."', '".$_POST[$lager["Lager"]."Stk"]."', '$datum');";
    	//echo "sql=$sql<br>";
    	$res = mysql_query($sql) or die("Insert:".mysql_error());
  	}
}

// a jeste smazu vsechny predchozi zaznamu v dlagerbew pred datumem inventury
// 2010-04-01 --- mazani nelze delat, protoze pri exportu se odkazuju na importni mnozstvi
// 
//$sql_delete = "delete from dlagerbew where ((teil='$teil') and (date_stamp<'$datum'))";
//mysql_query($sql_delete);

// a vrati se zpet na zadani hodnot
header("location: ./dlagstk.php");
    
?>
