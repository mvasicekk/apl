<?
session_start();
require "../../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

	$rechnung = $_GET['rechnung'];
	$rechnungsdatum = $_GET['rechdatum'];
	$lieferdatum = $_GET['liefdatum'];
	$vom = $_GET['vom'];
	$an = $_GET['an'];
	$deleteold = $_GET['delold'];


	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	

	// vratim cislo faktury
	$nodeElement = $doc->createElement("rechnung");
	$nodeText = $doc->createTextNode($rechnung);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

	$nodeElement = $doc->createElement("rechnungsdatum");
	$nodeText = $doc->createTextNode($rechnungsdatum);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("lieferdatum");
	$nodeText = $doc->createTextNode($lieferdatum);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("vom");
	$nodeText = $doc->createTextNode($vom);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("an");
	$nodeText = $doc->createTextNode($an);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$oldRechnungExists = 0;
	
	// test na existenci faktury
	if($deleteold==0)
	{
		$sql = "select origauftrag from drechneu where ((origauftrag='$rechnung') and (vom='$vom') and (an='$an'))";
		$res = mysql_query($sql);
		if(mysql_affected_rows()>0)
		{
			$oldRechnungExists = 1;
			$nodeElement = $doc->createElement("oldexists");
			$nodeText = $doc->createTextNode("1");
			$nodeElement->appendChild($nodeText);
			$root->appendChild($nodeElement);
		}
	}
	
	// mam smazat starou fakturu
	if(($deleteold>0) || ($oldRechnungExists==0))
	{
		$sql="delete from drechneu where ((origauftrag='$rechnung') and (vom='$vom') and (an='$an'))";
		mysql_query($sql);
		
		// zjistit menu, ve ktere bude nova faktura
		$mena = umrechnungGetWahr($vom,$an);
		
		$nodeElement = $doc->createElement("mena");
		$nodeText = $doc->createTextNode($mena);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
		
		// zjistim minutovou sazbu u puvodni faktury
		// budu zjistovat ze vzdalene tabulky $host,$user,$pass,$db
		$minpreisOriginal = umrechnungGetMinpreisOriginal(REMOTE_HOST,REMOTE_USER,REMOTE_PASS,REMOTE_DB,$rechnung);
		$nodeElement = $doc->createElement("minpreisoriginal");
		$nodeText = $doc->createTextNode($minpreisOriginal);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
		
		// zjistim novou minutovou sazbu z mistni tabulky dkndumrech
		$minpreisNeu = umrechnungGetMinpreisNeu($vom,$an);
		$nodeElement = $doc->createElement("minpreisneu");
		$nodeText = $doc->createTextNode($minpreisNeu);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);

		// zjistim posledni cislo faktury
		$letzterechnung = umrechnungGetLetzteRechnung($vom,$an);
		$nodeElement = $doc->createElement("letzterechnung");
		$nodeText = $doc->createTextNode($letzterechnung);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);

		// zjistim ceny F,Z a S
		$sonstPreiseArray = umrechnungGetFrachtZollSonst($vom,$an);
		
		// projedu vsechny radky z tabulky drechbew
		$drechbewRows = umrechnungGetDrechbewRows(REMOTE_HOST,REMOTE_USER,REMOTE_PASS,REMOTE_DB,$rechnung);
		while($drechbewrow=mysql_fetch_array($drechbewRows))
		{
			insertToDrechNeu($drechbewrow,$vom,$an,$mena,$minpreisOriginal,$minpreisNeu,$letzterechnung,make_DB_datum($rechnungsdatum),make_DB_datum(($lieferdatum)),$sonstPreiseArray);
		}
		// zvysim cislo posledni faktury u dvojice zakazniku
		incrementRechnungNummer($vom,$an);
		
	}
	
	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
?>

