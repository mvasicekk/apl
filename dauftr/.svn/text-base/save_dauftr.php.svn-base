<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	// TODO: dodelat validaci parametru
	
	$auftragsnr = trim($_GET['auftragsnr']);
	$bestellnr=trim($_GET['bestellnr']);
	$aufdat=trim($_GET['aufdat']);
	$ex_datum_soll=trim($_GET['ex_datum_soll']);

	$datum=$aufdat;
	$datum=make_DB_datum($datum);
	///$datumRoz = explode(".",$datum); // Rozøežeme datum na jednotlivé údaje
	//$datum = $datumRoz[2]."-".$datumRoz[1]."-".$datumRoz[0]; // Opìt ho spojíme
	$aufdat=$datum;

	if(strlen($ex_datum_soll)>0)
	{
		$datum_cast=substr($ex_datum_soll,0,strpos($ex_datum_soll,' '));
		$datum=$datum_cast;
		$datumRoz = explode(".",$datum); // Rozøežeme datum na jednotlivé údaje
		$datum = $datumRoz[2]."-".$datumRoz[1]."-".$datumRoz[0]; // Opìt ho spojíme
		$datum_cast=$datum;
		
		$time_cast=substr($ex_datum_soll,strpos($ex_datum_soll,' '));
		
		$ex_datum_soll=$datum_cast." ".$time_cast;
	}
 
	//$vonHod = substr($von,0,2); // rozøežeme pøíchod na údaje
	//$vonMin = substr($von,3,2); // rozøežeme pøíchod na údaje
 
	//$bisHod = substr($bis,0,2); // rozøežeme odchod na údaje
	//$bisMin = substr($bis,3,2); // rozøežeme odchod na údaje
 
	//$von = date("y-m-d H:i:s",mktime($vonHod, $vonMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2])); // sestavíme nový pøíchod i s datumem
	//$bis = date("y-m-d H:i:s",mktime($bisHod, $bisMin, 0, $datumRoz[1], $datumRoz[0], $datumRoz[2])); // sestavíme nový odchod i s datumem
 
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // kontrola hodnot parametru
	/////////////////////////////////////////////////////////////////////////////////////
	
	// limit 1 je pro jistotu, kdyby se pokazilo kriterium ve where
	
	$sql="update daufkopf set `bestellnr`='".$bestellnr."'";
	$sql.=",`Aufdat`='".$aufdat."'";

	$timestamp=false;
	if(strlen($ex_datum_soll)>0)
		$timestamp=strtotime($ex_datum_soll);

	if($timestamp)
		$sql.=", `ex_datum_soll`='".$ex_datum_soll."'";

	$sql.=" where (AuftragsNr='".$auftragsnr."') limit 1";
	
	$result=mysql_query($sql);
	$affected_rows=mysql_affected_rows();
	$mysql_error=mysql_error();
	$output = '<?xml version="1.0" encoding="cp-1250" standalone="yes"?>';
	$output .= '<response>';
	$output .= '<sql>';
	$output .= $sql;
	$output .= '</sql>';
	$output .= '<affectedrows>';
	$output .= $affected_rows;
	$output .= '</affectedrows>';
	$output .= '<mysqlerror>';
	$output .= $mysql_error;
	$output .= '</mysqlerror>';
	$output .= "<auftragsnr>$auftragsnr</auftragsnr>";	
	$output .= '</response>';
	
	echo $output;
	
?>

