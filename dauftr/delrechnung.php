<?
session_start();
require "../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

	$auftragsnr = $_GET['auftragsnr'];
	$run = $_GET['run'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	// vratim cislo behu
	$nodeElement = $doc->createElement("run");
	$nodeText = $doc->createTextNode($run);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

	// vratim cislo faktury
	$nodeElement = $doc->createElement("auftragsnr");
	$nodeText = $doc->createTextNode($auftragsnr);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	// pokud je run=1 mam prvni beh, testnu, zda uz ma export hotovou fakturu
	if($run==1)
	{
		$hasrechnung = has_rechnung($auftragsnr);
		$nodeElement = $doc->createElement("hasrechnung");
		$nodeText = $doc->createTextNode($hasrechnung);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
	}
	else
	{
		// ktery uzivatel chce mazat
		$mazac = get_user_pc();
		// prekopirovat starou fakturu do zalozni tabulky
		$chyba=backupRechnung($auftragsnr,$mazac);
        // zjistim lieferdatum a rechnungssatum pro mazanou fakturu
        $datumArray = getRechnungDatums($auftragsnr);

		// smazat starou fakturu
		$smazanoRadku=deleteRechnung($auftragsnr);
		// poslat informacni email
		
//		$recipient = "jr@abydos.cz,";
		$recipient.= "hl@abydos.cz,";
		$recipient.= "in@abydos.cz,";
		$recipient.= "jk@abydos.cz";
                //$recipient.= "sz@abydos.cz,";

		/*
		// posle mail i odesilateli
		$uzivatel = get_user();
		if(strlen($uzivatel)>0)
		{
		 $uzivatelemail = $uzivatel."@abydos.cz";
		 $recipient.= ",$uzivatelemail";
		}
		*/
		
		
		$subject = "Rechnung ".$auftragsnr." wurde geloescht";
		$message = "<h3>Daten fur Rechnung <b>$auftragsnr</b> wurden geloescht.</h3>";
		$message .= "<h3>$smazanoRadku<b>Positionen nach drechdeleted kopiert !</b>.</h3>";
        $message.="<h3>Rechnungsdatum: ".$datumArray['fertig']." Auslieferdatum: ".$datumArray['ausliefer_datum'];
		
		$user = get_user_pc();
		$message.= "<br><br>mit freundlichen Gruessen<br>$user";
		if(strlen($chyba)>0)
			$message.= "Error: $chyba";
		
		
		$headers = "From: <apl@abydos.cz>\n";
		$headers = "Content-Type: text/html; charset=UTF-8\n";
		
		@mail($recipient,$subject,$message,$headers);
	}
	
	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
?>

