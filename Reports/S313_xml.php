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


if($teil=='*'){
    $teilwhere="";
    $teilDAwhere="";
}
else{
    $teilwhere="(drueck.teil='$teil')";
    $teilDAwhere="(dauftr.teil='$teil')";
}
	



if($bAlle){
$sql=" select"; 
$sql.="	drueck.drueck_id,";
$sql.="	drueck.auftragsnr,";
$sql.="	drueck.teil,";
$sql.="	drueck.taetnr,";
$sql.="	drueck.schicht,";
$sql.="	drueck.oe,";
$sql.="	DATE_FORMAT(drueck.datum,'%d.%m.%Y') as datum,";
$sql.="	drueck.persnr,";
$sql.="	dpers.name,";
$sql.="	drueck.`pos-pal-nr` as pal,";
$sql.="	drueck.`stück` as stk,";
$sql.="	drueck.`auss-stück` as aussstk,";
$sql.="	drueck.auss_typ,";
$sql.="	drueck.`vz-soll` as vzkd,";
$sql.="	drueck.`vz-ist` as vzaby,";
$sql.="	if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,(`stück`)*`vz-soll`) as sumvzkd,";
$sql.="	if(auss_typ=4,(`stück`+`auss-stück`)*`vz-ist`,(`stück`)*`vz-ist`) as sumvzaby,";
$sql.="	`verb-zeit` as sumverb,";
$sql.="	gew,";
$sql.="	brgew,";
$sql.="	`muster-platz` as musterplatz,";
$sql.="	DATE_FORMAT(`muster-vom`,'%Y-%m-%d') as mustervom";
$sql.=" from drueck";
$sql.=" join dpers using (persnr)";
$sql.=" join dkopf using (teil)";

$sql.=" where (";
$sql.=" (auftragsnr between '$auftragsnr_von' and '$auftragsnr_bis')";
$sql.=" and (`pos-pal-nr` between '$palvon' and '$palbis')";

if(strlen($teilwhere)>0)
	$sql.=" AND $teilwhere";


$sql.=" )";

$sql.=" order by auftragsnr,teil,pal,taetnr,datum,persnr";
    
}
else{
    //view s nevyexportovanyma paletama
    
$views=array("S313_pal");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select";
$pt.="     dauftr.auftragsnr,";
$pt.="     dauftr.`pos-pal-nr` as pal,";
$pt.="     dauftr.teil";
$pt.=" from dauftr";
$pt.=" where (";
$pt.="     (dauftr.`auftragsnr-exp` is null)";
$pt.="     and (dauftr.`pal-nr-exp` is null)";
$pt.="     and (dauftr.KzGut='G')";
$pt.="     and (dauftr.auftragsnr between '$auftragsnr_von' and '$auftragsnr_bis')";
$pt.="     and (dauftr.`pos-pal-nr` between '$palvon' and '$palbis')";
if(strlen($teilDAwhere)>0)
	$pt.=" AND $teilDAwhere";
$pt.=" )";

//echo $pt;
$db->query($pt);

$S313_pal = $pcip.$views[0];

	
$sql=" select"; 
$sql.="	drueck.drueck_id,";
$sql.="	drueck.auftragsnr,";
$sql.="	drueck.teil,";
$sql.="	drueck.taetnr,";
$sql.="	drueck.schicht,";
$sql.="	drueck.oe,";
$sql.="	DATE_FORMAT(drueck.datum,'%d.%m.%Y') as datum,";
$sql.="	drueck.persnr,";
$sql.="	dpers.name,";
$sql.="	drueck.`pos-pal-nr` as pal,";
$sql.="	drueck.`stück` as stk,";
$sql.="	drueck.`auss-stück` as aussstk,";
$sql.="	drueck.auss_typ,";
$sql.="	drueck.`vz-soll` as vzkd,";
$sql.="	drueck.`vz-ist` as vzaby,";
$sql.="	if(auss_typ=4,(drueck.`stück`+drueck.`auss-stück`)*`vz-soll`,(drueck.`stück`)*`vz-soll`) as sumvzkd,";
$sql.="	if(auss_typ=4,(drueck.`stück`+drueck.`auss-stück`)*`vz-ist`,(drueck.`stück`)*`vz-ist`) as sumvzaby,";
$sql.="	`verb-zeit` as sumverb,";
$sql.="	gew,";
$sql.="	brgew,";
$sql.="	`muster-platz` as musterplatz,";
$sql.="	DATE_FORMAT(`muster-vom`,'%Y-%m-%d') as mustervom";
$sql.=" from drueck";
$sql.=" join dpers using (persnr)";
$sql.=" join dkopf on dkopf.teil=drueck.teil";
$sql.=" join $S313_pal on $S313_pal.auftragsnr=drueck.auftragsnr and $S313_pal.pal=drueck.`pos-pal-nr` and $S313_pal.teil=drueck.teil";
$sql.=" where (";
$sql.=" (drueck.auftragsnr between '$auftragsnr_von' and '$auftragsnr_bis')";
$sql.=" and (drueck.`pos-pal-nr` between '$palvon' and '$palbis')";

if(strlen($teilwhere)>0)
	$sql.=" AND $teilwhere";


$sql.=" )";

$sql.=" order by drueck.auftragsnr,drueck.teil,drueck.`pos-pal-nr`,taetnr,datum,persnr";
    
}

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
function vypocti_fac1($record)
{
	if($record['verb']!=0)
		return $record['vzkd']/$record['verb'];
	else
		return 0;
}

function vzkd_stk($record)
{
	if($record['bezstueck']!=0)
		return $record['vzkd']/$record['bezstueck'];
	else
		return 0;
}

function vzaby_stk($record)
{
	if($record['bezstueck']!=0)
		return $record['vzaby']/$record['bezstueck'];
	else
		return 0;
}

function verb_stk($record)
{
	if($record['bezstueck']!=0)
		return $record['verb']/$record['bezstueck'];
	else
		return 0;
}

function get_muster_platz($record)
{
	if(strlen($record['musterplatz'])>0)
		return $record['musterplatz'];
	else
		return "???";
}

function get_muster_vom($record)
{
	if(strlen($record['mustervom'])>0)
		return $record['mustervom'];
	else
		return "??-??-??";
}


$options = array(
					'encoder'=>false,
					'rootTag'=>'S313',
					'idColumn'=>'auftragsnr',
					'rowTag'=>'auftrag',
					'elements'=>array(
						'auftragsnr',
						'teile'=>array(
							'rootTag'=>'teile',
							'rowTag'=>'teil',
							'idColumn'=>'teil',
							'elements'=>array(
								'teilnr'=>'teil',
								'gew',
								'brgew',
								'f_muster_platz'=>'#get_muster_platz();',
								'f_muster_vom'=>'#get_muster_vom();',
								'paletten'=>array(
									'rootTag'=>'paletten',
									'rowTag'=>'palette',
									'idColumn'=>'pal',
									'elements'=>array(
										'pal',
										'taetigkeiten'=>array(
										'rootTag'=>'taetigkeiten',
										'rowTag'=>'taetigkeit',
										'idColumn'=>'taetnr',
										'elements'=>array(
											'tatnr'=>'taetnr',
											'positionen'=>array(
												'rootTag'=>'positionen',
												'rowTag'=>'position',
												'idColumn'=>'drueck_id',
												'elements'=>array(
													'taetnr',
													'datum',
													'pal',
													'schicht',
													'oe',
													'persnr',
													'name',
													'stk',
													'aussstk',
													'auss_typ',
													'vzkd',
													'sumvzkd',
													'vzaby',
													'sumvzaby',
													'sumverb'
												),
											),
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
$domxml->encoding="UTF-8";

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
$domxml->save("S313.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
