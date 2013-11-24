<?
session_start();
require "../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

	
	
	$id_dauftr = $_GET['id_dauftr'];
	$vsechnypalety = $_GET['vsechnypalety'];
	$aplsave = $_GET['aplsave'];
	$preis = $_GET['preis'];
	$vzkd = $_GET['vzkd'];
	$vzaby = $_GET['vzaby'];
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	

	$nodeElement = $doc->createElement("id_dauftr");
	$nodeText = $doc->createTextNode($id_dauftr);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);


	$nodeElement = $doc->createElement("vsechnypalety");
	$nodeText = $doc->createTextNode($vsechnypalety);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	

	$nodeElement = $doc->createElement("aplsave");
	$nodeText = $doc->createTextNode($aplsave);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

	$nodeElement = $doc->createElement("preis");
	$nodeText = $doc->createTextNode($preis);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("vzkd");
	$nodeText = $doc->createTextNode($vzkd);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("vzaby");
	$nodeText = $doc->createTextNode($vzaby);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	// ktery uzivatel chce mazat
	$mazac = get_user_pc();
	$recipient = "jr@abydos.cz,";
	//$recipient.= "hl@abydos.cz";
	
	$dr=getDauftrRowFromId($id_dauftr);
	$auftragsnr=$dr["auftragsnr"];
	$teil=$dr["teil"];
	$pal=$dr["pos-pal-nr"];
	$abgnr=$dr["abgnr"];
	
	if($vsechnypalety=="true"){
		//1.dauftr
		$r=updateDauftrPreisVzKdVzAbyFromAuftrag($auftragsnr,$teil,$pal,$abgnr,$preis,$vzkd,$vzaby,1);
		$updateddauftr = $r;
		//2.drueck
		// ziskam seznam palet k uprave
		$palety = getPalArrayFromDauftrAuftragsnrTeilAbgnr($auftragsnr,$teil,$abgnr);
		$updateddrueck=0;
		foreach ($palety as $paleta){
			$ud=updateDrueckVzKdVzAbyFromAuftrag($vzkd,$vzaby,$paleta,$teil,$abgnr,$auftragsnr);
			$updateddrueck+=$ud;
		}
	}
	else{
		//1.dauftr
		$r=updateDauftrPreisVzKdVzAbyFromAuftrag($auftragsnr,$teil,$pal,$abgnr,$preis,$vzkd,$vzaby,0);
		$updateddauftr = $r;
		//2.drueck
		$r1=updateDrueckVzKdVzAbyFromAuftrag($vzkd,$vzaby,$pal,$teil,$abgnr,$auftragsnr);
		$updateddrueck = $r1;
	}
	
	// a jeste ulozeni do apl
	if($aplsave=="true"){
		
		$udr = updateDpos($teil,$abgnr,$vzkd,$vzaby);
		$nodeElement = $doc->createElement("updateddpos");
		$nodeText = $doc->createTextNode($udr);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
		
	}

//	$nodeElement = $doc->createElement("sql_auftrag");
//	$nodeText = $doc->createTextNode($r['sql']);
//	$nodeElement->appendChild($nodeText);
//	$root->appendChild($nodeElement);

	$nodeElement = $doc->createElement("updateddauftr");
	$nodeText = $doc->createTextNode($updateddauftr);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

	$nodeElement = $doc->createElement("updateddrueck");
	$nodeText = $doc->createTextNode($updateddrueck);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$nodeElement = $doc->createElement("auftragsnr");
	$nodeText = $doc->createTextNode($auftragsnr);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	$subject = "Preis ".$auftragsnr." geaendert";
	$message = "<h3>Daten fur Rechnung <b>$auftragsnr</b> wurden geloescht.</h3>";
	$message .= "<h3>$smazanoRadku<b>Positionen nach drechdeleted kopiert !</b>.</h3>";
	
	$user = get_user_pc();
	$message.= "<br><br>mit freundlichen Gruessen<br>$user";
	if(strlen($chyba)>0)
		$message.= "Error: $chyba";
		
		
	$headers = "From: <apl@abydos.cz>\n";
	$headers = "Content-Type: text/html; charset=UTF-8\n";
		
	//@mail($recipient,$subject,$message,$headers);
	
	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
?>

