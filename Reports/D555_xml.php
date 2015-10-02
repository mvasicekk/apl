<?php
require_once '../security.php';
//session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu


$pcip=get_pc_ip();


$sql.=" select * from dma where imanr='$imanr'";
$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$options = array(
    'encoder' => false,
    'rootTag' => 'D555',
    'idColumn' => 'imanr',
    'rowTag' => 'ima',
    'elements' => array(
	'id',
	'imanr',
	'emanr',
	'teil',
	'auftragsnrarray',
	'ema_auftragsarray',
	'ema_auftragsarray_genehmigt',
	'ima_auftragsnrarray_genehmigt',
	'palarray',
	'ema_palarray',
	'ema_palarray_genehmigt',
	'ima_palarray_genehmigt',
	'tatundzeitarray',
	'ema_tatundzeitarray',
	'ema_tatundzeitarray_genehmigt',
	'ima_tatundzeitarray_genehmigt',
	'bemerkung',
	'ema_anlagen_array',
	'imavon',
	'ima_genehmigt',
	'ema_genehmigt',
	'ima_genehmigt_user',
	'ima_genehmigt_stamp',
	'ema_genehmigt_stamp',
	'ima_genehmigt_bemerkung',
	'ema_genehmigt_bemerkung',
	'ema_antrag_am',
	'ema_antrag_vom',
	'ema_antrag_text',
	'ema_dauftrid_array',
	'ema_dauftrid_array_genehmigt',
	'stamp',
    ),
);



// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);

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
//

$element=$domxml->createElement("parameters");
$parametry=$domxml->firstChild;
$parametry->appendChild($element);
$i=1;

if(is_array($p)){
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
    
}


//header('Content-Type: application/xml');
//echo $proc->transformToXML($domxml);



// smazu pouzite pohledy
for($i=0;$i<sizeof($views);$i++)
{

	// pohledy se smazou podle jejich poctu definovaneho polem views
	
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


//$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("D555.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
