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

$sql = "select ";
$sql.= " idun,YEAR(persunfall.`Datum`) as jahr,";
$sql.= " MONTH(persunfall.`Datum`) as monat,";
$sql.= " DATE_FORMAT(persunfall.`Datum`,'%Y-%m-%d') as datum,";
$sql.= " persunfall.`PerNr` as persnr,";
$sql.= " dpers.`Name` as name,dpers.`Vorname` as vorname,CONCAT(dpers.name,' ',dpers.vorname) as vollname,";
$sql.= " unfalltyp.unfalltyp,";
$sql.= " persunfall.typ";
$sql.= " from persunfall";
$sql.= " join dpers on persunfall.`PerNr`=dpers.`PersNr`";
$sql.= " join unfalltyp on unfalltyp.idunfall=persunfall.typ";
$sql.= " where";
$sql.= "    persunfall.`Datum` between '$von' and '$bis'";
$sql.= "    and persunfall.pernr between $persvon and $persbis";
$sql.= " order by";
$sql.= " YEAR(persunfall.`Datum`),MONTH(persunfall.`Datum`),persunfall.`Datum`,persunfall.`PerNr`";

//echo $sql;
//
//$sql = "select";
//$sql.= "     month(persunfall.`Datum`) as monat,";
//$sql.= "     persunfall.`PerNr` as persnr,";
//$sql.= "     DATE_FORMAT(persunfall.`Datum`,'%d.%m.%Y') as datum,";
//$sql.= "     persunfall.typ,";
//$sql.= "     count(persunfall.typ) as poceturazu_typu";
//$sql.= " from";
//$sql.= "     persunfall";
//$sql.= " where";
//$sql.= "     persunfall.`Datum` between '$von' and '$bis'";
//$sql.= " group by";
//$sql.= "     month(persunfall.`Datum`),persunfall.`PerNr`,persunfall.`Datum`,persunfall.typ";
//

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

$options = array(
    'encoder' => false,
    'rootTag' => 'S182',
    'idColumn' => 'jahr',
    'rowTag' => 'jahr',
    'elements' => array(
        'jahrnr' => 'jahr',
        'monate' => array(
            'rootTag' => 'monate',
            'rowTag' => 'monat',
            'idColumn' => 'monat',
            'elements' => array(
                'jahrnr' => 'jahr',
                'monatnr'=>'monat',
                'unfalle'=>array(
                    'rootTag' => 'unfalle',
                    'rowTag' => 'unfall',
                    'idColumn' => 'idun',
                    'elements' => array(
                        'datum',
                        'persnr',
                        'name',
                        'vorname',
                        'vollname',
                        'unfalltyp',
                        'typ',
                        ),
                    ),
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


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S182.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
