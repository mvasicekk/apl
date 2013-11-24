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

$sql="";

$sql.=" select ";
$sql.="     concat(convert(drueck.Teil,CHAR),' ') as teil,";
$sql.="     dkopf.Teilbez as teilbez,";
$sql.="     dkopf.Gew as gew,";
$sql.="     concat(convert(dkopf.`Muster-Platz`,CHAR),' ') as muster,";
$sql.="     sum(if(drueck.TaetNr=417,drueck.`Stück`,0)) as stk_417,";
$sql.="     sum(if(drueck.TaetNr=446,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as vzkd_446,";
$sql.="     sum(if(drueck.TaetNr=446,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_446,";
$sql.="     sum(if(drueck.TaetNr=2446,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as vzkd_2446,";
$sql.="     sum(if(drueck.TaetNr=2446,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_2446,";
$sql.="     sum(if(drueck.TaetNr=2546,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as vzkd_2546,";
$sql.="     sum(if(drueck.TaetNr=451,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as vzkd_451,";
$sql.="     sum(if(drueck.TaetNr=451,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_451,";
$sql.="     sum(if(drueck.TaetNr=2451,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`),0)) as vzkd_2451,";
$sql.="     sum(if(drueck.TaetNr=2451,if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as vzaby_2451,";
$sql.="     sum(drueck.`Verb-Zeit`) as verb";
$sql.=" from drueck";
$sql.=" join dkopf on dkopf.Teil=drueck.Teil";
$sql.=" where";
$sql.="     dkopf.Kunde=$kunde";
$sql.="     and drueck.Datum between '$datumvon' and '$datumbis'";
$sql.=" group by";
$sql.="     drueck.Teil";
    
//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'E310',
		'idColumn'=>'teil',
		'rowTag'=>'teil_row',
		'elements'=>array(
                    'teil',
                    'teilbez',
                    'gew',
                    'muster',
                    'stk_417',
                    'vzkd_446',
                    'vzaby_446',
                    'vzkd_2446',
                    'vzaby_2446',
                    'vzkd_2546',
                    'vzkd_451',
                    'vzaby_451',
                    'vzkd_2451',
                    'vzaby_2451',
                    'verb'
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
$domxml->save("E310.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
