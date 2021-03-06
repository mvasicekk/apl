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
$sql.= "    dfaehigkeittyp.id as Q_typ_id,";
$sql.= "    dfaehigkeittyp.beschreibung as Q_typ_beschreibung,";
$sql.= "    dfaehigkeittyp.stat_nr as Q_typ_statnr,";
$sql.= "    dpersfaehigkeit.persnr,";
$sql.= "    dpers.name,";
$sql.= "    dpers.vorname,";
$sql.= "    dpers.regeloe,";
$sql.= "    dfaehigkeiten.id as id_faehigkeit,";
$sql.= "    dfaehigkeiten.faeh_abkrz,";
$sql.= "    dfaehigkeiten.beschreibung Q_beschreibung,";
$sql.= "    dpersfaehigkeit.soll,";
$sql.= "    dpersfaehigkeit.ist";
$sql.= " from";
$sql.= "    dpersfaehigkeit";
$sql.= " join";
$sql.= "    dfaehigkeiten on dpersfaehigkeit.faehigkeit_id=dfaehigkeiten.id";
$sql.= " join";
$sql.= "    dfaehigkeittyp on dfaehigkeiten.faehigkeit_typ=dfaehigkeittyp.id";
$sql.= " join";
$sql.= "    dpers on dpers.persnr=dpersfaehigkeit.persnr";
$sql.= " where";
$sql.= "    (dpers.austritt is null or dpers.eintritt>dpers.austritt)";
// jen MA
$sql.= "    and (dpers.dpersstatus='MA')";
$sql.= "    and (dpersfaehigkeit.persnr between '$persvon' and '$persbis')";
if($qtyp!='*')
    $sql .= " and (dfaehigkeittyp.beschreibung='$qtyp')";
$sql.= " order by";
$sql.= "    dfaehigkeittyp.stat_nr,";
$sql.= "    dpersfaehigkeit.persnr,";
$sql.= "    dpersfaehigkeit.faehigkeit_id";


//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'S170',
		'idColumn'=>'Q_typ_id',
		'rowTag'=>'Q_typ',
		'elements'=>array(
                    'Q_typ_id',
                    'Q_typ_beschreibung',
                    'Q_typ_statnr',
                    'personen'=>array(
                        'rootTag'=>'personen',
                        'idColumn'=>'persnr',
                        'rowTag'=>'person',
                        'elements'=>array(
                            'persnr',
                            'name',
                            'vorname',
                            'regeloe',
                            'faehigkeiten'=>array(
                                'rootTag'=>'faehigkeiten',
                                'idColumn'=>'id_faehigkeit',
                                'rowTag'=>'faehigkeit',
                                'elements'=>array(
                                    'id_faehigkeit',
                                    'faeh_abkrz',
                                    'Q_beschreibung',
                                    'soll',
                                    'ist'
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
$domxml->save("S170.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
