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

$sql = "select";
$sql.= "     DATE_FORMAT(dpersbewerber.bewerbe_datum,'%y-%m-%d') as bewerbe_datum";
$sql.= "     ,DATE_FORMAT(dpersbewerber.eintritt_datum,'%y-%m-%d') as eintritt_datum";
$sql.= "     ,dpersbewerber.oe_voraussichtlich";
$sql.= "     ,if(dpersbewerber.artzt_eingang_untersuchung<>0,'J','N') as eingang_untersuchung";
$sql.= "     ,CONCAT(dpersbewerber.bewertung1,'/',dpersbewerber.bewertung2,'/',dpersbewerber.bewertung3) as bewertung";
$sql.= "     ,dpers.PersNr as persnr";
$sql.= "     ,dpers.`Name` as name";
$sql.= "     ,dpers.Vorname as vorname";
$sql.= "     ,dpers.regeloe";
$sql.= "     ,dtb1.text_kurz as geeignet_text_kurz";
$sql.= "     ,SUBSTR(dpers.dpersstatus,1,3) as dpersstatus";
$sql.= "     ,DATE_FORMAT(dpers.eintritt,'%y-%m-%d') as eintritt_datum_aktual";
$sql.= "     ,if(CHAR_LENGTH(dpersdetail1.kom7)>12,CONCAT(SUBSTR(dpersdetail1.kom7,1,12),'...'),dpersdetail1.kom7) as handy";
$sql.= "     ,CONCAT(dpers.name,' ',dpers.Vorname) as vollname";
$sql.= "     ,if(CHAR_LENGTH(CONCAT(dpersdetail1.plz,' ',dpers.komm_ort,' ',dpersdetail1.strasse))>25,CONCAT(SUBSTR(CONCAT(dpersdetail1.plz,' ',dpers.komm_ort,' ',dpersdetail1.strasse),1,25),'...'),CONCAT(dpersdetail1.plz,' ',dpers.komm_ort,' ',dpersdetail1.strasse)) as aufenthalt";
$sql.= "    ,DATE_FORMAT(dpers.geboren,'%y-%m-%d') as geboren";
$sql.= "    ,DATE_FORMAT(dpers.austritt,'%y-%m-%d') as austritt";
$sql.= "    ,dtb2.text_kurz as transport";
$sql.= " from dpersbewerber";
$sql.= " join dpers on dpers.PersNr=dpersbewerber.persnr";
$sql.= " join dtextbuch dtb1 on dtb1.id=dpersbewerber.geeignet_id";
$sql.= " left join dtextbuch dtb2 on dtb2.id=dpersbewerber.eigen_transport_id";
$sql.= " join dpersdetail1 on dpersdetail1.persnr=dpersbewerber.persnr";
$sql.= " where";
$sql.= "     (dpers.dpersstatus='BEWERBER' or dpers.dpersstatus='BEENDET' or dpers.dpersstatus='MA')";
if(($eintrittab!=NULL) && ($eintrittbis!=NULL)) $sql.= " and (dpersbewerber.eintritt_datum between '$eintrittab' and '$eintrittbis')";
$sql.= "     and (dpersbewerber.bewerbe_datum between '$bewerbdatvon' and '$bewerbdatbis')";
if($geeignet!='*') $sql.= " and dtb1.text_kurz='$geeignet'";
if($vorausoe!='*') $sql.= " and dpersbewerber.oe_voraussichtlich='$vorausoe'";
$sql.= " order by";
$sql.= "     dpersbewerber.eintritt_datum,";
$sql.= "     dpersbewerber.oe_voraussichtlich,";
$sql.= "     dpersbewerber.persnr";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

// vyber poli pro zobrazeni v sestave

$options = array(
		'encoder'=>false,
		'rootTag'=>'S167',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'name',
                    'vorname',
                    'vollname',
                    'dpersstatus',
                    'bewerbe_datum',
                    'eintritt_datum',
                    'eintritt_datum_aktual',
                    'oe_voraussichtlich',
                    'regeloe',
                    'aufenthalt',
                    'geboren',
                    'austritt',
                    'handy',
                    'bewertung',
                    'eingang_untersuchung',
                    'transport',
                    'geeignet_text_kurz',
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
$domxml->save("S167.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
