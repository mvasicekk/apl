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


    // cislo zkaznika musi existovat v databazi zakazniku 
	/////////////////////////////////////////////////////////////////////////////////////////
	
	
	$sql="select Name1,Kunde from dksd where (Kunde='".$value."')";
	$result=mysql_query($sql);
	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<kunde>";
			$output .= '<kundenr>' . $row['Kunde'] . '</kundenr>';
			$output .= '<kundename1>' . $row['Name1'] . '</kundename1>';
			$output.="</kunde>";
		}
	}
	else
	{
		$output.="<kunde>";
		$output .= '<kundenr>' . "ERROR-NOKDNR" . '</kundenr>';
		$output.="</kunde>";
	}
	
	$output .= '</response>';
	
	echo $output;
?>

