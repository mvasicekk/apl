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

/*
$pcip=get_pc_ip();
$views=array("v_dauftr","v_drueck");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT dauftr.auftragsnr,teil,minpreis,sum(`St�ck`) as stkimport FROM `dauftr` ";
$pt.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$pt.=" WHERE ((dauftr.auftragsnr='".$auftragsnr."') and (kzgut='G')) group by dauftr.auftragsnr,teil,minpreis";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select `AuftragsNr`,";
$pt.=" `Teil`,`TaetNr`,";
$pt.=" sum(`St�ck`) as gutstk,";
$pt.=" sum(if(auss_typ=2,`Auss-St�ck`,0)) as auss2,";
$pt.=" sum(if(auss_typ=4,`Auss-St�ck`,0)) as auss4,";
$pt.=" sum(if(auss_typ=6,`Auss-St�ck`,0)) as auss6,";
$pt.=" sum(if(auss_typ=4,(`St�ck`+`Auss-St�ck`)*`VZ-SOLL`,(`St�ck`*`VZ-SOLL`)) ) as vzkd,";
$pt.=" sum(if(auss_typ=4,(`St�ck`+`Auss-St�ck`)*`VZ-IST`,(`St�ck`*`VZ-IST`)) ) as vzaby,";
$pt.=" sum(`Verb-Zeit`) as verb,";
$pt.=" max(`VZ-SOLL`) as vzkd_stk,";
$pt.=" if(sum(`Verb-Zeit`)<>0,sum(if(auss_typ=4,(`St�ck`+`Auss-St�ck`)*`VZ-SOLL`,(`St�ck`*`VZ-SOLL`)) )/sum(`Verb-Zeit`),0) as factor";
$pt.=" from drueck where ((auftragsnr='".$auftragsnr."')) group by auftragsnr,teil,taetnr";
 
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$v_dauftr=$pcip.$views[0];
$v_drueck=$pcip.$views[1];
*/

$sql=" SELECT drueck_id,dpers.Name,dpers.Vorname,drueck.AuftragsNr,drueck.Teil, dkopf.Teilbez,";
$sql.=" drueck.TaetNr,drueck.`Stück` as stk, if(`auss_typ`=4,(`Stück`+`Auss-Stück`)*`VZ-SOLL`,`Stück`*`VZ-SOLL`) AS vzkd, ";
$sql.=" if(`auss_typ`=4,(`Stück`+`Auss-Stück`)*`VZ-IST`,`Stück`*`VZ-IST`) AS vzaby, ";
$sql.=" drueck.`Verb-Zeit` as verb,drueck.PersNr,drueck.Datum as datum,drueck.`pos-pal-nr` as pal, ";
$sql.=" DATE_FORMAT(drueck.`verb-von`,'%H:%i') as von,DATE_FORMAT(drueck.`verb-bis`,'%H:%i') as bis,drueck.`marke-aufteilung` as ma, drueck.Schicht as drueck_schicht,";
$sql.=" drueck.`auss_typ`,drueck.`Auss-Stück` as auss_stk, dpers.Schicht as dpers_schicht,";
$sql.=" If(`auss_typ`=4,(`Stück`+`Auss-Stück`),`Stück`) AS bezstueck, dkopf.kunde ";
$sql.=" FROM dkopf JOIN (dpers JOIN drueck ON dpers.PersNr = drueck.PersNr) ON dkopf.Teil = drueck.Teil ";
$sql.=" WHERE ((drueck.persnr between '".$pers_von."' and '".$pers_bis."') ";
$sql.=" AND (drueck.Datum Between '".$datum_von."' And '".$datum_bis."') ";
$sql.=" AND (drueck.schicht Between '".$schicht_von."' And '".$schicht_bis."'))";
$sql.=" order by datum,persnr,auftragsnr,von,teil,taetnr";
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

$options = array(
					'encoder'=>false,
					'rootTag'=>'S280',
					'idColumn'=>'datum',
					'rowTag'=>'datumy',
					'elements'=>array(
						'datum',
						'personal'=>array(
							'rootTag'=>'personal',
							'rowTag'=>'pers',
							'idColumn'=>'PersNr',
							'elements'=>array(
								'PersNr',
								'Name',
								'Vorname',
								'schicht'=>'dpers_schicht',
								'auftraege'=>array(
									'rootTag'=>'auftraege',
									'rowTag'=>'auftrag',
									'idColumn'=>'AuftragsNr',
									'elements'=>array(
										'AuftragsNr',
										'positionen'=>array(
											'rootTag'=>'positionen',
											'rowTag'=>'position',
											'idColumn'=>'drueck_id',
											'elements'=>array(
												'Teil',
												'pal',
												'Teilbez',
												'drueck_schicht',
												'TaetNr',
												'stk',
												'auss_stk',
												'auss_typ',
												'vzkd_stk'=>'#vzkd_stk();',
												'vzaby_stk'=>'#vzaby_stk();',
												'verb_stk'=>'#verb_stk();',
												'vzkd',
												'vzaby',
												'verb',
												'ma',
												'von',
												'bis'
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
//$domxml->save("S282.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
