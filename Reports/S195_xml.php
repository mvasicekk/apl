<?php
	require_once('XML/Query2XML.php');
    require_once('DB.php');
    $db= &DB::connect('mysql://root:nuredv@localhost/apl');
	$db->query("set names utf8");
	
	$query2xml = XML_Query2XML::factory($db);
	 
	//parametry datumvon,datumbis, pole s dilama
	//if(is_array($teile))
	
	//echo "teile=".$teile;
	
	$dily=explode("|",strtoupper($teile));
	
	foreach($dily as $dil)
	{
		$teil_where.=" (teil='$dil') or";
	}
	
	if(strlen($teil_where)>11)
	{
		// odstranit or na konci retezce
		$teil_where = substr($teil_where,0,strlen($teil_where)-2);
	}
	else
		// abych nemel chybny sql dotaz nahradim nevyplnenej retez jednickou = vzdy splneno
		$teil_where=1;
	
	$sql = "SELECT drueck.`PersNr`, CONCAT_WS(' ',`Name`,`Vorname`) as Name,`Teil`, sum(`Stück`+`Auss-Stück`) as stk_plus_auss FROM `drueck`";
	$sql.=" JOIN DPers ON (drueck.Persnr=dpers.persnr)";
	$sql.=" WHERE ((datum between '$datumvon' and '$datumbis') and (taetnr=30)";
	$sql.=" and ($teil_where))";
	$sql.=" group by persnr,`Name`,teil";
	
	//echo $sql;
	
	/*
	$sql="SELECT DATE_FORMAT(DZeit.Datum,'%d.%m.%Y') as Datum, DZeit.PersNr,";
	$sql.=" CONCAT_WS(',',DPers.Name,DPers.Vorname) as Name,FORMAT(DZeit.Stunden,1) as Stunden, ";
	$sql.=" DZeit.Schicht, DZeit.tat, DZeit.transport, DZeit.essen, DATE_FORMAT(DZeit.anw_von,'%H:%i') as von, DATE_FORMAT(DZeit.anw_bis,'%H:%i') bis,";
	$sql.=" FORMAT(DZeit.pause1,1) as pause1, FORMAT(DZeit.pause2,1) as pause2";
	$sql.=" FROM DPers JOIN DZeit ON DPers.PersNr = DZeit.PersNr";
	$sql.=" WHERE (((DZeit.Datum)='$datum'))";
	$sql.=" ORDER BY DZeit.PersNr, DPers.Name, DZeit.Schicht";
	*/
	
	$options = array(
						'rootTag'=>'S195',
						'rowTag'=>'row',
						'idColumn'=>'PersNr',
						'elements'=>array(
						'PersNr',
						'Name',
						'Teil',
						'stk_plus_auss')
					);
					
	$domxml = $query2xml->getXML($sql,$options);
	//$domxml->encoding="windows-1250";
	
	// pokusy s polem parameters
// rozkouskovat si ho na jednotlive polozky
// a vytvorim pole parametru

foreach($parameters as $var=>$value)
{
	
	// pokud nazev parametru obsahuje _label, budu hledat hodnotu parametru
	if(strpos($var,"_label"))
	{
		$p[$value]=$last_value;
	}
	$last_value=$value;
	//$promenne.=$var."=".$value."&";
}

// v promenne p bych mel mit seznam parametru, pridam ho do XML jako node do domxml

$element=$domxml->createElement("parameters");
$domxml->appendChild($element);
$i=1;
foreach($p as $var=>$value)
{
	$poradinode=$domxml->createElement("N".$i);
	$labelnode=$domxml->createElement("label",$var);
	$valuenode=$domxml->createElement("value",$value);
	$element->appendChild($poradinode);
	$poradinode->appendChild($labelnode);
	$poradinode->appendChild($valuenode);
	$i++;
}

//$domxml->save("S195.xml");
?>
