<?
session_start();
require "../../fns_dotazy.php";
dbConnect();

	$vzkd=$_GET['vzkd'];
	$kunde=$_GET['kunde'];
	$auftragsnr=$_GET['auftragsnr'];
	$preisid=$_GET['preisid'];


	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');

	$output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$output .= '<response>';
	

    /////////////////////////////////////////////////////////////////////////////////////////
	// 1. zjistim si cenu za minutu pro aktualni auftrag
	mysql_query('set names utf8');
	$minpreis=get_minpreis_von_auftrag($auftragsnr);
	
	$preis = $minpreis * $vzkd;
	
	// zjistim na kolik mist pro daneho zakaznika zakrouhlit cenu
	$runden = getRundenFromKunde($kunde);
	
	$preis = round($preis,$runden);
	
	$output.="<vzkd>$vzkd</vzkd>";
	$output.="<preis>$preis</preis>";
	$output.="<minpreis>$minpreis</minpreis>";
	$output.="<runden>$runden</runden>";
	$output.="<kunde>$kunde</kunde>";
	$output.="<auftragsnr>$auftragsnr</auftragsnr>";
	$output.="<preisid>$preisid</preisid>";

	$output .= '</response>';
	
	echo $output;
?>
