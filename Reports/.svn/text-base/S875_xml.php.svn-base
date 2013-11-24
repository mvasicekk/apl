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
$sql.= "    drech.AuftragsNr as auftragsnr,";
$sql.= "     `dtaetkz-abg`.Stat_Nr as statnr,";
$sql.= "     dstat.`T-Stat-Text` as text,";
$sql.= "     sum(drech.DM*(drech.`Stück`+drech.Ausschuss)) as betrag";
$sql.= " from drech";
$sql.= " join daufkopf on daufkopf.auftragsnr=drech.AuftragsNr";
$sql.= " join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drech.abgnr";
$sql.= " join dstat on dstat.Stat_Nr=`dtaetkz-abg`.Stat_Nr";
$sql.= " where";
$sql.= "     daufkopf.ausliefer_datum between '$ausliefer_von' and '$ausliefer_bis'";
if($kunde!='*')
$sql.= "     and daufkopf.kunde='$kunde'";
if($teil!='%')
$sql.= "     and drech.teil like '$teil'";
$sql.= " group by";
$sql.= "     drech.AuftragsNr,";
$sql.= "     `dtaetkz-abg`.stat_nr";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

$options = array(
                'encoder'=>FALSE,
		'rootTag'=>'S875',
		'idColumn'=>'auftragsnr',
		'rowTag'=>'auftraege',
		'elements'=>array(
			'auftragsnr',
			'statnr'=>array(
				'rootTag'=>'stat',
				'rowTag'=>'statnr_row',
				'idColumn'=>'statnr',
				'elements'=>array(
					'statnr',
					'text',
					'betrag',
				),
			),
		),
);
					

// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);

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
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


$db->disconnect();
//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S875.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
