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

$von = $jahr."-".$monat."-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr."-".$monat."-".$pocetDnuVMesici;

$pcip=get_pc_ip();

$sql= "select dperstransport.id,dperstransport.persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,DATE_FORMAT(dperstransport.datum,'%Y-%m-%d') as datum,dkfz.marke,dkfz.sitzen,dkfz.rz,dperstransport.preis from dperstransport";
$sql.= " join dpers on dpers.`PersNr`=dperstransport.persnr";
$sql.= " join dkfz on dkfz.id=dperstransport.kfz";
$sql.= " where dperstransport.persnr between '$persvon' and '$persbis'";
$sql.= " and dperstransport.datum between '$von' and '$bis'";
$sql.= " order by dperstransport.persnr,dperstransport.datum,dkfz.marke";



$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

$options = array(
		'encoder'=>false,
		'rootTag'=>'S188',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
			'persnr',
                        'name',
                        'tage'=>array(
                            'rootTag'=>'tage',
                            'rowTag'=>'tag',
                            'idColumn'=>'datum',
                            'elements'=>array(
                                'datum',
                                'kfz'=>array(
                                    'rootTag'=>'autos',
                                    'rowTag'=>'kfz',
                                    'idColumn'=>'id',
                                    'elements'=>array(
                                        'marke',
                                        'preis',
                                        'sitzen',
                                        'rz',
                                        'id',
                                    ),
                                )
                             ),
                         ),
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


//$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S188.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
