<?
session_start();
require "../fns_dotazy.php";
require_once '../db.php';

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
	//$sql="select  PersNr,Name,Vorname,Schicht from dpers where ((`PersNr`='".$value."') and (`austritt` is null))";
        $sql="select  PersNr,Name,Vorname,Schicht from dpers where ((`PersNr`='".$value."') and ((austritt is null) or (DATEDIFF(NOW(),austritt)<60)))";
	$result=mysql_query($sql);
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
                        $apl = AplDB::getInstance();
                        $regeloe = $apl->getRegelOE($value);
			$output.="<pers>";
			$output .= '<persnr>' . $row['PersNr'] . '</persnr>';
			$output .= '<name>' . $row['Vorname']." ".$row['Name'] . '</name>';
			$output .= '<schicht>' . $row['Schicht'] . '</schicht>';
                        $output .= '<regeloe>' . $regeloe . '</regeloe>';
			$output.="</pers>";
		}
	}
	else
	{
		$output.="<pers>";
		$output .= '<persnr>' . "ERROR-NOPERSNR" . '</persnr>';
		$output .= '<sql>' . $sql . '</sql>';
		$output.="</pers>";
	}
	
		$output.="<controlid>";
		$output .= $controlid;
		$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>

