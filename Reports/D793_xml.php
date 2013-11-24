<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";
require_once '../db.php';


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu

$a = AplDB::getInstance();

$pcip=get_pc_ip();

$views=array("D793_sumteilstk","");
//
//// povolit naslouchani na portu
//
$viewname=$pcip.$views[0];
$db->query("drop view $viewname");


if($rechnung=="DCZ"){
$pt=" create view $viewname as ";
$pt.=" select ";
$pt.="     drechneu.an as kunde, ";
$pt.="     drechneu.Teil as teil, ";
$pt.="     drechneu.teilbez, ";
$pt.="     dkopf.gew as gew, ";
$pt.="     dkopf.teillang, ";
$pt.="     dkopf.`Muster-Freigabe-2` as komplex, ";
$pt.="     drechneu.auftragsnr as import, ";
$pt.="     drechneu.`Taet-kz` as tatkz, ";
$pt.="     drechneu.Text1 as text1,";
$pt.="     drechneu.dm,";
$pt.="     drechneu.abgnr,";
$pt.="     sum(drechneu.`Stück`) as sum_stk, ";
$pt.="     sum(drechneu.Ausschuss) as sum_auss, ";
//$pt.="     sum((drechneu.`Stück`+drechneu.Ausschuss)*drechneu.DM) as betrag, ";
//$pt.="     max(drechneu.abgnr) as maxabgnr, ";
//$pt.="     count(drechneu.abgnr) as countabgnr, ";

$pt.="     if(drechneu.abgnr<2000,'REGEL','MA') as re_ma ";
$pt.=" from drechneu join dkopf on dkopf.teil=drechneu.teil ";

// 2012-07-12 zmena, misto rechnungsdatumu vybiram podle auslief-datumu
$pt.=" where 1 and drechneu.`datum-auslief` between '$von' and '$bis' and drechneu.an='$kunde' and drechneu.`Taet-kz`<>'I'";
if(strlen($komplexKz)>0)
    $pt.=" and dkopf.`Muster-Freigabe-2`='$komplexKz'";
//$pt.=" and drechneu.Teil='18500015' ";
$pt.=" group by ";
$pt.="     drechneu.Teil, ";
$pt.="     drechneu.auftragsnr, ";
$pt.="     drechneu.`Taet-kz`,";
$pt.="     drechneu.dm,";
$pt.="     drechneu.abgnr";
}
else{
$pt=" create view $viewname as ";
$pt.=" select ";
$pt.="     daufkopf.kunde as kunde, ";
$pt.="     drech.Teil as teil, ";
$pt.="     drech.teilbez, ";
$pt.="     dkopf.gew as gew, ";
$pt.="     dkopf.teillang, ";
$pt.="     dkopf.`Muster-Freigabe-2` as komplex, ";
$pt.="     drech.auftragsnr as import, ";
$pt.="     drech.`Taet-kz` as tatkz, ";
$pt.="     drech.Text1 as text1,";
$pt.="     drech.dm,";
$pt.="     drech.abgnr,";
$pt.="     sum(drech.`Stück`) as sum_stk, ";
$pt.="     sum(drech.Ausschuss) as sum_auss, ";
$pt.="     if(drech.abgnr<2000,'REGEL','MA') as re_ma ";
$pt.=" from drech join dkopf on dkopf.teil=drech.teil ";
$pt.=" join daufkopf on daufkopf.auftragsnr=drech.auftragsnr";

// 2012-07-12 zmena, misto rechnungsdatumu vybiram podle auslief-datumu
$pt.=" where 1 and drech.`datum-auslief` between '$von' and '$bis' and daufkopf.kunde='$kunde' and drech.`Taet-kz`<>'I'";
if(strlen($komplexKz)>0)
    $pt.=" and dkopf.`Muster-Freigabe-2`='$komplexKz'";
//$pt.=" and drechneu.Teil='18500015' ";
$pt.=" group by ";
$pt.="     drech.Teil, ";
$pt.="     drech.auftragsnr, ";
$pt.="     drech.`Taet-kz`,";
$pt.="     drech.dm,";
$pt.="     drech.abgnr";
}
//
//$pt.=" order by drechneu.Teil, drechneu.auftragsnr, max(drechneu.abgnr)";
$db->query($pt);
//echo "pt=$pt";
$D793_sumteilstk=$pcip.$views[0];
// dotaz do DB

$sql.=" select ";
$sql.="     $D793_sumteilstk.kunde, ";
$sql.="     $D793_sumteilstk.teil, ";
$sql.="     $D793_sumteilstk.teilbez, ";
$sql.="     $D793_sumteilstk.gew, ";
$sql.="     $D793_sumteilstk.teillang, ";
$sql.="     $D793_sumteilstk.komplex, ";
$sql.="     $D793_sumteilstk.import, ";
$sql.="     $D793_sumteilstk.tatkz, ";
$sql.="     $D793_sumteilstk.text1,";
$sql.="     $D793_sumteilstk.sum_stk, ";
$sql.="     sum($D793_sumteilstk.sum_auss) as sum_auss, ";
$sql.="     sum($D793_sumteilstk.dm) as preis,";
$sql.="     max($D793_sumteilstk.abgnr) as maxabgnr, ";
$sql.="     $D793_sumteilstk.re_ma ";
//$sql.="     if($D793_sumteilstk.abgnr<2000,'REGEL','MA') as re_ma ";
$sql.=" from $D793_sumteilstk";
$sql.=" group by ";
$sql.="     $D793_sumteilstk.teil, ";
$sql.="     $D793_sumteilstk.import, ";
$sql.="     $D793_sumteilstk.tatkz,";
$sql.="     $D793_sumteilstk.sum_stk";
if($bSortKomplex)
    $sql.=" order by $D793_sumteilstk.komplex,$D793_sumteilstk.teil, $D793_sumteilstk.import, $D793_sumteilstk.abgnr";
else
    $sql.=" order by $D793_sumteilstk.teil, $D793_sumteilstk.import, $D793_sumteilstk.abgnr";


//echo "sql=$sql";
$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

function getBetrag($record){
    $value = round(
                    floatval(($record['sum_stk']+$record['sum_auss'])*$record['preis'])
                    ,4);
    return round($value,4);
}

function getBetragNeu($record){
    $preisNeu = getNeuPreisStk($record);
    $value = round(
                    floatval(($record['sum_stk']+$record['sum_auss'])*$preisNeu)
                    ,4);
    return round($value,4);
}

function getNeuPreisStk($record){
    global $a;
    
    $preis = $record['preis'];
    $abgnr = $record['maxabgnr'];
    $kunde = $record['kunde'];
    $teil = $record['teil'];
    $minpreis = $a->getMinPreisProKunde($kunde);
    
    if($abgnr==406 ||$abgnr==446 ||$abgnr==2346){
        $preis = round($preis*(0.125/0.08),3);
    }
    if($abgnr==2446 ||$abgnr==2546 ||$abgnr==2451){
        $preis = 0;
    }
    if($abgnr==451){
        $vzkd1=$a->getVzAbyProTeilAbgNr($teil, 451);
        $vzkd2=$a->getVzAbyProTeilAbgNr($teil, 2451);
        $preis = round(($vzkd1+$vzkd2)*1.1*$minpreis,3);
    }
    if($abgnr==2351){
        $vzkd2=$a->getVzAbyProTeilAbgNr($teil, 2351);
        $preis = round(($vzkd2)*1.1*$minpreis,3);
    }
    return $preis;
}

$options = array(
    'encoder' => false,
    'rootTag' => 'D793',
    'idColumn' => 'teil',
    'rowTag' => 'teil',
    'elements' => array(
        'teilnr' => 'teil',
        'teilbez',
        'komplex',
        'gew',
        'teillang',
        'importe' => array(
            'rootTag' => 'importe',
            'idColumn' => 'import',
            'rowTag' => 'im',
            'elements' => array(
                'import',
                'tatigkeiten' => array(
                    'rootTag' => 'tatigkeiten',
                    'idColumn' => 'tatkz',
                    'rowTag' => 'tat',
                    'elements' => array(
                        'tatkz',
                        'kusy' => array(
                            'rootTag' => 'kusy',
                            'rowTag' => 'kus',
                            'idColumn' => 'sum_stk',
                            'elements' => array(
                                'teilnr' => 'teil',
                                'tatkz',
                                'text1',
                                'sum_stk',
                                'sum_auss',
                                'preis',
                                'preisNeu' => '#getNeuPreisStk();',
                                'betrag' => '#getBetrag();',
                                'betragNeu' => '#getBetragNeu();',
                                'maxabgnr',
                                're_ma'
                            ),
                        ),
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

	// pohledy se smazou podle jejich poctu definovaneho polem views
	
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}


//$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("D793.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
