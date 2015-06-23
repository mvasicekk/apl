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
//$views=array("v_drueck","v_anwesenheit");


//$viewname=$pcip.$views[0];
//$db->query("drop view $viewname");
//$pt="create view $viewname";
//$pt.=" as SELECT DRUECK.PersNr,DRUECK.Datum,sum(if(DRUECK.auss_typ=4,(DRUECK.`Stück`+DRUECK.`Auss-Stück`)*DRUECK.`VZ-IST`,DRUECK.`Stück`*DRUECK.`VZ-IST`)) AS vzaby, sum(if(DRUECK.auss_typ=4,(DRUECK.`Stück`+DRUECK.`Auss-Stück`)*DRUECK.`VZ-soll`,DRUECK.`Stück`*DRUECK.`VZ-Soll`)) AS vzkd, Sum(DRUECK.`Verb-Zeit`) AS verb";
//$pt.=" FROM DRUECK";
//$pt.=" where ((drueck.datum='".$datum."'))";
//$pt.=" GROUP BY DRUECK.PersNr, DRUECK.Datum";
//
////echo $pt."<br>";
//$db->query($pt);
//
//$viewname=$pcip.$views[1];
//$db->query("drop view $viewname");
//$pt="create view $viewname";
//$pt.=" as SELECT persnr,datum,schicht as schichtanw,sum(stunden*60) as anwesenheit FROM `dzeit`";
//$pt.=" join dtattypen on dtattypen.tat=dzeit.tat";
//$pt.=" WHERE ((datum='".$datum."') and (schicht between '".$schicht_von."' and '".$schicht_bis."') and (dtattypen.oestatus='a'))";
//$pt.=" group by persnr,datum,schicht";
////echo $pt."<br>";
//$db->query($pt);
//
//// provedu dotaz nad vytvorenymi pohledy
//$v_drueck=$pcip.$views[0];
//$v_anwesenheit=$pcip.$views[1];

$sql = "select";
$sql.= " dpers.`PersNr` as persnr,";
$sql.= " CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,";
$sql.= " DATE_FORMAT(dpers.austritt,'%y-%m-%d') as austritt,";
$sql.= " DATE_FORMAT(dpers.eintritt,'%y-%m-%d') as eintritt,";
$sql.= " if(dpers.premie_za_vykon<>0,1,0) as premie_za_vykon,";
$sql.= " if(dpers.qpremie_akkord<>0,1,0) as qpremie_akkord,";
$sql.= " if(dpers.qpremie_zeit<>0,1,0) as qpremie_zeit,";
$sql.= " if(dpers.premie_za_prasnost<>0,1,0) as premie_za_prasnost,";
$sql.= " if(dpers.premie_za_3_mesice<>0,1,0) as premie_za_3_mesice,";
$sql.= " if(dpers.bewertung<>0,1,0) as bewertung,dpers.regelarbzeit,";
$sql.= " dpers.regeloe,";
$sql.= " dpers.alteroe,";
$sql.= " dpers.lohnfaktor,";
$sql.= " dpers.leistfaktor,";
$sql.= " SUBSTRING(dpers.dpersstatus,1,3) as dpersstatus,";
$sql.= " dpersdetail1.regeltrans,";
$sql.= " if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),0) as tage_nach_austritt";
$sql.= " from";
$sql.= " dpers";
$sql.= " left join";
$sql.= " dpersdetail1";
$sql.= " on";
$sql.= " dpers.`PersNr`=dpersdetail1.persnr";
if($reporttyp=='alle')
    $sql.= " where (dpers.eintritt is not null) and (dpers.austritt is null or dpers.austritt<dpers.eintritt or if(dpers.austritt is not null,DATEDIFF(NOW(),dpers.austritt),0)<60)";
else if($reporttyp=='Eintritt'){
    $von = sprintf("%04d-%02d-%02d",$jahr,$monat,1);
    $bis = sprintf("%04d-%02d-%02d",$jahr,$monat,  cal_days_in_month(CAL_GREGORIAN, $monat, $jahr));
    $sql.= " where (dpers.eintritt between '$von' and '$bis')";
}
else{
    $von = sprintf("%04d-%02d-%02d",$jahr,$monat,1);
    $bis = sprintf("%04d-%02d-%02d",$jahr,$monat,  cal_days_in_month(CAL_GREGORIAN, $monat, $jahr));
    $sql.= " where (dpers.austritt between '$von' and '$bis')";
}


// vynechat korekcni cisla, tj. cisla zacinajici na 9 a majici 4 mista


$sql.= " and (dpers.persnr>9999 or dpers.persnr<9000)";
$sql.= " order by";
$sql.= " dpers.`PersNr`";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'S169',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'name',
                    'austritt',
                    'eintritt',
                    'premie_za_vykon',
                    'qpremie_akkord',
                    'qpremie_zeit',
                    'premie_za_prasnost',
                    'premie_za_3_mesice',
                    'bewertung',
                    'regelarbzeit',
                    'regeloe',
                    'alteroe',
		    'dpersstatus',
                    'lohnfaktor',
                    'leistfaktor',
                    'regeltrans',
                    'tage_nach_austritt',
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
$domxml->save("S169.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
