<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";
//require '../db.php';

global $aplDB;
$aplDB = AplDB::getInstance();

// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

$pcip=get_pc_ip();

$von = $jahr."-".$monat."-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr."-".$monat."-".$pocetDnuVMesici;

// vytvorim si nekolik pohledu

$views=array("v_dzeit","v_vorschuss","v_essen","v_praemien","v_leistung","v_transport");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="    dpers.persnr,";
$pt.="    dpers.`Name` as name,";
$pt.="    dpers.`Vorname` as vorname,";
$pt.="    dpers.`Schicht` as schicht,";
$pt.="    DATE_FORMAT(dpers.eintritt,'%y-%m-%d') as eintritt,";
$pt.="    DATE_FORMAT(dpers.austritt,'%y-%m-%d') as austritt,";
$pt.="    DATE_FORMAT(dpersdetail1.dobaurcita,'%y-%m-%d') as dobaurcita,";
$pt.="    DATE_FORMAT(dpersdetail1.zkusebni_doba_dobaurcita,'%y-%m-%d') as zkusebni_doba_dobaurcita,";
$pt.="    dpersdetail1.lohnkoef,";
$pt.="    dpersdetail1.mzda_podle_smen,";
$pt.="    sum(dzeit.`Stunden`) as sumstunden,";
$pt.="    sum(if(dtattypen.oestatus='a',dzeit.`Stunden`,0)) as sumstundena,";
$pt.="    sum(if(dzeit.tat='z',1,0)) as tage_z,";
$pt.="    sum(if(dzeit.tat='nv',1,0)) as tage_nv,";
$pt.="    sum(if(dzeit.tat='nw',1,0)) as tage_nw,";
$pt.="    sum(if(dzeit.tat='d',1,0)) as tage_d,";
$pt.="    sum(if(dzeit.tat='np',1,0)) as tage_np,";
$pt.="    sum(if(dzeit.tat='n',1,0)) as tage_n,";
$pt.="    sum(if(dzeit.tat='nu',1,0)) as tage_nu,";
$pt.="    sum(if(dzeit.tat='p',1,0)) as tage_p,";
$pt.="    sum(if(dzeit.tat='u',1,0)) as tage_u,";
$pt.="    sum(if(dzeit.tat='?',1,0)) as tage_frage,";
$pt.="    durlaub1.jahranspruch,";
$pt.="    durlaub1.rest as restold,";
$pt.="    if(durlaub1.genom>=durlaub1.rest,0,durlaub1.rest-durlaub1.genom) as rest,";
$pt.=" durlaub1.gekrzt,";
$pt.="    durlaub1.genom,";
$pt.=" durlaub1.jahranspruch+durlaub1.rest-durlaub1.gekrzt-durlaub1.genom as offen";
$pt.=" from dpers";
$pt.=" join dzeit using(persnr)";
$pt.=" join dtattypen on dzeit.tat=dtattypen.tat";
$pt.=" left join dpersdetail1 on dpersdetail1.persnr=dpers.`PersNr`";
$pt.=" left join durlaub1 on durlaub1.`PersNr`=dpers.`PersNr`";
$pt.=" where";
$pt.=" (";
$pt.="    (dpers.austritt is null or dpers.austritt>='$von' or dpers.eintritt>dpers.austritt)";
$pt.="    and (dzeit.`Datum` between '$von' and '$bis')";
$pt.="    and (dpers.persnr between '$persvon' and '$persbis')";
$pt.=" )";
$pt.=" group by dpers.`PersNr`";


//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dvorschuss.`PersNr` as persnr,sum(dvorschuss.`Vorschuss`) as sumvorschuss ";
$pt.=" from dvorschuss where `PersNr` between '$persvon' and '$persbis' ";
$pt.=" and datum between '$von' and '$bis' group by persnr";
//echo $pt."<br>";
$db->query($pt);


$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dzeit.`PersNr` as persnr,sum(dessen.essen_preis) sumessen";
$pt.=" from dzeit";
$pt.=" join dessen on dzeit.id_essen=dessen.id_essen";
$pt.=" where dzeit.`PersNr` between '$persvon' and '$persbis' and dzeit.`Datum` between '$von' and '$bis' and dzeit.essen<>0";
$pt.=" group by dzeit.persnr";
$db->query($pt);

$viewname=$pcip.$views[3];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dpraemie.persnr,dpraemie.leistpraem,dpraemie.qualpraemie,dpraemie.erschwerniss,dpraemie.sonstprem,dpraemie.gilt";
$pt.=" from dpraemie";
$pt.=" where dpraemie.persnr between '$persvon' and '$persbis'";
$pt.=" and dpraemie.monat='$monat' and dpraemie.`Jahr`='$jahr'";
$db->query($pt);

$viewname=$pcip.$views[4];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select drueck.`PersNr`,";
$pt.=" sum(if(auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as leistung,";
$pt.=" sum(if(auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`*koef,(drueck.`Stück`)*drueck.`VZ-IST`*koef)) as leistungkc";
$pt.=" from drueck";
$pt.=" join dschicht on drueck.schicht=dschicht.schichtnr";
$pt.=" where drueck.persnr between '$persvon' and '$persbis'";
$pt.=" and drueck.datum between '$von' and '$bis'";
$pt.=" group by drueck.persnr";
//echo "$pt";
$db->query($pt);

//transport
$viewname=$pcip.$views[5];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dperstransport.persnr,sum(dperstransport.preis) as transport";
$pt.=" from dperstransport";
$pt.=" where dperstransport.persnr between '$persvon' and '$persbis' and dperstransport.datum between '$von' and '$bis'";
$pt.=" group by dperstransport.persnr";
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$v_dzeit=$pcip.$views[0];
$v_vorschuss = $pcip.$views[1];
$v_essen = $pcip.$views[2];
$v_praemien = $pcip.$views[3];
$v_leistung = $pcip.$views[4];
$v_transport = $pcip.$views[5];

$sql = "select $v_dzeit.*,$v_vorschuss.sumvorschuss,$v_essen.sumessen,";
$sql.=" $v_praemien.leistpraem,qualpraemie,erschwerniss,sonstprem,gilt,";
$sql.=" $v_leistung.leistung,$v_leistung.leistungkc,$v_transport.transport";
$sql.=" ,'$bis' as datumbis";
$sql.=" ,'$von' as datumvon";
$sql.=" from $v_dzeit";
$sql.=" left join $v_vorschuss on $v_dzeit.persnr=$v_vorschuss.persnr";
$sql.=" left join $v_essen on $v_dzeit.persnr=$v_essen.persnr";
$sql.=" left join $v_praemien on $v_dzeit.persnr=$v_praemien.persnr";
$sql.=" left join $v_leistung on $v_dzeit.persnr=$v_leistung.persnr";
$sql.=" left join $v_transport on $v_dzeit.persnr=$v_transport.persnr";
$sql.=" order by $v_dzeit.persnr";

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

function urlaubgenom($record)
{
        global $aplDB;
	return $aplDB->getUrlaubtageGenommenBis($record['persnr'], $record['datumbis']);
}

function offen($record){
    $gen = urlaubgenom($record);
    // 2010-01-05 gekrzt se bude zadavat s opacnym znamenkem, tedy kratim dovolenou o 3 dny - zadam -3
    $offen = $record['jahranspruch']+$record['restold']+$record['gekrzt']-$gen;
    return $offen;
}

function SoTage($record){
        global $aplDB;
	return $aplDB->getNotInATageInArbeitCountBetweenDatums($record['datumvon'],$record['datumbis'],$record['persnr']);
}

$options = array(
		'encoder'=>false,
		'rootTag'=>'S140',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                                'datumvon',
                                'datumbis',
                                'persnr',
                                'name',
                                'vorname',
                                'eintritt',
                                'austritt',
                                'dobaurcita',
                                'zkusebni_doba_dobaurcita',
                                'lohnkoef',
                                'mzda_podle_smen',
                                'schicht',
                                'sumstunden',
                                'sumstundena',
                                'tage_z',
                                'tage_nv',
                                'tage_nw',
                                'tage_d',
                                'tage_np',
                                'tage_n',
                                'tage_nu',
                                'tage_u',
                                'tage_p',
                                'tage_frage',
                                'tage_so'=>'#SoTage();',
                                'jahranspruch',
                                'rest',
                                'restold',
                                'gekrzt',
                                'genom'=>'#urlaubgenom();',
                                'offen'=>'#offen();',
                                'sumvorschuss',
                                'sumessen',
                                'transport',
                                'leistpraem',
                                'qualpraemie',
                                'erschwerniss',
                                'sonstprem',
                                'gilt',
                                'leistung',
                                'leistungkc'
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
$domxml->save("S140.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
