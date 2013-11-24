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

$oeWhere = '';
$oeIWhere = '';

if($oeArray!=FALSE) {
    if((count($oeArray)==1) && (strlen($oeArray[0])==1) && ($oeArray[0]=='*')) {
        // nedelam nic
    }
    else {
        foreach ($oeArray as $oecko) {
            $oeWhere .= " dzeitsoll.oe like '".$oecko."' or";
            $oeIWhere .= " dzeit.tat like '".$oecko."' or";
        }
        if(strlen($oeWhere)>0) {
            // odeberu or na konci retezce
            $oeWhere = substr($oeWhere, 0, strlen($oeWhere)-2);
        }
        if(strlen($oeIWhere)>0) {
            // odeberu or na konci retezce
            $oeIWhere = substr($oeIWhere, 0, strlen($oeIWhere)-2);
        }
        //echo $
        $oeIWhere = strtr($oeIWhere, '*', '%');
        $oeWhere = strtr($oeWhere, '*', '%');
    }
}

$bTestOE = (strlen($oeWhere)>0);
// vytvorim si nekolik pohledu


$pcip=get_pc_ip();

$von = $jahr."-".$monat."-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr."-".$monat."-".$pocetDnuVMesici;

if($bTestOE) {
    $sql = "select dzeitsoll.persnr,dpers.name,dpers.vorname,dzeitsoll.oe,dtattypen.og,DATE_FORMAT(calendar.datum,'%Y-%m-%d') as datum,calendar.cislodne,";
    $sql.=" sum(dzeitsoll.stunden) as stunden";
    $sql.=" from calendar";
    $sql.= " join dzeitsoll on calendar.datum = dzeitsoll.datum ";
    $sql.= " join dpers on dpers.persnr=dzeitsoll.persnr";
    $sql.= " join dtattypen on dtattypen.tat=dzeitsoll.oe";
    $sql.=" where ((calendar.datum between '$von' and '$bis')";
    $sql.=" and (dzeitsoll.persnr between '$persvon' and '$persbis')";
    //$sql.=" and (dzeitsoll.oe <>'-') and (dzeitsoll.oe like '$oe'))";
    $sql.=" and (dzeitsoll.oe <>'-') and ($oeWhere)";
    $sql.=" and (dpers.dpersstatus='MA')";
    $sql.=" and (dpers.kor=0)";
    $sql.=" )";
    $sql.=" group by dzeitsoll.persnr,oe,calendar.datum";
}else {
    $sql = "select dzeitsoll.persnr,dpers.name,dpers.vorname,dzeitsoll.oe,dtattypen.og,DATE_FORMAT(calendar.datum,'%Y-%m-%d') as datum,calendar.cislodne,";
    $sql.=" sum(dzeitsoll.stunden) as stunden";
    $sql.=" from calendar";
    $sql.= " join dzeitsoll on calendar.datum = dzeitsoll.datum ";
    $sql.= " join dpers on dpers.persnr=dzeitsoll.persnr";
    $sql.= " join dtattypen on dtattypen.tat=dzeitsoll.oe";
    $sql.=" where ((calendar.datum between '$von' and '$bis')";
    $sql.=" and (dzeitsoll.persnr between '$persvon' and '$persbis')";
    $sql.=" and (dzeitsoll.oe <>'-')";
    $sql.=" and (dpers.dpersstatus='MA')";
    $sql.=" and (dpers.kor=0)";
    $sql.=" )";
    $sql.=" group by dzeitsoll.persnr,oe,calendar.datum";
}

//echo "sql=$sql"."<br>";

if($bTestOE) {
    $sql_ist = "select dzeit.persnr,dpers.name,dpers.vorname,dzeit.tat as oe,dtattypen.og,DATE_FORMAT(calendar.datum,'%Y-%m-%d') as datum,calendar.cislodne,";
    $sql_ist.=" sum(dzeit.stunden) as stunden";
    $sql_ist.=" from calendar";
    $sql_ist.= " join dzeit on calendar.datum = dzeit.datum ";
    $sql_ist.= " join dpers on dpers.persnr=dzeit.persnr";
    $sql_ist.= " join dtattypen on dtattypen.tat=dzeit.tat";
    $sql_ist.=" where ((calendar.datum between '$von' and '$bis')";
    $sql_ist.=" and (dzeit.persnr between '$persvon' and '$persbis')";
    //$sql_ist.=" and (dzeit.tat <>'-') and (dzeit.tat like '$oe')";
    $sql_ist.=" and (dzeit.tat <>'-') and ($oeIWhere)";
    $sql_ist.=" and (dpers.dpersstatus='MA')";
    $sql_ist.=" and (dpers.kor=0)";
    $sql_ist.=" )";
    $sql_ist.=" group by dzeit.persnr,dzeit.tat,calendar.datum";
}
else {
    $sql_ist = "select dzeit.persnr,dpers.name,dpers.vorname,dzeit.tat as oe,dtattypen.og,DATE_FORMAT(calendar.datum,'%Y-%m-%d') as datum,calendar.cislodne,";
    $sql_ist.=" sum(dzeit.stunden) as stunden";
    $sql_ist.=" from calendar";
    $sql_ist.= " join dzeit on calendar.datum = dzeit.datum ";
    $sql_ist.= " join dpers on dpers.persnr=dzeit.persnr";
    $sql_ist.= " join dtattypen on dtattypen.tat=dzeit.tat";
    $sql_ist.=" where ((calendar.datum between '$von' and '$bis')";
    $sql_ist.=" and (dzeit.persnr between '$persvon' and '$persbis')";
    $sql_ist.=" and (dzeit.tat <>'-')";
    $sql_ist.=" and (dpers.dpersstatus='MA')";
    $sql_ist.=" and (dpers.kor=0)";
    $sql_ist.=" )";
    $sql_ist.=" group by dzeit.persnr,dzeit.tat,calendar.datum";
}

//echo "sql_ist=$sql_ist"."<br>";
// sumy pro mesic, dovolena, naplanovany pocet hodin, stdstunden apod.
$sql_persnr = "select dpersdetail1.dobaurcita,dpers.persnr,dpers.komm_ort,dpers.regelarbzeit,dpers.eintritt,dpers.MAStunden,";
$sql_persnr.= " dpers.stdsoll_datum from dpers";
$sql_persnr.= " left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr ";
//$sql_persnr.= " where dpers.persnr between '$persvon' and '$persbis' and dpers.austritt is null order by dpers.persnr";
$sql_persnr.= " where dpers.persnr between '$persvon' and '$persbis' and dpersstatus='MA' and dpers.kor=0 order by dpers.persnr";

// posle xml do souboru

$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry


$options_soll = array(
		'encoder'=>false,
		'rootTag'=>'soll',
		'idColumn'=>'persnr',
		'rowTag'=>'pers',
		'elements'=>array(
			'persnr',
                        'name',
                        'vorname',
			'oes'=>array(
				'rootTag'=>'oes',
				'rowTag'=>'oe',
				'idColumn'=>'oe',
				'elements'=>array(
					'oekz'=>'oe',
                                        'og',
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


$options_ist = array(
		'encoder'=>false,
		'rootTag'=>'ist',
		'idColumn'=>'persnr',
		'rowTag'=>'pers',
		'elements'=>array(
			'persnr',
                        'name',
                        'vorname',
			'oes'=>array(
				'rootTag'=>'oes',
				'rowTag'=>'oe',
				'idColumn'=>'oe',
				'elements'=>array(
					'oekz'=>'oe',
                                        'og',
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


// vyber vsech lidi podle kriterii v eintrittu
$options_persnr = array(
		'encoder'=>false,
		'rootTag'=>'personalinfo',
		'idColumn'=>'persnr',
		'rowTag'=>'persinfo',
		'elements'=>array(
                                'persnr',
                                'komm_ort',
                                'regelarbzeit',
                                'eintritt',
                                'stdsoll_datum',
                                'dobaurcita',
                                'MAStunden'
                                 ),
                 );


// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML

/**
 *
 *
 * @var DOMDocument
 */
if($typ==1||$typ==3)
    $domxml_soll = $query2xml->getXML($sql,$options_soll);
if($typ==2||$typ==3)
    $domxml_ist = $query2xml->getXML($sql_ist,$options_ist);

$domxml_persinfo = $query2xml->getXML($sql_persnr,$options_persnr);
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

$domxml = new DOMDocument('1.0');

$root = $domxml->createElement('S132');

if($typ==1||$typ==3){
    $nodeToImport = $domxml_soll->getElementsByTagName("soll")->item(0);
    $node = $domxml->importNode($nodeToImport,TRUE);
    $root->appendChild($node);
}


if($typ==2||$typ==3){
    $nodeToImport = $domxml_ist->getElementsByTagName("ist")->item(0);
    $node = $domxml->importNode($nodeToImport,TRUE);
    $root->appendChild($node);
}

    $nodeToImport = $domxml_persinfo->getElementsByTagName("personalinfo")->item(0);
    $node = $domxml->importNode($nodeToImport,TRUE);
    $root->appendChild($node);

// pridam info o kalendari
$sql = "select date_format(datum,'%Y-%m-%d') as datum,date_format(von_f_guss,'%H:%i') as von_f_guss";
$sql.=",date_format(bis_f_guss,'%H:%i') as bis_f_guss";
$sql.=",date_format(von_s_guss,'%H:%i') as von_s_guss";
$sql.=",date_format(bis_s_guss,'%H:%i') as bis_s_guss";
$sql.=",date_format(von_f_ne,'%H:%i') as von_f_ne";
$sql.=",date_format(bis_f_ne,'%H:%i') as bis_f_ne";
$sql.=",date_format(von_s_ne,'%H:%i') as von_s_ne";
$sql.=",date_format(bis_s_ne,'%H:%i') as bis_s_ne";
$sql.=" from calendar where datum between '$von' and '$bis'";

dbConnect();
$res = mysql_query($sql);
while($row = mysql_fetch_assoc($res)){
    $calendarA[$row['datum']] = $row;
}
$calendarElement = $domxml->createElement('calendar');
foreach ($calendarA as $datum=>$calendarinfo){
    $tagElement = $domxml->createElement('tag');
    $datumElement = $domxml->createElement('datum');

    $vonfgussElement = $domxml->createElement('vonfguss');
    $vonsgussElement = $domxml->createElement('vonsguss');
    $vonfneElement = $domxml->createElement('vonfne');
    $vonsneElement = $domxml->createElement('vonsne');

    $bisfgussElement = $domxml->createElement('bisfguss');
    $bissgussElement = $domxml->createElement('bissguss');
    $bisfneElement = $domxml->createElement('bisfne');
    $bissneElement = $domxml->createElement('bissne');


    $datumElement->appendChild($domxml->createTextNode($calendarinfo['datum']));
    $vonfgussElement->appendChild($domxml->createTextNode($calendarinfo['von_f_guss']));
    $vonsgussElement->appendChild($domxml->createTextNode($calendarinfo['von_s_guss']));
    $vonfneElement->appendChild($domxml->createTextNode($calendarinfo['von_f_ne']));
    $vonsneElement->appendChild($domxml->createTextNode($calendarinfo['von_s_ne']));

    $bisfgussElement->appendChild($domxml->createTextNode($calendarinfo['bis_f_guss']));
    $bissgussElement->appendChild($domxml->createTextNode($calendarinfo['bis_s_guss']));
    $bisfneElement->appendChild($domxml->createTextNode($calendarinfo['bis_f_ne']));
    $bissneElement->appendChild($domxml->createTextNode($calendarinfo['bis_s_ne']));


    $tagElement->appendChild($datumElement);
    $tagElement->appendChild($vonfgussElement);
    $tagElement->appendChild($vonsgussElement);
    $tagElement->appendChild($vonfneElement);
    $tagElement->appendChild($vonsneElement);

    $tagElement->appendChild($bisfgussElement);
    $tagElement->appendChild($bissgussElement);
    $tagElement->appendChild($bisfneElement);
    $tagElement->appendChild($bissneElement);

    $calendarElement->appendChild($tagElement);
}
$root->appendChild($calendarElement);

// pridam si definici barev pro jednotlive OE
$sql = "select dtattypen.tat,dtattypen.farbe_rgb from dtattypen";
dbConnect();
mysql_query('set names utf8');
$res = mysql_query($sql);
while($row = mysql_fetch_assoc($res)){
    $rgb = $row['farbe_rgb'];
    if($rgb==null)
        $rgbArray = '255,255,255';
    else{
        $rgbArray = $row['farbe_rgb'];
    }
    $oekey = $row['tat'];
    $oeFarben[$oekey] = $rgbArray;
}

$farbenElement = $domxml->createElement('oefarben');
foreach ($oeFarben as $key=>$value){
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

$domxml->appendChild($root);




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
$domxml->save("S132.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
