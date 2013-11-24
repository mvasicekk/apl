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

$pcip=get_pc_ip();
$views=array("pt_S240","pt_S240_summe_lieferung");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dksd.kunden_stat_nr as pg,daufkopf.kunde,drueck.auftragsnr,";
$pt.=" sum(if(stat_nr='S0011',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as kd_S0011,";
$pt.=" sum(if(stat_nr='S0011',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as aby_S0011,";
$pt.=" sum(if(stat_nr='S0011',`verb-zeit`,0)) as verb_S0011,";
$pt.=" sum(if(stat_nr='S0041',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as kd_S0041,";
$pt.=" sum(if(stat_nr='S0041',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as aby_S0041,";
$pt.=" sum(if(stat_nr='S0041',`verb-zeit`,0)) as verb_S0041,";
$pt.=" sum(if(stat_nr='S0051',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as kd_S0051,";
$pt.=" sum(if(stat_nr='S0051',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as aby_S0051,";
$pt.=" sum(if(stat_nr='S0051',`verb-zeit`,0)) as verb_S0051,";
$pt.=" sum(if(stat_nr='S0061',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as kd_S0061,";
$pt.=" sum(if(stat_nr='S0061',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as aby_S0061,";
$pt.=" sum(if(stat_nr='S0061',`verb-zeit`,0)) as verb_S0061,";
$pt.=" sum(if(stat_nr='S0081',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as kd_S0081,";
$pt.=" sum(if(stat_nr='S0081',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as aby_S0081,";
$pt.=" sum(if(stat_nr='S0081',`verb-zeit`,0)) as verb_S0081,";
$pt.=" sum(if(stat_nr='S0091',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as kd_S0091,";
$pt.=" sum(if(stat_nr='S0091',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as aby_S0091,";
$pt.=" sum(if(stat_nr='S0091',`verb-zeit`,0)) as verb_S0091,";
$pt.=" sum(if(stat_nr='X',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as kd_X,";
$pt.=" sum(if(stat_nr='X',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as aby_X,";
$pt.=" sum(if(stat_nr='X',`verb-zeit`,0)) as verb_X,";
$pt.=" sum(if(stat_nr='M',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`),0)) as kd_M,";
$pt.=" sum(if(stat_nr='M',if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`),0)) as aby_M,";
$pt.=" sum(if(stat_nr='M',`verb-zeit`,0)) as verb_M,";
$pt.=" sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`)) as kd_celkem,";
$pt.=" sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`)) as aby_celkem,";
$pt.=" sum(`verb-zeit`) as verb_celkem  from drueck join daufkopf using (auftragsnr) join dksd using (kunde) ";
$pt.=" join `dtaetkz-abg` on (`dtaetkz-abg`.`abg-nr`=drueck.taetnr) ";
$pt.=" where (drueck.datum between '$datum_von' and '$datum_bis') group by pg,daufkopf.kunde,drueck.auftragsnr";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select auftragsnr,sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`)) as kd_summe_lieferung, ";
$pt.=" sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`)) as aby_summe_lieferung, ";
$pt.=" sum(`verb-zeit`) as verb_summe_lieferung  from drueck ";
$pt.=" where (drueck.datum between '$datum_von' and '$datum_bis') group by auftragsnr ";
 
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$pt_S240=$pcip.$views[0];
$pt_S240_summe_lieferung=$pcip.$views[1];

$sql=" SELECT $pt_S240.pg, $pt_S240.kunde, $pt_S240.auftragsnr, $pt_S240.kd_S0011, $pt_S240.aby_S0011, ";
$sql.=" $pt_S240.verb_S0011, $pt_S240.kd_S0041, $pt_S240.aby_S0041, $pt_S240.verb_S0041, $pt_S240.kd_S0051, ";
$sql.=" $pt_S240.aby_S0051, $pt_S240.verb_S0051, $pt_S240.kd_S0061, $pt_S240.aby_S0061, $pt_S240.verb_S0061, ";
$sql.=" $pt_S240.kd_S0081, $pt_S240.aby_S0081, $pt_S240.verb_S0081, $pt_S240.kd_S0091, $pt_S240.aby_S0091, ";
$sql.=" $pt_S240.verb_S0091, $pt_S240.kd_X, $pt_S240.aby_X, $pt_S240.verb_X, $pt_S240.kd_M, $pt_S240.aby_M, ";
$sql.=" $pt_S240.verb_M, $pt_S240.kd_celkem, $pt_S240.aby_celkem, $pt_S240.verb_celkem, ";
$sql.=" $pt_S240_summe_lieferung.kd_summe_lieferung, $pt_S240_summe_lieferung.aby_summe_lieferung, ";
$sql.=" $pt_S240_summe_lieferung.verb_summe_lieferung";
$sql.=" FROM $pt_S240 INNER JOIN $pt_S240_summe_lieferung ON $pt_S240.auftragsnr = $pt_S240_summe_lieferung.auftragsnr";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

function get_ex_datum_soll($record)
{
	$sollauftrag=substr($record['termin'],1);
	$res=mysql_query("select DATE_FORMAT(ex_datum_soll,'%y-%m-%d %H:%i') as ex_datum_soll from daufkopf where (auftragsnr='$sollauftrag')");
	$row=mysql_fetch_array($res);
	//echo "kurs=".$row['kurs']."<br>";
	return $row['ex_datum_soll'];
}



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

function gew_geplant($record)
{
	return $record['Gew']*$record['stk_ursprung'];
}

function delta_kd($record)
{
	return $record['kd_summe_lieferung']-$record['kd_celkem'];
}

function delta_aby($record)
{
	return $record['aby_summe_lieferung']-$record['aby_celkem'];
}

function delta_verb($record)
{
	return $record['verb_summe_lieferung']-$record['verb_celkem'];
}

$options = array(
					'rootTag'=>'S240',
					'idColumn'=>'pg',
					'rowTag'=>'produkt_gruppe',
					'elements'=>array(
						'pg',
						'kunden'=>array(
							'rootTag'=>'kunden',
							'rowTag'=>'kunde',
							'idColumn'=>'kunde',
							'elements'=>array(
								'kundenr'=>'kunde',
								'auftraege'=>array(
									'rootTag'=>'auftraege',
									'rowTag'=>'auftrag',
									'idColumn'=>'auftragsnr',
									'elements'=>array(
										'auftragsnr',
										'vzkd_minuten'=>array(
											'rootTag'=>'vzkd_minuten',
											'rowTag'=>'vzkd_min',
											'idColumn'=>'auftragsnr',
											'elements'=>array(
												'auftragsnr',
												'kd_S0011',
												'kd_S0041',
												'kd_S0051',
												'kd_S0061',
												'kd_S0081',
												'kd_S0091',
												'kd_X',
												'kd_M',
												'kd_celkem',
												'kd_summe_lieferung',
												'delta_kd'=>'#delta_kd();'
											)
										),
										'vzaby_minuten'=>array(
											'rootTag'=>'vzaby_minuten',
											'rowTag'=>'vzaby_min',
											'idColumn'=>'auftragsnr',
											'elements'=>array(
												'aby_S0011',
												'aby_S0041',
												'aby_S0051',
												'aby_S0061',
												'aby_S0081',
												'aby_S0091',
												'aby_X',
												'aby_M',
												'aby_celkem',
												'aby_summe_lieferung',
												'delta_aby'=>'#delta_aby();'
											)
										),
										'verb_minuten'=>array(
											'rootTag'=>'verb_minuten',
											'rowTag'=>'verb_min',
											'idColumn'=>'auftragsnr',
											'elements'=>array(
												'verb_S0011',
												'verb_S0041',
												'verb_S0051',
												'verb_S0061',
												'verb_S0081',
												'verb_S0091',
												'verb_X',
												'verb_M',
												'verb_celkem',
												'verb_summe_lieferung',
												'delta_verb'=>'#delta_verb();'
											)
										)
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
//$domxml->save("S240.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
