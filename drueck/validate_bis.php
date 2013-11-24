<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];
	$von = $_GET['von'];
	$datum = $_GET['datum'];
	$persnr = $_GET['persnr'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    /////////////////////////////////////////////////////////////////////////////////////////

	//$datum = '30.12.1899';
		
	$db_von=make_DB_datetime($von,$datum);
	$db_datum=make_DB_datum($datum);
	$db_bis=make_DB_datetime($value,$datum);

	
	
	// puvodni
	//$sql = "select `verb-bis` as bis from drueck where ((persnr='".$persnr."') and (datum='".$db_datum."') and (`verb-bis`='".$db_von."'))";
	
	// novy
	$sql = "select `verb-bis` as bis from drueck where ((persnr='".$persnr."') and (datum='".$db_datum."') and (DATE_FORMAT(`verb-bis`,'%H:%i')='".$von."'))";
	
	mysql_query('set names utf8');
	$result=mysql_query($sql);
	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	
	$pocetZaznamu=mysql_affected_rows();
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(($pocetZaznamu>0)||((substr($db_von,11,5)=='00:00')&&(substr($db_bis,11,5)=='00:00')))
	{
		if($pocetZaznamu>0)
		{
			// 	pro novy von zeit jsem nalezl odpovidajici bis zeit
			while ($row = mysql_fetch_array($result))
			{
				$output.="<biszeit>";
				$output.= '<bis>' . $row['bis'] . '</bis>';
				$output.="</biszeit>";
			}
		}
		else
		{
			// oba casy von i bis jsou nulove
				$output.="<biszeit>";
				$output.= '<bis>' . $row['bis'] . '</bis>';
				$output.="</biszeit>";
		}
	}
	else
	{
		// pro novy von zeit jsem nenasel stary bis zeit
		// pro dany datum najdu posledni zadany rozsah casu
		$sql = "select max(`verb-von`) as von,max(`verb-bis`) as bis from drueck where ((persnr='".$persnr."') and (datum='".$db_datum."'))";
		$result=mysql_query($sql);
		$row = mysql_fetch_array($result);
		if((mysql_affected_rows()>0)&&(strlen($row['von'])>0))
		{
			// pro dany datum uz mam nejake zaznamy
			$output.="<biszeit>";
			$output.= '<bis>ERROR-NOBIS</bis>';
			$output.= '<errordescription>Casy nenavazuji</errordescription>';
			$output.="</biszeit>";
			$output.="<lastvonbis>";
			$output.= '<lbis>' . $row['bis'] . '</lbis>';
			$output.= '<lvon>' . $row['von'] . '</lvon>';
			$output.= '<strlenlvon>' . strlen($row['von']) . '</strlenlvon>';
			$output.="</lastvonbis>";
		}
		else
		{
			// pro dane datum jeste nemam zadne zaznamy
			$output.="<biszeit>";
			$output.= '<bis>ERROR-NOLEIST</bis>';
			$output.= '<errordescription>Heute noch keine Leistung ? / dnes jeste zadny vykon ?</errordescription>';
			$output.= '<sql>'.$sql.'</sql>';
			$output.="</biszeit>";
		}
	}
	
	$output.="<controlid>";
	$output .= $controlid;
	$output.="</controlid>";
	
	$output .= '</response>';
	
	echo $output;
?>

