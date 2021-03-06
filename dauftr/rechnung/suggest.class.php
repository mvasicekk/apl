<?
	require_once "../../fns_dotazy.php";
	//dbConnect();
	class Suggest
	{
	
		public function getSuggestions($keyword)
		{
			if(strlen($keyword)>0)
			{
				$sql = "select auftragsnr,DATE_FORMAT(datum,'%Y-%m-%d') as datum,vom,an,origauftrag from drechneu where ((auftragsnr like '".$keyword."%')) group by auftragsnr limit 100";
			}
			else
			{
				$sql="select auftragsnr from drechneu where (auftragsnr='abcd')";
			}

			
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: nocache');
			header('Content-Type: text/xml');
	
			$doc = new DOMDocument('1.0');
			$root = $doc->createElement('response');
	
			dbConnect();
			$result=mysql_query($sql);
			
			// pokud mi dotaz vrati nejake zaznamy, tak je projdu
			if(mysql_affected_rows()>0){
				
				while ($row = mysql_fetch_array($result))
				{

					$rechnungElement = $doc->createElement("rechnung");
					$root->appendChild($rechnungElement);
					
					$nodeElement = $doc->createElement("auftragsnr");
					$nodeText = $doc->createTextNode($row['auftragsnr']);
					$nodeElement->appendChild($nodeText);
					$rechnungElement->appendChild($nodeElement);
					
					$nodeElement = $doc->createElement("datum");
					$nodeText = $doc->createTextNode($row['datum']);
					$nodeElement->appendChild($nodeText);
					$rechnungElement->appendChild($nodeElement);
					
					$nodeElement = $doc->createElement("vom");
					$nodeText = $doc->createTextNode($row['vom']);
					$nodeElement->appendChild($nodeText);
					$rechnungElement->appendChild($nodeElement);
					
					$nodeElement = $doc->createElement("an");
					$nodeText = $doc->createTextNode($row['an']);
					$nodeElement->appendChild($nodeText);
					$rechnungElement->appendChild($nodeElement);
					
					$nodeElement = $doc->createElement("origauftrag");
					$nodeText = $doc->createTextNode($row['origauftrag']);
					$nodeElement->appendChild($nodeText);
					$rechnungElement->appendChild($nodeElement);
					
				}
			}
			$doc->appendChild($root);
			$output = $doc->saveXML();
			echo $output;
		}
	}
