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
// pohled s paletama
$views=array("S310_pal");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT teil,auftragsnr,`pos-pal-nr` as pal,fremdauftr,fremdpos from dauftr where ((auftragsnr between '$auftragsnr_von' and '$auftragsnr_bis')) group by auftragsnr,teil,`pos-pal-nr`";
//echo $pt;
$db->query($pt);

$S310_pal_view = $pcip.$views[0];

if($teil=='*')
	$teilwhere="";
else
	$teilwhere="(drueck.teil='$teil')";
	
if($tat=='*')
	$tatwhere="";
else
	$tatwhere="(drueck.taetnr='$tat')";
	
if($persnr=='*')
	$perswhere="";
else
	$perswhere="(drueck.persnr='$persnr')";

$sql=" select drueck_id,drueck.auftragsnr,drueck.teil,taetnr as tat,$S310_pal_view.fremdauftr,$S310_pal_view.fremdpos,drueck.schicht,drueck.oe,DATE_FORMAT(datum,'%d.%m.%Y') as datum,drueck.persnr,name, ";
$sql.=" `pos-pal-nr` as pal,`stück` as stk,`auss-stück` as auss_stk,auss_typ,`vz-soll` as vzkd_stk, ";
$sql.=" `vz-ist` as vzaby_stk,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`) as vzkd, ";
$sql.=" if(auss_typ=4,(`stück`+`auss-stück`)*`vz-ist`,`stück`*`vz-ist`) as vzaby, ";
$sql.=" `verb-zeit` as verb,gew,brgew,`muster-platz` as muster_platz,if(`muster-vom` is not null,DATE_FORMAT(`muster-vom`,'%y-%m-%d'),`muster-vom`) as muster_vom ";
$sql.=" from drueck  join dpers using (persnr) ";
$sql.=" join dkopf on (drueck.teil=dkopf.teil) ";
$sql.= " join $S310_pal_view on $S310_pal_view.auftragsnr=drueck.auftragsnr and $S310_pal_view.teil=drueck.teil and $S310_pal_view.pal=drueck.`pos-pal-nr` ";
$sql.=" where (";
$sql.=" (drueck.auftragsnr between '$auftragsnr_von' and '$auftragsnr_bis')";


if(strlen($teilwhere)>0)
	$sql.=" AND $teilwhere";

if(strlen($tatwhere)>0)
	$sql.=" AND $tatwhere";
	
if(strlen($perswhere)>0)
	$sql.=" AND $perswhere";

$sql.=" )";

$sql.=" order by drueck.auftragsnr,drueck.teil,drueck.taetnr,drueck.datum,drueck.persnr";
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
	if(strlen($record['muster_platz'])>0)
		return $record['muster_platz'];
	else
		return "???";
}

function get_muster_vom($record)
{
	if(strlen($record['muster_vom'])>0)
		return $record['muster_vom'];
	else
		return "??-??-??";
}


$options = array(
                    'encoder'=>false,
                    'rootTag'=>'S310',
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
                                'fremdauftrpositionen'=>array(
                                    'rootTag'=>'fremdauftrpositionen',
                                    'rowTag'=>'fremdauftr',
                                    'idColumn'=>'fremdauftr',
                                    'elements'=>array(
                                        'fremdauftrnr'=>'fremdauftr',
                                        'fremdposnr'=>'fremdpos',
                                        'taetigkeiten'=>array(
                                            'rootTag'=>'taetigkeiten',
                                            'rowTag'=>'taetigkeit',
                                            'idColumn'=>'tat',
                                            'elements'=>array(
                                                'tatnr'=>'tat',
						'positionen'=>array(
                                                    'rootTag'=>'positionen',
                                                    'rowTag'=>'position',
                                                    'idColumn'=>'drueck_id',
                                                    'elements'=>array(
                                                        'tat',
							'datum',
							'pal',
							'schicht',
                                                        'oe',
							'persnr',
							'name',
							'stk',
							'auss_stk',
							'auss_typ',
							'vzkd_stk',
							'vzkd',
							'vzaby_stk',
							'vzaby',
							'verb'
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
$domxml->save("S310.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
