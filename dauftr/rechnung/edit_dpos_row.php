<?
require_once '../../security.php';
require "../fns_dotazy.php";
dbConnect();

	$dpos_id = $_GET['dpos_id'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


	// vyberu vsechny sklady ze seznamu skladu
	$sql="select Lager,LagerBeschreibung from dlager order by Lager";
	$result=mysql_query($sql);
	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<lager>";
			$output .= '<lagernr>' . $row['Lager'] . '</lagernr>';
			$output .= '<lagerbeschreibung>' . $row['LagerBeschreibung'] . '</lagerbeschreibung>';
			$output.="</lager>";
		}
	}

	// vyberu vsechny mozne cinnosti
	$sql="select `abg-nr` as taetnr,Name as taetnrbeschreibung from `dtaetkz-abg` order by `abg-nr`";
	$result=mysql_query($sql);
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$output.="<taetigkeit>";
			$output .= '<taetnr>' . $row['taetnr'] . '</taetnr>';
			$output .= '<taetnrbeschreibung>' . $row['taetnrbeschreibung'] . '</taetnrbeschreibung>';
			$output.="</taetigkeit>";
		}
	}

	// pridam dpos_id, abych nasel spravny radek v tabulce
	$output.="<dpos_id>";
	$output.=$dpos_id;
	$output.="</dpos_id>";
	
	$output .= '</response>';
	
	echo $output;
?>

