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

if($teil!='%')
{
	$teilwhere=" and (dauftr.teil='$teil')";
}
else
{
	$teilwhere="";
}
	
$sql.=" select";
$sql.="     dauftr.auftragsnr as import,";
$sql.="     DATE_FORMAT(daufkopf.aufdat,'%Y-%m.%d') as aufdat,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.`pos-pal-nr` as pal,";
$sql.="     dauftr.abgnr,";
$sql.="     sum(drueck.`Stück`) as stk_gut,";
$sql.="     dauftr.`stück` as stk_import,";
$sql.="     sum(if(drueck.auss_typ=2,drueck.`Auss-Stück`,0)) as auss2,";
$sql.="     sum(if(drueck.auss_typ=4,drueck.`Auss-Stück`,0)) as auss4,";
$sql.="     sum(if(drueck.auss_typ=6,drueck.`Auss-Stück`,0)) as auss6,";
$sql.="     sum(if(drueck.auss_typ=4,(drueck.`Auss-Stück`+drueck.`Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as vzkd,";
$sql.="     sum(if(drueck.auss_typ=4,(drueck.`Auss-Stück`+drueck.`Stück`)*drueck.`VZ-IST`,(drueck.`Stück`)*drueck.`VZ-IST`)) as vzaby,";
$sql.="     sum(drueck.`Verb-Zeit`) as verb";
$sql.=" from";
$sql.="     dauftr";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.=" join drueck on drueck.AuftragsNr=dauftr.auftragsnr and drueck.`pos-pal-nr`=dauftr.`pos-pal-nr` and drueck.Teil=dauftr.teil and drueck.TaetNr=dauftr.abgnr";
$sql.=" where";
$sql.="     (dauftr.auftragsnr between '$auftragsnr_von' and '$auftragsnr_bis')";
$sql.="     and (dauftr.`auftragsnr-exp` is null) $teilwhere";
$sql.=" group by";
$sql.="     dauftr.auftragsnr,";
$sql.="     dauftr.teil,";
$sql.="     dauftr.`pos-pal-nr`,";
$sql.="     dauftr.abgnr";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
function spocti_factor($record)
{
	if($record['verb']!=0)
		return $record['vzkd']/$record['verb'];
	else
		return 0;
}

$options = array(
    'rootTag' => 'S220noex',
    'idColumn' => 'import',
    'rowTag' => 'auftraege',
    'elements' => array(
	'import',
	'aufdat',
	'teile' => array(
	    'rootTag' => 'teile',
	    'rowTag' => 'teil',
	    'idColumn' => 'teil',
	    'elements' => array(
		'teilnr' => 'teil',
		'stk_import',
		'paletten' => array(
		    'rootTag' => 'paletten',
		    'rowTag' => 'palette',
		    'idColumn' => 'pal',
		    'elements' => array(
			'pal',
			'stk_import',
			'taetigkeiten' => array(
			    'rootTag' => 'taetigkeiten',
			    'rowTag' => 'taetigkeit',
			    'idColumn' => 'abgnr',
			    'elements' => array(
				'abgnr',
				'stk_gut',
				'auss2',
				'auss4',
				'auss6',
				'vzkd',
				'vzaby',
				'verb',
//							'preis',
//							'factor'=>'#spocti_factor();'
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


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S220noex.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
