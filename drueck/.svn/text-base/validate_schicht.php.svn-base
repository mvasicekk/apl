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
	
	mysql_query('set names utf8');
	
	$sql="select schichtnr from dschicht where ((`schichtnr`='".$value."'))";
	$result=mysql_query($sql);
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<schicht>";
			$output .= '<schichtnr>' . $row['schichtnr'] . '</schichtnr>';
			$output.="</schicht>";
		}
	}
	else
	{
		$output.="<schicht>";
			$output .= '<schichtnr>' . "ERROR-NOSCHICHT" . '</schichtnr>';
			$output .= '<sql>' . $sql . '</sql>';
		$output.="</schicht>";
	}
	
		$output.="<controlid>";
		$output .= $controlid;
		$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>

