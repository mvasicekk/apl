<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";

// cast pro vytvoreni XML by mela byt v jinaem souboru jmenosestavy_xml.php
$connectString = "mysql://".LOCAL_USER.":".LOCAL_PASS."@".LOCAL_HOST."/".LOCAL_DB;

//$db = &DB::connect('mysql://root:nuredv@localhost/apl');
$db = &DB::connect($connectString);

global $db;

$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

$pcip=get_pc_ip();

$query2xml = XML_Query2XML::factory($db);

$sql="select ";
$sql.="     drundlauf.*,";
$sql.="	    dspediteur.`name` as spediteurname,";
$sql.="     drundlaufimex.imex,";
$sql.="     drundlaufimex.auftragsnr,";
$sql.="	    if(imex='E',zielorte.zielort,'') as zielort,";
$sql.="	    if(imex='E',daufkopf.ex_datum_soll,'') as exsoll,";
$sql.="     drundlaufimex.id as imexid";
$sql.=" from drundlauf";
$sql.=" join dspediteur on dspediteur.id=drundlauf.dspediteur_id";
$sql.=" left join drundlaufimex on drundlaufimex.rundlauf_id=drundlauf.id";
$sql.=" left join daufkopf on daufkopf.auftragsnr=drundlaufimex.auftragsnr";
$sql.=" left join zielorte on zielorte.id=daufkopf.zielort_id";
$sql.=" where";
$sql.="     (drundlauf.ab_aby_soll_datetime between '$von' and '$bis')";
//$sql.="     or";
//$sql.="     drundlauf.an_aby_soll_datetime between '$von' and '$bis')";
$sql.="     and (drundlauf.dspediteur_id between '$spedvon' and '$spedbis')";
$sql.=" order by";
$sql.="     drundlauf.ab_aby_soll_datetime,";
$sql.="     drundlauf.id,";
$sql.="     drundlaufimex.imex,";
$sql.="     drundlaufimex.auftragsnr";

$options = array(
    'encoder' => false,
    'rootTag' => 'D811',
    'rowTag' => 'rundlauf',
    'idColumn' => 'id',
    'elements' => array(
	'id',
	'ab_aby_ort',
	'ab_aby_soll_datetime',
	'ab_aby_ist_datetime',
	'proforma',
	'dspediteur_id',
	'spediteurname',
	'fahrername',
	'lkw_kz',
	'naves_kz',
	'an_kunde_ort',
	'an_kunde_ort_id',
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
	'payload' => array(
	    'rootTag' => 'payload',
	    'rowTag' => 'pay',
	    'idColumn' => 'imexid',
	    'elements' => array(
		'imex',
		'auftragsnr',
		'zielort',
		'exsoll',
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
$domxml->save("D811.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
