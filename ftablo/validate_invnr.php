<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];
	
	$invnr=$value;

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    /////////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query('set names utf8');
	
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
		
	
	// 1. zjistim existenci inventarniho cisla
	$sql = "select invnummer,anlage_beschreibung from dreparatur_geraete join dreparatur_anlagen using(anlage_id) where ((`invnummer`='$invnr'))";
	
	$resTeil = mysql_query($sql);
	if(mysql_affected_rows()>0)
	{
		// nasel jsem
		$row=mysql_fetch_array($resTeil);
		$output .= "<invnr>".$row['invnr']."</invnr>";
		$output .= "<beschreibung>".$row['anlage_beschreibung']."</beschreibung>";
	}
	else
	{
		// nenasel
		// jeste povolim nulu v amnr
		if($invnr==0)
		{
			$output .= "<invnr>$invnr</invnr>";
			$output .= "<beschreibung>nic</beschreibung>";
		}
		else
		{
			$output.="<error>";
			$output .= "<errordescription>ERROR-NOINVNR</errordescription>";
			//$output .= "<sql>$sql</sql>";
			$output.="</error>";
		}
	}
	
		
	$output.="<controlid>";
	$output .= $controlid;
	$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>

