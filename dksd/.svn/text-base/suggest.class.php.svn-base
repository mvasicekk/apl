<?
	require_once "../fns_dotazy.php";
	//dbConnect();
	class Suggest
	{
	
		public function getSuggestions($keyword)
		{
			if(strlen($keyword)>0)
			{
				$sql = "select kunde,name1,name2,Straße as strasse,plz,ort,tel,preismin from dksd where (kunde like '$keyword%') order by kunde";
			}
			else
			{
				
				// TODO: je to prasecina, ale funkcni 
				$sql="select kunde from dksd where (kunde='98765')";
			}

			dbConnect();
			mysql_query("set names utf8");
			$result=mysql_query($sql);
			$doc = new DOMDocument('1.0');
			$root = $doc->createElement('response');
			$nodes = array("kunde","name1","name2","strasse","plz","ort","tel","preismin");
			// pokud mi dotaz vrati nejake zaznamy, tak je projdu
			if(mysql_affected_rows()>0)
				while ($row = mysql_fetch_array($result))
				{
					$kdElement = $doc->createElement('kd');
					foreach($nodes as $node)
					{
						$nodeElement = $doc->createElement($node);
						$nodeText = $doc->createTextNode($row[$node]);
						$nodeElement->appendChild($nodeText);
						$kdElement->appendChild($nodeElement);
					}
					$root->appendChild($kdElement);
				}
			// add the final closing tag
			$doc->appendChild($root);
			$output = $doc->saveXML();
			// return the results
			return $output;
		}
	}
