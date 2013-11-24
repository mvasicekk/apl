<?
session_start();
require "../../fns_dotazy.php";
dbConnect();
mysql_query('set names utf8');

	$vom = $_GET['vom'];
	$an = $_GET['an'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');

	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
    // vom musi existovat v tabulce dkndumrech 
	/////////////////////////////////////////////////////////////////////////////////////////
	$sql="select an from dkndumrech where ((vom='".$vom."') and (an='$an'))";
	$result=mysql_query($sql);
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		$nodeElement = $doc->createElement("an");
		$nodeText = $doc->createTextNode($an);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
	}
	else
	{
		$nodeElement = $doc->createElement("an");
		$nodeText = $doc->createTextNode("ERROR");
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
	}
	
	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
?>

