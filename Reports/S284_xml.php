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



// vytvorim si nekolik pohledu


$sql = "SELECT drueck.teil, dkopf.teilbez, drueck.TaetNr, ";
$sql .="`dtaetkz-abg`.Name, Sum(drueck.`Stück`) AS sumstk,";
$sql .=" sum(if(auss_typ=4,(`Stück`+`Auss-Stück`)*`vz-ist`,(`Stück`)*`vz-ist`)) AS sumvzaby,";
$sql .=" sum(drueck.`Verb-Zeit`) AS sumverb";
$sql .=" FROM ((drueck INNER JOIN dpers ON drueck.PersNr = dpers.PersNr) INNER JOIN dkopf ON drueck.Teil = dkopf.Teil)";
$sql .=" INNER JOIN `dtaetkz-abg` ON drueck.TaetNr = `dtaetkz-abg`.`abg-nr`";
$sql .=" WHERE (((drueck.PersNr) Between '$pers_von' And '$pers_bis') AND ((drueck.Datum) Between '$datum_von' And '$datum_bis'))";
$sql .=" GROUP BY drueck.Teil, dkopf.Teilbez, drueck.TaetNr, `dtaetkz-abg`.Name";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$options = array(
					'encoder'=>false,
					'rootTag'=>'S284',
					'idColumn'=>'teil',
					'rowTag'=>'teil',
					'elements'=>array(
						'teilnr'=>'teil',
						'teilbez',
						'taetigkeiten'=>array(
							'rootTag'=>'taetigkeiten',
							'rowTag'=>'taetigkeit',
							'idColumn'=>'TaetNr',
							'elements'=>array(
								'teilnr'=>'teil',
								'teilbez',
								'tatnr'=>'TaetNr',
								'Name',
								'sumstk',
								'sumvzaby',
								'sumverb'
							),
						),
					),
				);

// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML
					
$domxml = $query2xml->getXML($sql,$options);

$domxml->encoding="UTF-8";

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


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
//$domxml->save("S284.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
