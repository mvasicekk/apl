<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

$sql = " select";
$sql.= "     dabmahnung.id,";
$sql.= "     dabmahnung.persnr,";
$sql.= "     CONCAT(dpers.name,' ',dpers.vorname) as name,";
$sql.= "     DATE_FORMAT(dabmahnung.datum,'%d.%m.%Y') as datum,";
$sql.= "     dabmahnung.grund,";
$sql.= "     dabmahnung.bemerk,";
$sql.= "     dabmahnung.betr,";
$sql.= "     dabmahnung.vorschlag,";
$sql.= "     dabmahnung.vorschlag_von,";
$sql.= "     dabmahnung.vorschlag_betrag,";
$sql.= "     dabmahnung.vorschlag_bemerkung,";
$sql.= "     if(dreklamation.rekl_nr is null,'------',dreklamation.rekl_nr) as rekl_nr,";
$sql.= "     if(dabmahnung.betrdat is null,'',DATE_FORMAT(dabmahnung.betrdat,'%d.%m.%Y')) as betrdat";
$sql.= " from dabmahnung";
$sql.= " join dpers on dpers.persnr=dabmahnung.persnr";
$sql.= " left join dreklamation on dreklamation.id=dabmahnung.dreklamation_id";
$sql.= " where";
$sql.= "     dabmahnung.persnr between $persvon and $persbis";
$sql.= "     and dabmahnung.datum between '$von' and '$bis'";
//$sql.= "     and (dabmahnung.vorschlag=0)";
$sql.= " order by dabmahnung.vorschlag,dabmahnung.persnr,dabmahnung.datum";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

$options = array(
    'encoder' => false,
    'rootTag' => 'S184',
    'idColumn' => 'id',
    'rowTag' => 'person',
    'elements' => array(
        'persnr',
        'name',
        'datum',
        'grund',
        'bemerk',
        'betr',
        'betrdat',
	'vorschlag',
	'vorschlag_von',
	'vorschlag_betrag',
	'vorschlag_bemerkung',
	'rekl_nr',
        ),
    );


// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML

/**
 *
 *
 * @var DOMDocument
 */
$domxml = $query2xml->getXML($sql,$options);
//$domxml->encoding="windows-1250";

// pokusy s polem parameters
// rozkouskovat si ho na jednotlive polozky
// a vytvorim pole parametru
// TODO doplnit nejakou moznost zformatovani, napr. datum zadane ve formulari by melo nejak vypadat
// mozna by bylo lepsi resit to AJAXem uz pri zadavani do formulare
// ale uz pri generovani formulare budu muset pridat handlery podle planovaneho obsahu formulare

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


// smazu pouzite pohledy
for($i=0;$i<sizeof($views);$i++)
{
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S184.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
