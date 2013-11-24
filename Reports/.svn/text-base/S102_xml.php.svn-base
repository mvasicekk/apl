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

$sql = " select";
$sql.= " dp.PersNr as persnr,";
$sql.= " CONCAT(dp.name,' ',dp.vorname) as name,";
$sql.= "     DATE_FORMAT(dp.eintritt,'%d.%m.%Y') as eintritt,";
$sql.= "     DATE_FORMAT(dp.geboren,'%d.%m.%Y') as geboren,";
$sql.= "     if(dp.geboren is null,'Z',DATE_FORMAT(dp.geboren,'%m-%d')) as geboren_sort,";
$sql.= "     dpersdetail1.kom7 as handy";
$sql.= " from dpers dp";
$sql.= " join dpersdetail1 on dpersdetail1.persnr=dp.persnr";
$sql.= " where";
$sql.= " dp.dpersstatus='MA' ";
if($austritt=='ja')
    $sql.= " and dp.austritt is null";
$sql.= " order by";
if($sort=='geboren')
    $sql.= "     if(dp.geboren is null,'Z',DATE_FORMAT(dp.geboren,'%m-%d')),dp.persnr";
else
    $sql.= "     dp.persnr";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'S102',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'name',
                    'eintritt',
                    'geboren',
                    'geboren_sort',
                    'handy'
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
$domxml->save("S102.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
