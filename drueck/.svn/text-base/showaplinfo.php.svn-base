<?
session_start();
require "../fns_dotazy.php";
dbConnect();


	$controlid = $_GET['controlid'];
	$value = $_GET['value'];
	$auftragsnr=$_GET['auftragsnr_value'];
	$pal=$_GET['pal_value'];
	$teil=$_GET['teil_value'];
	$mehr_value=$_GET['mehr_value'];

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: nocache');
	header('Content-Type: text/xml');
	
	

    /////////////////////////////////////////////////////////////////////////////////////////
	
	mysql_query('set names utf8');
	
	$sql="select `abg-nr` as abgnr,dtaetkz,name,oper_cz,oper_d from `dtaetkz-abg` order by `abg-nr`";
	$result = mysql_query($sql);
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('response');
	
	if(mysql_affected_rows()>0)
	{
		$nodes=getFieldsArray($result);
		while($row=mysql_fetch_array($result))
		{
			$nodeElement=$doc->createElement("tat");
			foreach($nodes as $sloupec)
			{
				$sloupecJmeno = $doc->createElement($sloupec);
				$sloupecObsah = $doc->createTextNode($row[$sloupec]);
				$sloupecJmeno->appendChild($sloupecObsah);
				$nodeElement->appendChild($sloupecJmeno);
			}
			$root->appendChild($nodeElement);
		}
	}
		
	$doc->appendChild($root);
	$output=$doc->saveXML();
	echo $output;
?>

