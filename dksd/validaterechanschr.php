<?
session_start();
require "../fns_dotazy.php";
dbConnect();

	$value = $_GET['kunde'];
	$id = $_GET['id'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


    // cislo zkaznika musi existovat v databazi zakazniku 
	/////////////////////////////////////////////////////////////////////////////////////////
	

	mysql_query("set names utf8");
	$sql="select name1,kunde,name2 from dksd where (kunde='".$value."')";
	$result=mysql_query($sql);
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	//$nodes = array("kunde","name1","name2");
	// pokud mi dotaz vrati nejake zaznamy, tak je projdu
	if(mysql_affected_rows()>0)
	{
		// vytvorim pole s nazvy podle vybranych sloupcu z dotazu
		$nodes=getFieldsArray($result);
		while ($row = mysql_fetch_array($result))
		{
			foreach($nodes as $node)
			{
				$nodeElement = $doc->createElement($node);
				$nodeText = $doc->createTextNode($row[$node]);
				$nodeElement->appendChild($nodeText);
				$root->appendChild($nodeElement);
			}
		}
	}
	else
	{
		$nodeElement = $doc->createElement('error');
		$nodeText = $doc->createTextNode("Kundenummer Fehler !!!");
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
	}

	$nodeElement = $doc->createElement('id');
	$nodeText = $doc->createTextNode($id);
	$nodeElement->appendChild($nodeText);
	$root->appendChild($nodeElement);
		
	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
?>