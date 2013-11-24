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

$datvon = $datum;
$timebis = strtotime($datvon);
$timebisPlus6Days = $timebis + 5 * (24*60*60);
$datbis = date('Y-m-d', $timebisPlus6Days);

$oe = str_replace('*', '%', $oe);
if(!strcmp($schicht, '*')) $schicht = '';
if(!strcmp($oe,'%')) $oe = '';

//echo "datvon=$datvon,datbis=$datbis";

//exit;

$pcip=get_pc_ip();

if( (strlen($schicht)>0) && (strlen($oe)>0) )
    $sql = "select dpers.`PersNr` as persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr join dzeitsoll on dzeitsoll.persnr=dpers.`PersNr` where ((dpers.austritt is null or dpers.eintritt>dpers.austritt) and (dpers.`Schicht`='$schicht') and (dzeitsoll.datum between '$datvon' and '$datbis') and (dzeitsoll.oe like '$oe')) order by dpers.`PersNr`";
else if(strlen($schicht)>0){
    $sql = "select dpers.`PersNr` as persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where ((dpers.austritt is null or dpers.eintritt>dpers.austritt) and (dpers.`Schicht`='$schicht')) order by dpers.`PersNr`";
}
else if(strlen($oe)>0){
    $sql = "select dpers.`PersNr` as persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr join dzeitsoll on dzeitsoll.persnr=dpers.`PersNr` where ((dpers.austritt is null or dpers.eintritt>dpers.austritt) and (dzeitsoll.datum between '$datvon' and '$datbis') and (dzeitsoll.oe like '$oe')) order by dpers.`PersNr`";
}
else{
    $sql = "select dpers.`PersNr` as persnr,CONCAT(dpers.`Name`,' ',dpers.`Vorname`) as name,dpers.eintritt,dpers.austritt,dpers.komm_ort as ort,dpersdetail1.kom7 as tel, dpersdetail1.kom4 as marke from dpers left join dpersdetail1 on dpers.`PersNr`=dpersdetail1.persnr where ((dpers.austritt is null or dpers.eintritt>dpers.austritt)) order by dpers.`PersNr`";
}

//echo "datvon=$datvon,datbis=$datbis,oe=$oe,schicht=$schicht<br>";
//echo $sql;
//exit;


$query2xml = XML_Query2XML::factory($db);
	

$options = array(
		'encoder'=>false,
		'rootTag'=>'S1XX1',
		'idColumn'=>'persnr',
		'rowTag'=>'person',
		'elements'=>array(
                    'persnr',
                    'name',
                    'eintritt',
                    'austritt',
                    'ort',
                    'tel',
                    'marke',
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
$domxml->save("S1XX1.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
