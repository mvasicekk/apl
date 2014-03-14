<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";




// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;
$pcip=get_pc_ip();

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");
// vytahnu si parametry z XML souboru

$domxml = new DOMDocument("1.0");
$domxml->encoding="UTF-8";


// pokusy s polem parameters
// rozkouskovat si ho na jednotlive polozky
// a vytvorim pole parametru
// TODO doplnit nejakou moznost zformatovani, napr. datum zadane ve formulari by melo nejak vypadat
// mozna by bylo lepsi resit to AJAXem uz pri zadavani do formulare
// ale uz pri generovani formulare budu muset pridat handlery podle planovaneho obsahu formulare
// tady uz to nepujde


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

$root=$domxml->createElement("S390");
$domxml->appendChild($root);

$element=$domxml->createElement("parameters");
$parametry=$domxml->firstChild;
$parametry->appendChild($element);
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

	$poradinode=$domxml->createElement("N".$i);
	$labelnode=$domxml->createElement("label","von");
	$valuenode=$domxml->createElement("value",$stampVon);
	$element->appendChild($poradinode);
	$poradinode->appendChild($labelnode);
	$poradinode->appendChild($valuenode);
	$i++;

	$poradinode=$domxml->createElement("N".$i);
	$labelnode=$domxml->createElement("label","bis");
	$valuenode=$domxml->createElement("value",$stampBis);
	$element->appendChild($poradinode);
	$poradinode->appendChild($labelnode);
	$poradinode->appendChild($valuenode);
	$i++;
	
	
$node=$domxml->createElement("von");
$nodeText=$domxml->createTextNode($stampVon);
$node->appendChild($nodeText);
$root->appendChild($node);

$node=$domxml->createElement("bis");
$nodeText=$domxml->createTextNode($stampBis);
$node->appendChild($nodeText);
$root->appendChild($node);


$lagerArray = getLagerArray();
foreach($lagerArray as $key=>$value)
{
	$lagernode=$domxml->createElement("lager");

	$lagerENode = $domxml->createElement("lagerkz");
	$lagerETextNode = $domxml->createTextNode($key);
	$lagerENode->appendChild($lagerETextNode);
	$lagernode->appendChild($lagerENode);

	$lagerENode = $domxml->createElement("beschreibung");
	$lagerETextNode = $domxml->createTextNode($value);
	$lagerENode->appendChild($lagerETextNode);
	$lagernode->appendChild($lagerENode);
	
	$lagerENode = $domxml->createElement("inventurstk");
	$lagerETextNode = $domxml->createTextNode(lagerInventurStk($dil,$key,$stampBis));
	$lagerENode->appendChild($lagerETextNode);
	$lagernode->appendChild($lagerENode);
	
	$lagerENode = $domxml->createElement("gutplus");
	$lagerETextNode = $domxml->createTextNode(getLagerGutPlusTeilDatum($dil,$key,$stampVon,$stampBis));
	$lagerENode->appendChild($lagerETextNode);
	$lagernode->appendChild($lagerENode);
	
	$lagerENode = $domxml->createElement("gutminus");
	$lagerETextNode = $domxml->createTextNode(getLagerGutMinusTeilDatum($dil,$key,$stampVon,$stampBis));
	$lagerENode->appendChild($lagerETextNode);
	$lagernode->appendChild($lagerENode);
	
	$lagerENode = $domxml->createElement("aussplus");
	$lagerETextNode = $domxml->createTextNode(getLagerAussPlusTeilDatum($dil,$key,$stampVon,$stampBis));
	$lagerENode->appendChild($lagerETextNode);
	$lagernode->appendChild($lagerENode);
	
	$lagerENode = $domxml->createElement("aussminus");
	$lagerETextNode = $domxml->createTextNode(getLagerAussMinusTeilDatum($dil,$key,$stampVon,$stampBis));
	$lagerENode->appendChild($lagerETextNode);
	$lagernode->appendChild($lagerENode);
	
	$root->appendChild($lagernode);
}



$db->disconnect();
dbConnect();

//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S390.xml");

?>
