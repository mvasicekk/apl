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


$von = sprintf("%04d-%02d-%02d",$jahr,$monat,1);
$bis = sprintf("%04d-%02d-%02d",$jahr,$monat,  cal_days_in_month(CAL_GREGORIAN, $monat, $jahr));

// vytvorim si nekolik pohledu


$pcip=get_pc_ip();

$sql_dzeit = " select";
$sql_dzeit.= "     dzeit.PersNr as persnr,";
$sql_dzeit.= "     CONCAT(dpers.`Name`,' ',dpers.Vorname) as name,";
$sql_dzeit.= "     dzeit.tat as oe,";
$sql_dzeit.= "     DATE_FORMAT(dzeit.Datum,'%Y-%m-%d') as datum,";
$sql_dzeit.= "     sum(dzeit.Stunden) as stunden";
$sql_dzeit.= " from";
$sql_dzeit.= "     dzeit";
$sql_dzeit.= " join dpers on dpers.PersNr=dzeit.PersNr";
$sql_dzeit.= " where";
$sql_dzeit.= "     dzeit.Datum between '$von' and '$bis'";
$sql_dzeit.= "     and dzeit.PersNr between $persvon and $persbis";
$sql_dzeit.= " group by";
$sql_dzeit.= "     dzeit.PersNr,";
$sql_dzeit.= "     dzeit.tat,";
$sql_dzeit.= "     dzeit.Datum";


$sql_essen = " select";
$sql_essen.= "     dzeit.PersNr as persnr,";
$sql_essen.= "     'essen' as oe,";
$sql_essen.= "     DATE_FORMAT(dzeit.Datum,'%Y-%m-%d') as datum,";
$sql_essen.= "     dessen.essen_preis as essen_preis";
$sql_essen.= " from";
$sql_essen.= "     dzeit";
$sql_essen.= " join dpers on dpers.PersNr=dzeit.PersNr";
$sql_essen.= " join dessen on dessen.id_essen=dzeit.id_essen";
$sql_essen.= " where";
$sql_essen.= "     dzeit.Datum between '$von' and '$bis'";
$sql_essen.= "     and dzeit.PersNr between $persvon and $persbis";
$sql_essen.= "     and dzeit.essen<>0";
$sql_essen.= " group by";
$sql_essen.= "     dzeit.PersNr,";
$sql_essen.= "     'essen',";
$sql_essen.= "     dzeit.Datum";

$sql_trans = " select";
$sql_trans.= "     dperstransport.persnr as persnr,";
$sql_trans.= "     dkfz.marke oe,";
$sql_trans.= "     DATE_FORMAT(dperstransport.datum,'%Y-%m-%d') as datum,";
$sql_trans.= "     sum(dperstransport.preis) as trans_preis,";
$sql_trans.= "     count(dperstransport.preis) as trans_count";
$sql_trans.= " from";
$sql_trans.= "     dperstransport";
$sql_trans.= " join dpers on dpers.PersNr=dperstransport.persnr";
$sql_trans.= " join dkfz on dkfz.id=dperstransport.kfz";
$sql_trans.= " where";
$sql_trans.= "     dperstransport.datum between '$von' and '$bis'";
$sql_trans.= "     and dperstransport.persnr between $persvon and $persbis";
$sql_trans.= " group by";
$sql_trans.= "     dperstransport.persnr,";
$sql_trans.= "     dkfz.marke,";
$sql_trans.= "     dperstransport.datum";

//echo "sql_dzeit=".$sql_dzeit;

//$sql_leistung = '';
//$sql_leistung .= "select $v_personen.*,";
//$sql_leistung .= " $v_leistung.og,$v_leistung.oestatus,$v_leistung.oe,$v_leistung.Datum as datum,$v_leistung.sumvzaby,$v_leistung.sumverb";
//$sql_leistung .= " from $v_personen";
//$sql_leistung .= " left join $v_leistung on $v_personen.persnr = $v_leistung.persnr";
//$sql_leistung .= " order by $v_personen.persnr";

//echo "sql_leistung=".$sql_leistung;

//exit ();

$query2xml = XML_Query2XML::factory($db);

//echo $sql."<br>";
// tady se budou tisknout parametry


$options_dzeit = array(
    'encoder'=>false,
    'rootTag'=>'dzeit',
    'idColumn'=>'persnr',
    'rowTag'=>'pers',
    'elements'=>array(
        'persnr',
        'name',
        'oes'=>array(
            'rootTag'=>'oes',
            'rowTag'=>'oe',
            'idColumn'=>'oe',
            'elements'=>array(
                'oekz'=>'oe',
                'tage'=>array(
                    'rootTag'=>'tage',
                    'rowTag'=>'tag',
                    'idColumn'=>'datum',
                    'elements'=>array(
                        'datum',
                        'stunden'
                    ),
                ),
            ),
        ),
    ),
);

$options_essen = array(
    'encoder'=>false,
    'rootTag'=>'essen',
    'idColumn'=>'persnr',
    'rowTag'=>'pers',
    'elements'=>array(
        'persnr',
        'oes'=>array(
            'rootTag'=>'oes',
            'rowTag'=>'oe',
            'idColumn'=>'oe',
            'elements'=>array(
                'oekz'=>'oe',
                'tage'=>array(
                    'rootTag'=>'tage',
                    'rowTag'=>'tag',
                    'idColumn'=>'datum',
                    'elements'=>array(
                        'datum',
                        'essen_preis'
                    ),
                ),
            ),
        ),
    ),
);

$options_trans = array(
    'encoder'=>false,
    'rootTag'=>'trans',
    'idColumn'=>'persnr',
    'rowTag'=>'pers',
    'elements'=>array(
        'persnr',
        'oes'=>array(
            'rootTag'=>'oes',
            'rowTag'=>'oe',
            'idColumn'=>'oe',
            'elements'=>array(
                'oekz'=>'oe',
                'tage'=>array(
                    'rootTag'=>'tage',
                    'rowTag'=>'tag',
                    'idColumn'=>'datum',
                    'elements'=>array(
                        'datum',
                        'trans_preis',
                        'trans_count'
                    ),
                ),
            ),
        ),
    ),
);
//$options_leistung = array(
//        'encoder'=>false,
//        'rootTag'=>'leistung',
//        'idColumn'=>'PersNr',
//        'rowTag'=>'pers',
//        'elements'=>array(
//            'PersNr',
//            'Name',
//            'Vorname',
//            'oes'=>array(
//                'rootTag'=>'oes',
//                'rowTag'=>'oe',
//                'idColumn'=>'oe',
//                'elements'=>array(
//                    'oekz'=>'oe',
//                    'og',
//                    'oestatus',
//                    'tage'=>array(
//                        'rootTag'=>'tage',
//                        'rowTag'=>'tag',
//                        'idColumn'=>'datum',
//                        'elements'=>array(
//                            'datum',
//                            'sumvzaby',
//                            'sumverb',
//                        ),
//                    ),
//                ),
//            ),
//        ),
//);



// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML

/**
 *
 *
 * @var DOMDocument
 */
$domxml_dzeit = $query2xml->getXML($sql_dzeit,$options_dzeit);
$domxml_essen = $query2xml->getXML($sql_essen,$options_essen);
$domxml_trans = $query2xml->getXML($sql_trans,$options_trans);


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

$root = $domxml->createElement('S185');

$nodeToImport = $domxml_dzeit->getElementsByTagName("dzeit")->item(0);
$node = $domxml->importNode($nodeToImport,TRUE);
$root->appendChild($node);


$nodeToImport = $domxml_essen->getElementsByTagName("essen")->item(0);
$node = $domxml->importNode($nodeToImport,TRUE);
$root->appendChild($node);

$nodeToImport = $domxml_trans->getElementsByTagName("trans")->item(0);
$node = $domxml->importNode($nodeToImport,TRUE);
$root->appendChild($node);


// pridam si definici barev pro jednotlive OE
//$sql = "select dtattypen.tat,dtattypen.farbe_rgb from dtattypen";
//dbConnect();
//mysql_query('set names utf8');
//$res = mysql_query($sql);
//while($row = mysql_fetch_assoc($res)) {
//    $rgb = $row['farbe_rgb'];
//    if($rgb==null)
//        $rgbArray = '255,255,255';
//    else {
//        $rgbArray = $row['farbe_rgb'];
//    }
//    $oekey = $row['tat'];
//    $oeFarben[$oekey] = $rgbArray;
//}
//
//$farbenElement = $domxml->createElement('oefarben');
//foreach ($oeFarben as $key=>$value) {
//    $farbeElement = $domxml->createElement('farbe');
//    $oeElement = $domxml->createElement('oe');
//    $rgbElement = $domxml->createElement('rgb');
//    $oeValue = $domxml->createTextNode($key);
//    $rgbValue = $domxml->createTextNode($value);
//    $oeElement->appendChild($oeValue);
//    $rgbElement->appendChild($rgbValue);
//    $farbeElement->appendChild($oeElement);
//    $farbeElement->appendChild($rgbElement);
//    $farbenElement->appendChild($farbeElement);
//}
//$root->appendChild($farbenElement);

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
$domxml->save("S185.xml");
?>
