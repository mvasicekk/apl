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

$sql.=" select";
$sql.="     dpers.PersNr as persnr,";
$sql.="     dpers.`Name` as name,";
$sql.="     dpers.Vorname as vorname,";
$sql.="     dpersdetail1.kom7 as handy,";
$sql.="     dpersdetail1.strasse as strasse,";
$sql.="     dpers.komm_ort as ort,";
$sql.="     DATE_FORMAT(dpers.austritt,'%Y-%m-%d') as austritt,";
$sql.="     DATE_FORMAT(dpers.geboren,'%Y-%m-%d') as geboren,";
$sql.="     DATE_FORMAT(dpers.eintritt,'%Y-%m-%d') as eintritt,";
$sql.="     dpers.dpersstatus as status, ";
$sql.="     dpers.regeloe";
$sql.=" from ";
$sql.="     dpers";
$sql.=" left join dpersdetail1 on dpersdetail1.persnr=dpers.persnr";
$sql.=" where";
$sql.="     1";
if(!$bAlle){
$sql.="     and ((austritt is null) or (eintritt>austritt))";
$sql.="     and dpers.dpersstatus='MA'";
$sql.="     and dpers.kor=0";
}
$sql.=" order by";
if($sort=='geboren'){
$sql.="     month(geboren),";
$sql.="     day(geboren),";
}
$sql.="     PersNr";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'E140',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'name',
                    'vorname',
                    'austritt',
                    'geboren',
                    'eintritt',
                    'status',
		    'regeloe',
                    'handy',
                    'strasse',
                    'ort',
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
$domxml->save("E140.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
