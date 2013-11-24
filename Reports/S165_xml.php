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


$pcip=get_pc_ip();

$sql = "select dzeit.id,concat(dpers.name,' ',dpers.vorname) as name,dzeit.`Datum` as datum,";
$sql.=" dzeit.persnr,DATE_FORMAT(anw_von,'%H:%i') as anw_von,DATE_FORMAT(anw_bis,'%H:%i') as anw_bis,";
$sql.=" dzeit.`Stunden` as stunden,dzeit.pause1,dzeit.pause2,dzeit.`Schicht` as schicht,dzeit.tat,dzeit.essen";
$sql.=" from dzeit join dpers on dzeit.persnr=dpers.persnr where dzeit.`Datum`='$datum' order by dzeit.`PersNr`,dzeit.anw_von";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'S165',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'name',
                    'datum',
                    'anwesenheit'=>array(
      				'rootTag'=>'anwesenheiten',
				'rowTag'=>'anwesenheit',
				'idColumn'=>'id',
				'elements'=>array(
                                    'anw_von',
                                    'anw_bis',
                                    'stunden',
                                    'pause1',
                                    'pause2',
                                    'schicht',
                                    'tat',
                                    'essen',
                                ),
                    ),
                  ),
);
					

// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);
//$domxml->encoding="windows-1250";

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
$domxml->save("S165.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
