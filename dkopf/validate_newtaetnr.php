<?
session_start();
require "../fns_dotazy.php";
dbConnect();

	$tatnr = $_GET['value'];
	$kunde = $_GET['kunde'];
	$teil = $_GET['teil'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');


	mysql_query('set names utf8');
	// najdu cislo operace v seznamu operaci v tabulce dtaetkz-abg
	$sql="select `abg-nr` as tatnr,oper_cz as bez_t,oper_d as bez_d from `dtaetkz-abg` where (`abg-nr`='$tatnr') order by `abg-nr`";
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	$result=mysql_query($sql);
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
		
		$vzkdVorschlag=getZeitVorschlag($kunde,$teil,$tatnr,'vzkd');
		$vzabyVorschlag=getZeitVorschlag($kunde,$teil,$tatnr,'vzaby');;
		
		$nodeElement = $doc->createElement('vzkdvorschlag');
		$nodeText = $doc->createTextNode($vzkdVorschlag);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
		
		$nodeElement = $doc->createElement('vzabyvorschlag');
		$nodeText = $doc->createTextNode($vzabyVorschlag);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);

		// posledni horni roh tam nebude
		
		
		
		
	}
	else
	{
		$nodeElement = $doc->createElement('error');
		$nodeText = $doc->createTextNode("tatnr error");
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);

		$nodeElement = $doc->createElement('tatnr');
		$nodeText = $doc->createTextNode($tatnr);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
		
		$nodeElement = $doc->createElement('sql');
		$nodeText = $doc->createTextNode($sql);
		$nodeElement->appendChild($nodeText);
		$root->appendChild($nodeElement);
		
	}

	$doc->appendChild($root);
	$output = $doc->saveXML();
	
	echo $output;
	
?>

