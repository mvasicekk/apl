<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";
require_once "../db.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");


$oeWhere = '';
$drueckOeWhere = '';

if($oeArray!=FALSE) {
    if(count($oeArray)==1 && $oeArray[0]=='*') {
        // nedelam nic
    }
    else {
        foreach ($oeArray as $oecko) {
            $oeWhere .= " dzeit.tat like '".$oecko."' or";
            $drueckOeWhere .= " drueck.oe like '".$oecko."' or";
        }
        if(strlen($oeWhere)>0) {
            // odeberu or na konci retezce
            $oeWhere = substr($oeWhere, 0, strlen($oeWhere)-2);
        }
        if(strlen($drueckOeWhere)>0) {
            // odeberu or na konci retezce
            $drueckOeWhere = substr($drueckOeWhere, 0, strlen($drueckOeWhere)-2);
        }

        $oeWhere = strtr($oeWhere, '*', '%');
        $drueckOeWhere = strtr($drueckOeWhere, '*', '%');
    }
}


// vytvorim si nekolik pohledu


$pcip=get_pc_ip();

$views=array("v_personen","v_dzeit","v_leistung");

$von = $jahr."-".$monat."-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr."-".$monat."-".$pocetDnuVMesici;

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt = '';
$pt .= "create view $viewname as ";
$pt .= "select dpers.`PersNr`,dpers.`Name`,dpers.`Vorname`,dpers.austritt,dpers.eintritt,dpers.regeloe,";
$pt .= "     max(dpersvertrag.eintritt) as eintritt_letzt,";
$pt .= "     max(dpersvertrag.austritt) as austritt_letzt,";
$pt .= "     max(dpersvertrag.befristet) as befristet_letzt,";
$pt .= "     count(dpersvertrag.eintritt) as eintritt_count";
$pt .= "    ,durlaub1.jahranspruch";
$pt .= "    ,durlaub1.rest";
$pt .= "    ,durlaub1.gekrzt";
$pt .= " ,'$bis' as datumbis";
$pt .= " ,'$von' as datumvon";
$pt .= " from dpers";
$pt .= " left join dpersvertrag on dpersvertrag.persnr=dpers.persnr";
$pt .= " left join durlaub1 on durlaub1.`PersNr`=dpers.`PersNr`";
$pt .= " where ((dpers.persnr between '$persvon' and '$persbis')";
//$pt .= " and (dpers.austritt is null or dpers.austritt<dpers.eintritt or dpers.austritt between '$von' and '$bis') and (dpers.persnr<>69052 and dpers.persnr<>69058 and dpers.persnr<>69061 and dpers.persnr<>69093))";
$pt .= " and (dpers.austritt is null or dpers.austritt<dpers.eintritt or dpers.austritt between '$von' and '$bis' or dpers.austritt>='$bis'))";
$pt .= " group by dpers.persnr";
//echo "pt=".$pt."<br>";
$db->query($pt);


$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt = '';
$pt .= "create view $viewname as ";
$pt .= "select dzeit.`PersNr`,dzeit.tat,dtattypen.og,dtattypen.oestatus,dzeit.`Datum`,sum(dzeit.`Stunden`) as sumstunden from dzeit";
$pt .= " join dtattypen on dzeit.tat=dtattypen.tat";
$pt .= " where ((dzeit.`Datum` between '$von' and '$bis')";
if(strlen($oeWhere)>0)
    $pt.=" and ($oeWhere)";
$pt .= " and (dzeit.`PersNr` between '$persvon' and '$persbis'))";
$pt .= " group by dzeit.`PersNr`,dzeit.tat,dzeit.`Datum`";
//echo "pt=".$pt."<br>";
$db->query($pt);


$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt = '';
$pt .= "create view $viewname as ";
$pt .= "select drueck.`PersNr`,drueck.oe,dtattypen.og,dtattypen.oestatus,drueck.`Datum`,";
$pt .= "sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`))/60 as sumvzaby,";
$pt .= "sum(drueck.`Verb-Zeit`)/60 as sumverb";
$pt .= " from drueck";
$pt .= " join dtattypen on drueck.oe=dtattypen.tat";
$pt .= " where ((`Datum` between '$von' and '$bis')";
if(strlen($drueckOeWhere)>0)
    $pt.=" and ($drueckOeWhere)";
$pt .= " and (`PersNr` between '$persvon' and '$persbis'))";
$pt .= " group by drueck.`PersNr`,drueck.oe,drueck.`Datum`";
//echo "pt=".$pt."<br>";
$db->query($pt);

$v_personen = $pcip.$views[0];
$v_dzeit = $pcip.$views[1];
$v_leistung = $pcip.$views[2];

$sql_dzeit = '';
$sql_dzeit .= "select $v_personen.*,";
$sql_dzeit .= " $v_dzeit.og,$v_dzeit.oestatus,$v_dzeit.tat,$v_dzeit.Datum as datum,$v_dzeit.sumstunden";
$sql_dzeit .= " from $v_personen";
$sql_dzeit .= " left join $v_dzeit on $v_personen.persnr = $v_dzeit.persnr";
$sql_dzeit .= " order by $v_personen.persnr";

//echo "sql_dzeit=".$sql_dzeit;

$sql_leistung = '';
$sql_leistung .= "select $v_personen.*,";
$sql_leistung .= " $v_leistung.og,$v_leistung.oestatus,$v_leistung.oe,$v_leistung.Datum as datum,$v_leistung.sumvzaby,$v_leistung.sumverb";
$sql_leistung .= " from $v_personen";
$sql_leistung .= " left join $v_leistung on $v_personen.persnr = $v_leistung.persnr";
$sql_leistung .= " order by $v_personen.persnr";

//echo "sql_leistung=".$sql_leistung;

//exit ();

$query2xml = XML_Query2XML::factory($db);

//echo $sql."<br>";
// tady se budou tisknout parametry



function offen($record){
    $gen = urlaubgenom($record);
    // 2010-01-05 gekrzt se bude zadavat s opacnym znamenkem, tedy kratim dovolenou o 3 dny - zadam -3
    $offen = $record['jahranspruch']+$record['rest']+$record['gekrzt']-$gen;
    return $offen;
}

$options_dzeit = array(
    'encoder'=>false,
    'rootTag'=>'dzeit',
    'idColumn'=>'PersNr',
    'rowTag'=>'pers',
    'elements'=>array(
        'PersNr',
        'Name',
        'Vorname',
        'eintritt_letzt',
        'austritt_letzt',
        'befristet_letzt',
        'eintritt_count',
        'jahranspruch',
        'rest',
        'gekrzt',
//        'genom'=>'#urlaubgenom();',
//        'offen'=>'#offen();',
        'oes'=>array(
            'rootTag'=>'oes',
            'rowTag'=>'oe',
            'idColumn'=>'tat',
            'elements'=>array(
                'oekz'=>'tat',
                'og',
                'oestatus',
                'tage'=>array(
                    'rootTag'=>'tage',
                    'rowTag'=>'tag',
                    'idColumn'=>'datum',
                    'elements'=>array(
                        'datum',
                        'sumstunden'
                    ),
                ),
            ),
        ),
    ),
);

$options_leistung = array(
        'encoder'=>false,
        'rootTag'=>'leistung',
        'idColumn'=>'PersNr',
        'rowTag'=>'pers',
        'elements'=>array(
            'PersNr',
            'Name',
            'Vorname',
            'eintritt_letzt',
            'austritt_letzt',
            'befristet_letzt',
            'eintritt_count',
            'jahranspruch',
            'rest',
            'gekrzt',
//            'genom'=>'#urlaubgenom();',
//            'offen'=>'#offen();',
            'oes'=>array(
                'rootTag'=>'oes',
                'rowTag'=>'oe',
                'idColumn'=>'oe',
                'elements'=>array(
                    'oekz'=>'oe',
                    'og',
                    'oestatus',
                    'tage'=>array(
                        'rootTag'=>'tage',
                        'rowTag'=>'tag',
                        'idColumn'=>'datum',
                        'elements'=>array(
                            'datum',
                            'sumvzaby',
                            'sumverb',
                        ),
                    ),
                ),
            ),
        ),
);



// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML

/**
 *
 *
 * @var DOMDocument
 */
$domxml_dzeit = $query2xml->getXML($sql_dzeit,$options_dzeit);
$domxml_leistung = $query2xml->getXML($sql_leistung,$options_leistung);


// pokusy s polem parameters
// rozkouskovat si ho na jednotlive polozky
// a vytvorim pole parametru
// TODO doplnit nejakou moznost zformatovani, napr. datum zadane ve formulari by melo nejak vypadat
// mozna by bylo lepsi resit to AJAXem uz pri zadavani do formulare
// ale uz pri generovani formulare budu muset pridat handlery podle planovaneho obsahu formulare

foreach($parameters as $var=>$value) {

// pokud nazev parametru obsahuje _label, budu hledat hodnotu parametru
    if(strpos($var,"_label")) {
        $p[$value]=$last_value;
    }
    $last_value=$value;
//$promenne.=$var."=".$value."&";
}

// v promenne p bych mel mit seznam parametru, pridam ho do XML jako node do domxml
//

$domxml = new DOMDocument('1.0');

$root = $domxml->createElement('S122');

$nodeToImport = $domxml_dzeit->getElementsByTagName("dzeit")->item(0);
$node = $domxml->importNode($nodeToImport,TRUE);
$root->appendChild($node);


$nodeToImport = $domxml_leistung->getElementsByTagName("leistung")->item(0);
$node = $domxml->importNode($nodeToImport,TRUE);
$root->appendChild($node);


// pridam si definici barev pro jednotlive OE
$sql = "select dtattypen.tat,dtattypen.farbe_rgb from dtattypen";
dbConnect();
mysql_query('set names utf8');
$res = mysql_query($sql);
while($row = mysql_fetch_assoc($res)) {
    $rgb = $row['farbe_rgb'];
    if($rgb==null)
        $rgbArray = '255,255,255';
    else {
        $rgbArray = $row['farbe_rgb'];
    }
    $oekey = $row['tat'];
    $oeFarben[$oekey] = $rgbArray;
}

$farbenElement = $domxml->createElement('oefarben');
foreach ($oeFarben as $key=>$value) {
    $farbeElement = $domxml->createElement('farbe');
    $oeElement = $domxml->createElement('oe');
    $rgbElement = $domxml->createElement('rgb');
    $oeValue = $domxml->createTextNode($key);
    $rgbValue = $domxml->createTextNode($value);
    $oeElement->appendChild($oeValue);
    $rgbElement->appendChild($rgbValue);
    $farbeElement->appendChild($oeElement);
    $farbeElement->appendChild($rgbElement);
    $farbenElement->appendChild($farbeElement);
}
$root->appendChild($farbenElement);

$element=$domxml->createElement("parameters");
$root->appendChild($element);
$i=1;
foreach($p as $var=>$value) {
    $poradinode=$domxml->createElement("N".$i);
    $labelnode=$domxml->createElement("label",$var);
    $valuenode=$domxml->createElement("value",$value);
    $element->appendChild($poradinode);
    $poradinode->appendChild($labelnode);
    $poradinode->appendChild($valuenode);
    $i++;
}

$domxml->appendChild($root);


// smazu pouzite pohledy
for($i=0;$i<sizeof($views);$i++) {
    $viewname=$pcip.$views[$i];
    $sql="drop view ". $viewname;
    $db->query($sql);
//echo $sql."<br>";
}


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S122.xml");
?>
