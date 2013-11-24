<?
session_start();
require "../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

	$auftragsnr = $_GET['auftragsnr'];
	$level=$_GET['level'];
	$id_dauftr = $_GET['id_dauftr'];
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	// vratim cislo zakazky
	$nodeElement = $doc->createElement("auftragsnr");
	$nodeText = $doc->createTextNode($auftragsnr);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

	// vratim level
	$nodeElement = $doc->createElement("level");
	$nodeText = $doc->createTextNode($level);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	// vratim id_dauftr
	$nodeElement = $doc->createElement("id_dauftr");
	$nodeText = $doc->createTextNode($id_dauftr);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

	//podle id_dauftr zjistim kolika radku v dauftr a v drueck se to tyka
	$dauftrRow = getDauftrRowFromId($id_dauftr);
	$pocetPozicDauftrArray = getPalArrayFromDauftrAuftragsnrTeilAbgnr($dauftrRow['auftragsnr'],$dauftrRow['teil'],$dauftrRow['abgnr']);
	$pocetPozicDrueckArray = getPalArrayFromDrueckAuftragsnrTeilAbgnr($dauftrRow['auftragsnr'],$dauftrRow['teil'],$dauftrRow['abgnr']);
	$pocetPozicDauftr=sizeof($pocetPozicDauftrArray);
	$pocetPozicDrueck=sizeof($pocetPozicDrueckArray);
	
	
	$nodeElement = $doc->createElement("dauftrpozic");
	$nodeText = $doc->createTextNode($pocetPozicDauftr);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	$nodeElement = $doc->createElement("drueckpozic");
	$nodeText = $doc->createTextNode($pocetPozicDrueck);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	
	
	// ktery uzivatel chce mazat
	$mazac = get_user_pc();
	$recipient = "jr@abydos.cz,";
	//$recipient.= "hl@abydos.cz";
	
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

