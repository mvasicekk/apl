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

$pcip=get_pc_ip();

$sql.= " select ";
$sql.= "     adresy.adresy_id,";
$sql.= "     adresy.suchbegriff,";
$sql.= "     adresy.kdnr,";
$sql.= "     adresy.code,";
$sql.= "     adresy.firma,";
$sql.= "     adresy.ansprechpartner,";
$sql.= "     adresy.`name`,";
$sql.= "     adresy.vorname,";
$sql.= "     CONCAT(adresy.`name`,' ',adresy.vorname) as fullname,";
$sql.= "     adresy.funktion,";
$sql.= "     if(adresy.geboren is not null,DATE_FORMAT(adresy.geboren,'%y-%m-%d'),'') as geboren,";
$sql.= "     adresy.telefon,";
$sql.= "     adresy.telefonprivat,";
$sql.= "     adresy.fax,";
$sql.= "     adresy.handy,";
$sql.= "     adresy.strasse,";
$sql.= "     adresy.ort,";
$sql.= "     adresy.plz,";
$sql.= "     CONCAT(adresy.plz,' ',adresy.ort,',',adresy.strasse) as adr,";
$sql.= "     adresy.email,";
$sql.= "     adresy.sonstiges";
$sql.= " from adresy";
if($bWhereKategorien)
$sql.= " join adresyinkategorie on adresyinkategorie.adresy_id=adresy.adresy_id";    
$sql.= " where(";
$sql.= " (deleted=0) ";
if($bWhereKategorien)
$sql.= " and ($whereKategorien)";
$sql.= " )";
$sql.= " order by";
$sql.= "     adresy.firma,adresy.`name`";
    
//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
    'encoder' => false,
    'rootTag' => 'S103',
    'idColumn' => 'adresy_id',
    'rowTag' => 'adresa',
    'elements' => array(
	'adresy_id',
	'suchbegriff',
	'kdnr',
	'code',
	'firma',
	'ansprechpartner',
	'name',
	'vorname',
	'fullname',
	'funktion',
	'geboren',
	'telefon',
	'telefonprivat',
	'fax',
	'handy',
	'strasse',
	'ort',
	'plz',
	'adr',
	'email',
	'sonstiges',
    ),
);
					

// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);
//$domxml->encoding="windows-1250";

// pokusy s polem parameters
// rozkouskovat si ho na jednotlive polozky
// a vytvorim pole parametru
// TODO doplnit nejakou moznost zformatovani, napr. datum zadane ve formulari by melo nejak vypadat
// mozna by bylo lepsi resit to AJAXem uz pri zadavani do formulare
// ale uz pri generovani formulare budu muset pridat handlery podle planovaneho obsahu formulare

//foreach($parameters as $var=>$value)
//{
//	
//	// pokud nazev parametru obsahuje _label, budu hledat hodnotu parametru
//	if(strpos($var,"_label"))
//	{
//		$p[$value]=$last_value;
//	}
//	$last_value=$value;
//	//$promenne.=$var."=".$value."&";
//}
//
//// v promenne p bych mel mit seznam parametru, pridam ho do XML jako node do domxml
////
//
//$element=$domxml->createElement("parameters");
//$parametry=$domxml->firstChild;
//$parametry->appendChild($element);
//$i=1;
//foreach($p as $var=>$value)
//{
//	$poradinode=$domxml->createElement("N".$i);
//	$labelnode=$domxml->createElement("label",$var);
//	$valuenode=$domxml->createElement("value",$value);
//	$element->appendChild($poradinode);
//	$poradinode->appendChild($labelnode);
//	$poradinode->appendChild($valuenode);
//	$i++;
//}


//header('Content-Type: application/xml');
//echo $proc->transformToXML($domxml);



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
$domxml->save("S103.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
