<?
session_start();
require "../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

	$value = $_GET['value'];
	$id = $_GET['id'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	// zjistim jmeno zakaznika
	$kndname = getKndName($value);
	if(strlen(trim($kndname))>1){
		// zakaznika jsem nasel, budu pokracovat
	}
	else
	{
		// zakaznika jsem nenasel, misto jmena zakaznika ulozim ERROR
		$kndname="ERROR";
	}
	
	$nodeElement = $doc->createElement("kndname");
	$nodeText = $doc->createTextNode($kndname);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
	
	// vratim id elementu
	$nodeElement = $doc->createElement("id");
	$nodeText = $doc->createTextNode($id);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);

		$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
?>

