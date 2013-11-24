<?php
session_start();
require_once('XML/Query2XML.php');
require_once('DB.php');
require_once "../fns_dotazy.php";


// cast pro vytvoreni XML by mela byt v jinem souboru jmenosestavy_xml.php
$db = &DB::connect('mysql://root:nuredv@localhost/apl');

global $db;
$pcip=get_pc_ip();


$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->query("set names utf8");

if($teil=='*')
	$teilwhere="";
else
	$teilwhere=" and (teil='$teil')";
	
if($tat=='*')
	$tatwhere="";
else
	$tatwhere=" and (drueck.taetnr='$tat')";
	
if($persnr=='*')
	$perswhere="";
else
	$perswhere=" and (drueck.persnr='$persnr')";

	
// vytvorim si nekolik pohledu
$views=array("S311_expal","S311");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT teil,auftragsnr,`pos-pal-nr` as pal from dauftr where ((`auftragsnr-exp`='$export') and (kzgut='G') $teilwhere)";
$pt.=" group by dauftr.teil,dauftr.auftragsnr,pal";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select teil,auftragsnr,taetnr,datum,persnr,`pos-pal-nr` as pal,schicht,oe,stück,`auss-stück`,auss_typ,`vz-soll` as vzkd,`vz-ist` as vzaby,`verb-zeit` as verb,drueck_id ";
//$pt.=" sum(if(auss_typ=4;(`stück`+`auss-stück`)*`vz-soll`;(`stück`)*`vz-soll`)) as sumvzkd,";
//$pt.=" sum(if(auss_typ=4;(`stück`+`auss-stück`)*`vz-ist`;(`stück`)*`vz-ist`)) as sumvzaby";
$pt.=" from drueck";
$pt.=" where (1 $teilwhere $tatwhere $perswhere) order by teil,auftragsnr,taetnr,datum,persnr";

//echo $pt."<br>";
$db->query($pt);

$S311_expal=$pcip.$views[0];
$S311=$pcip.$views[1];

$sql =" select $S311.teil,$S311.auftragsnr,$S311.taetnr,DATE_FORMAT($S311.datum,'%d.%m.%Y') as datum,$S311.persnr,$S311.`stück` as stk,";
$sql.=" $S311.pal,$S311.schicht,$S311.oe,$S311.`auss-stück` as aussstk,$S311.auss_typ,$S311.vzkd,$S311.vzaby,$S311.verb,";
$sql.=" if($S311.auss_typ=4,($S311.`stück`+$S311.`auss-stück`)*$S311.vzkd,($S311.`stück`)*$S311.vzkd) as sumvzkd,";
$sql.=" if($S311.auss_typ=4,($S311.`stück`+$S311.`auss-stück`)*$S311.vzaby,($S311.`stück`)*$S311.vzaby) as sumvzaby,";
$sql.=" $S311.drueck_id,dkopf.gew,dkopf.brgew,DATE_FORMAT(dkopf.`muster-vom`,'%y-%m-%d') as mustervom,dkopf.`muster-platz` as musterplatz,";
$sql.=" name";
$sql.=" from $S311";
$sql.=" join $S311_expal on $S311.auftragsnr=$S311_expal.auftragsnr and $S311.teil=$S311_expal.teil and $S311.pal=$S311_expal.pal ";
$sql.=" join dpers using(persnr)";
$sql.=" join dkopf on $S311.teil=dkopf.teil";
$sql.=" order by $S311.teil,$S311.auftragsnr,$S311.taetnr,$S311.datum,$S311.persnr";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

function get_kurs($wahr,$ausliefer)
{
	//echo "wahr=$wahr,ausliefer=$ausliefer<br>";
	if($wahr!="EUR")
	{
		// podle auslieferdatumu a meny zjistim kurs
		$res=mysql_query("select kurs from dkurs where ((gilt_von<='".$ausliefer."') and (gilt_bis>='".$ausliefer."'))");
		$row=mysql_fetch_array($res);
		//echo "kurs=".$row['kurs']."<br>";
		return $row['kurs'];
	}
	else
	{
		//echo "kurs=1<br>";
		return 1;
	}
}

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
					'rootTag'=>'S311',
					'idColumn'=>'teil',
					'rowTag'=>'dil',
					'elements'=>array(
						'teilnr'=>'teil',
						'gew',
						'brgew',
						'f_muster_platz'=>'#get_muster_platz();',
						'f_muster_vom'=>'#get_muster_vom();',
						'importy'=>array(
							'idColumn'=>'auftragsnr',
							'rowTag'=>'import',
							'elements'=>array(
								'auftragsnr',
								'taetigkeiten'=>array(
								'rootTag'=>'taetigkeiten',
								'rowTag'=>'taetigkeit',
								'idColumn'=>'taetnr',
								'elements'=>array(
									'tat'=>'taetnr',
									'positionen'=>array(
									'rootTag'=>'positionen',
									'rowTag'=>'position',
									'idColumn'=>'drueck_id',
									'elements'=>array(
										'tat'=>'taetnr',
										'datum',
										'persnr',
										'name',
										'pal',
										'schicht',
                                                                                'oe',
										'stk',
										'aussstk',
										'auss_typ',
										'vzkd',
										'vzaby',
										'sumvzkd',
										'sumvzaby',
										'verb'
									),
									),
								),
								),
							),
							),
					)
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


$db->disconnect();


//============================================================+
// END OF FILE                                                 
//============================================================+
$domxml->save("S311.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
