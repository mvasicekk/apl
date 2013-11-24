<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";
require_once '../db.php';


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$connectString = "mysql://".LOCAL_USER.":".LOCAL_PASS."@".LOCAL_HOST."/".LOCAL_DB;

$db = &DB::connect($connectString);

global $db;

$a = AplDB::getInstance();

if($k=="aktuell")
    $kurs = number_format($a->getKurs(date('Y-m-d'), 'EUR', 'CZK'), 2);
else
    $kurs = number_format($a->getKurs('2099-12-31', 'EUR', 'CZK'), 2);

$euroMulti = $kurs;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

// vytvorim si nekolik pohledu


$pcip=get_pc_ip();

$sql.=" select ";
$sql.="     drundlauf.*,";
$sql.="     drundlauf.preis*(1-drundlauf.rabatt/100) as kostencz,";
$sql.="     dspediteur.name as spedname,";
$sql.="     if(i.kunde is not null,i.kunde,e.kunde) as kd";
$sql.=" from drundlauf";
$sql.=" left join daufkopf i on i.auftragsnr=drundlauf.im";
$sql.=" left join daufkopf e on e.auftragsnr=drundlauf.ex";
$sql.=" join dspediteur on dspediteur.id=drundlauf.dspediteur_id";
$sql.=" where";
$sql.="     ((drundlauf.ab_aby_soll_datetime between '$terminvonDB' and '$terminbisDB')";
$sql.="	    or (drundlauf.an_kunde_soll_datetime between '$terminvonDB' and '$terminbisDB') ";
$sql.="	    or (drundlauf.an_aby_soll_datetime between '$terminvonDB' and '$terminbisDB') ";
$sql.=" )";
$sql.="     and (drundlauf.dspediteur_id between $spedvon and $spedbis)";
$sql.="     and ((i.kunde between '$kdvon' and '$kdbis') or (e.kunde between '$kdvon' and '$kdbis'))";
if($typ=="Spediteur")
    $sql.=" order by dspediteur_id,kd,ab_aby_soll_datetime";
if($typ=="InfoKD")
    $sql.=" order by kd,ab_aby_soll_datetime";
if($typ=="Dispo")
    $sql.=" order by ab_aby_soll_datetime";


$query2xml = XML_Query2XML::factory($db);
	
function kosten_EUR($record)
{
    global $euroMulti;
    
	if($euroMulti!=0){
	    $kostenCZ = $record['kostencz'];
	    	return $kostenCZ/$euroMulti;
	}
	else
		return 0;
}

function betrag_minus_kosten($record)
{
    global $euroMulti;
    
	if($euroMulti!=0){
	    $kostenCZ = $record['kostencz'];
	    	return $record['betrag']-$kostenCZ/$euroMulti;
	}
	else
		return $record['betrag'];
}


$options = array(
					'encoder'=>false,
					'rootTag'=>'D810',
					'idColumn'=>'id',
					'rowTag'=>'fahrt',
					'elements'=>array(
					    'im',
					    'ex',
					    'ab_aby_ort',
					    'ab_aby_soll_datetime',
					    'ab_aby_soll_datetime',
					    'ab_aby_ist_datetime',
					    'proforma',
					    'dspediteur_id',
					    'spedname',
					    'fahrername',
					    'lkw_kz',
					    'an_kunde_ort',
					    'an_kunde_soll_datetime',
					    'an_kunde_ist_datetime',
					    'an_aby_ort',
					    'an_aby_soll_datetime',
					    'an_aby_ist_datetime',
					    'an_aby_nutzlast',
					    'preis',
					    'rabatt',
					    'betrag',
					    'rechnung',
					    'bemerkung',
					    'kd',
					    'kostencz',
					    'kostenEUR'=>'#kosten_EUR();',
					    'betrag_minus_kosten'=>'#betrag_minus_kosten();',
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
$domxml->save("D810.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
