<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];
	
	$amnr=$value;

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    /////////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query('set names utf8');
	
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
		
	
	// 1. zjistim existenci cisla prostredku a zaskrtnuti pro zadavani
	$sql = "select `art-nr` as amnr, `art-name1` as name1,`art-name2` as name2 from `eink-artikel` where ((`art-nr`='$amnr') and (am_ausgabe<>0))";
	
	$resTeil = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		// nasel jsem
		$row=mysql_fetch_array($resTeil);
		$output .= "<amnr>".$row['amnr']."</amnr>";
		$output .= "<name1>".$row['name1']."</name1>";
		$output .= "<name2>".$row['name2']."</name2>";
	}
	else
	{
			// nenasel
			// jeste povolim nulu v amnr
			if($amnr==0)
			{
				$output .= "<amnr>$amnr</amnr>";
				$output .= "<name1>nic</name1>";
				$output .= "<name2> </name2>";
			}
			else
			{
				$output.="<error>";
				$output .= "<errordescription>ERROR-NOAMNR</errordescription>";
				//	$output .= "<sql>$sql</sql>";
				$output.="</error>";
			}
	}
	
		
	$output.="<controlid>";
	$output .= $controlid;
	$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>

