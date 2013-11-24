<?
session_start();
require "../fns_dotazy.php";
dbConnect();

	$value = $_GET['value'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // cislo dilu nesmi existovat a nesmi byt prazdne
	/////////////////////////////////////////////////////////////////////////////////////////
	
	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	
	if(!strlen($value))
	{
		$teilnr="ERROR";
		$teilbez="Teil darf nicht leer sein / dil nesmi byt prazdny";
	}
	else
	{
		$sql="select teil from dkopf where (teil='".$value."')";
		$result=mysql_query($sql);
		// pokud mi dotaz vrati nejake zaznamy, tak je projdu
		if(mysql_affected_rows()>0)
		{
			$teilnr="ERROR";
			$teilbez="Teil existiert / dil uz existuje";
		}
		else
		{
			$teilnr="OK";
			$teilbez="OK";
		}
	}

	$output.="<teil>";
	$output .= '<teilnr>' . $teilnr . '</teilnr>';
	$output .= '<teilbez>' . $teilbez . '</teilbez>';
	$output.="</teil>";
	$output .= '</response>';
	
	echo $output;
?>

