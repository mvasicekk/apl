<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    /////////////////////////////////////////////////////////////////////////////////////////
	
	
	$sql="select `a-nr` as anr,`auss-typ` as atyp from `auss-art` join `auss_typen` on `auss-art`.`a-nr`=`auss_typen`.`auss-art` where ((`a-nr`='".$value."')) order by `auss-typ` desc limit 1";
	$result=mysql_query($sql);
	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<ausschuss>";
			$output .= '<auss_art>' . $row['anr'] . '</auss_art>';
			$output .= '<auss_typ>' . $row['atyp'] . '</auss_typ>';
			$output.="</ausschuss>";
		}
	}
	else
	{
		$output.="<ausschuss>";
		if($value==0)
		{
			$output .= '<auss_art>0</auss_art>';
			$output .= '<auss_typ>0</auss_typ>';
		}
		else
		{
			$output .= '<auss_art>' . "ERROR-NOAUSSART" . '</auss_art>';
			$output .= '<sql>' . $sql . '</sql>';
		}
		$output.="</ausschuss>";
	}
	
		$output.="<controlid>";
		$output .= $controlid;
		$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>

