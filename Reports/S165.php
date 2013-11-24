<?php
	require_once('XML/Query2XML.php');
    require_once('DB.php');
    $db= &DB::connect('mysql://root:nuredv@localhost/apl');
	$db->query("set character set cp1250");
	
	$query2xml = XML_Query2XML::factory($db);
	 
	$sql="SELECT DATE_FORMAT(DZeit.Datum,'%d.%m.%Y') as Datum, DZeit.PersNr,";
	$sql.=" CONCAT_WS(',',DPers.Name,DPers.Vorname) as Name,FORMAT(DZeit.Stunden,1) as Stunden, ";
	$sql.=" DZeit.Schicht, DZeit.tat, DZeit.transport, DZeit.essen, DATE_FORMAT(DZeit.anw_von,'%H:%i') as von, DATE_FORMAT(DZeit.anw_bis,'%H:%i') bis,";
	$sql.=" FORMAT(DZeit.pause1,1) as pause1, FORMAT(DZeit.pause2,1) as pause2";
	$sql.=" FROM DPers JOIN DZeit ON DPers.PersNr = DZeit.PersNr";
	$sql.=" WHERE (((DZeit.Datum)='2006-10-23'))";
	$sql.=" ORDER BY DZeit.PersNr, DPers.Name, DZeit.Schicht";

	$options = array(
						'rootTag'=>'S165',
						'rowTag'=>'row',
						'idColumn'=>'PersNr',
						'elements'=>array(
						'Datum',
						'PersNr',
						'Name',
						'Stunden'=>"!return number_format(\$record['Stunden'],1,',',' ');",
						'Schicht',
						'tat',
						'transport',
						'essen',
						'von',
						'bis',
						'pause1'=>"!return number_format(\$record['pause1'],1,',',' ');",
						'pause2'=>"!return number_format(\$record['pause2'],1,',',' ');")
					);
					
	$domxml = $query2xml->getXML($sql,$options);
	
	$domxml->encoding="windows-1250";
	$domxsl = new DOMDocument;
	$domxsl->load("S165.xsl");
	
	$proc = new XSLTProcessor;
	
	$proc->importStyleSheet($domxsl);
	
	//header('Content-Type: application/xml');
	//echo "<pre>".$domxml->saveXML()."</pre>";
    echo $proc->transformToXML($domxml);

	//print $domxml->saveXML();
	$domxml->save("S165.xml");
?>
