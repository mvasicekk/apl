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

$views=array("anwesenheit");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT dzeit.persnr,dzeit.datum,dtattypen.og,sum(stunden) as stunden,sum(if(dtattypen.erschwerniss<>0,dzeit.stunden*6,0)) as prasne from dzeit join dpers on dpers.persnr=dzeit.persnr join dtattypen on dzeit.tat=dtattypen.tat where dzeit.persnr='$persnr' and dzeit.datum>=dpers.eintritt group by dzeit.persnr,dzeit.datum,dtattypen.og";
//echo $pt;
//exit;
$db->query($pt);
$anwesenheit=$pcip.$views[0];


// 2011-03-17
// nova verze dotazu pro korekci procent priplatku v zqavislosti na Stat_Nr
//select
//    drueck.persnr,
//    drueck.`Datum`,
//    dtattypen.`OG`,
//    `dtaetkz-abg`.`Stat_Nr`,
//    AVG(dtattypen.lohnfaktor) as lohnfaktor,
//    sum(if(auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,drueck.`Stück`*drueck.`VZ-IST`)) as vzaby_min,
//    sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor)) as kc,
//    sum(if((drueck.`TaetNr` between 7000 and 7999) or (drueck.`TaetNr` between 5000 and 5999),if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as bezpriplatku,
//    sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor*dtattypen.qualitatspraemie/100,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor*dtattypen.qualitatspraemie/100)) as qualpraemie
//from
//    drueck
//join dpers on dpers.`PersNr`=drueck.`PersNr`
//join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.`TaetNr`
//join dtattypen on dtattypen.tat=drueck.oe
//where
//    drueck.datum>=dpers.eintritt and drueck.datum<DATE_ADD(dpers.eintritt,INTERVAL 62 DAY)
//    and drueck.persnr=2609
//group by
//    drueck.`PersNr`,
//    drueck.`Datum`,
//    dtattypen.`OG`,
//    `dtaetkz-abg`.`Stat_Nr`

// zpracuji dotaz podle specifikace SQL

$sql = "select drueck.`PersNr` as persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,";
$sql .= " DATE_FORMAT(drueck.`Datum`,'%Y%m%d') as Datum,dtattypen.og,AVG(dtattypen.lohnfaktor) as lohnfaktor,";
$sql .= " $anwesenheit.stunden,$anwesenheit.prasne,sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor)) as kc,";
//$sql .= " sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.qualitatspraemie/100,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.qualitatspraemie/100)) as qualpraemie,";
$sql .= " sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as vzaby,";
$sql .= " sum(if((drueck.`TaetNr` between 7000 and 7999) or (drueck.`TaetNr` between 5000 and 5999),if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`),0)) as bezpriplatku,";
$sql .= " sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor*dtattypen.qualitatspraemie/100,(drueck.`Stück`)*drueck.`VZ-IST`*dtattypen.lohnfaktor*dtattypen.qualitatspraemie/100)) as qualpraemie";
$sql .= " from drueck";
$sql .= " join dtattypen on drueck.oe=dtattypen.tat";
$sql .= " join dpers on dpers.`PersNr`=drueck.`PersNr`";
$sql .= " left join $anwesenheit on $anwesenheit.persnr=drueck.persnr and $anwesenheit.datum=drueck.datum and dtattypen.og=$anwesenheit.og";
$sql .= " where (drueck.`Datum`>=dpers.eintritt and drueck.`PersNr`='$persnr') ";
$sql .= " group by drueck.`PersNr`,drueck.datum,dtattypen.og";
// 2010-06-07 vybrat jen dny s nenulovym vykonem
$sql .= " having sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`))>0";
$sql .= " order by drueck.`Datum`,dtattypen.og";

//echo $sql;
$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'S1XX2',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'name',
                    'eintritt',
                    'datumy'=>array(
                        'rootTag'=>'tage',
                        'idColumn'=>'Datum',
                        'rowTag'=>'tag',
                        'elements'=>array(
                            'datum'=>'Datum',
                            'ogs'=>array(
                                'rootTag'=>'ogs',
                                'idColumn'=>'og',
                                'rowTag'=>'og',
                                'elements'=>array(
                                    'ognr'=>'og',
                                    'stunden',
                                    'prasne',
                                    'lohnfaktor',
                                    'vzaby',
                                    'qualpraemie',
                                    'kc',
                                    'bezpriplatku',
                                 ),
                            ),
                        ),
                    ),
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
$domxml->save("S191.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
