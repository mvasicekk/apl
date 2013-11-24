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
$views=array("pt_S214_geplannt_kdminsoll","pt_S214_geplannt_ursprungstk","pt_S214_ohneEx_minuten_aus_DRUECK");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT termin,dauftr.AuftragsNr,DATE_FORMAT(aufdat,'%Y-%m-%d') as aufdat, fremdauftr,Teil,`pos-pal-nr`,abgnr, sum(`Stück`*vzkd) as sumvzkdgeplannt  FROM dauftr  ";
$pt.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr ";
if($statnr!='*')
    $pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=dauftr.abgnr";
$pt.=" WHERE ((dauftr.`auftragsnr-exp` Is Null) AND (dauftr.`pal-nr-exp` Is Null) ";
if($statnr!='*')
    $pt.=" and (`dtaetkz-abg`.Stat_Nr='$statnr')";
$pt.=" and (dauftr.termin between '$gepl_von' and '$gepl_bis') and (dauftr.abgnr between '$abgnrvon' and '$abgnrbis')) GROUP BY termin,dauftr.AuftragsNr, fremdauftr,Teil,`pos-pal-nr`,abgnr";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT AuftragsNr, Teil,`pos-pal-nr`, fremdauftr as maxoffremdauftr,sum(`Stück`) as sumursprung ";
$pt.=" FROM dauftr WHERE (((dauftr.`auftragsnr-exp`) Is Null) AND ((dauftr.`pal-nr-exp`) Is Null) and ";
$pt.=" ((dauftr.termin) between '$gepl_von' and '$gepl_bis')  AND  (kzgut='G')) GROUP BY AuftragsNr, fremdauftr,Teil,`pos-pal-nr`";
 
$db->query($pt);

$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select DRUECK.AuftragsNr, DRUECK.Teil,drueck.`pos-pal-nr`, DRUECK.TaetNr, sum(drueck.`Stück`) as stk, ";
$pt.=" sum(if(auss_typ=4,(drueck.`Stück`+`auss-Stück`)*`vz-soll`,drueck.`Stück`*`vz-soll`)) as sumvzkd,  ";
$pt.=" sum(if(auss_typ=4,(drueck.`Stück`+`auss-Stück`)*`vz-ist`,drueck.`Stück`*`vz-ist`)) as sumvzaby,  ";
$pt.=" Sum(DRUECK.`Verb-Zeit`) AS sumverb, sum(if(auss_typ=2,`auss-Stück`,0)) as auss2, ";
$pt.=" sum(if(auss_typ=4,`auss-Stück`,0)) as auss4, sum(if(auss_typ=6,`auss-Stück`,0)) as auss6 ";
$pt.=" FROM DRUECK INNER JOIN dauftr ON (DRUECK.`pos-pal-nr` = dauftr.`pos-pal-nr`) and ";
$pt.=" (DRUECK.TaetNr = dauftr.abgnr) AND (DRUECK.`pos-pal-nr` = dauftr.`pos-pal-nr`) and (DRUECK.Teil = dauftr.Teil) AND  (DRUECK.AuftragsNr = dauftr.AuftragsNr) ";
if($statnr!='*')
$pt.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
$pt.=" WHERE ((dauftr.termin between '$gepl_von' and '$gepl_bis')";
if($statnr!='*')
    $pt.=" and (`dtaetkz-abg`.Stat_Nr='$statnr')";
$pt.=" and (dauftr.`auftragsnr-exp` Is Null) AND (dauftr.`pal-nr-exp` Is Null)  and (dauftr.abgnr between '$abgnrvon' and '$abgnrbis')) GROUP BY DRUECK.AuftragsNr, DRUECK.Teil, drueck.`pos-pal-nr`,DRUECK.TaetNr";
 
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$pt_S214_geplannt_kdminsoll=$pcip.$views[0];
$pt_S214_geplannt_ursprungstk=$pcip.$views[1];
$pt_S214_ohneEx_minuten_aus_DRUECK=$pcip.$views[2];

$sql=" SELECT $pt_S214_geplannt_kdminsoll.termin, $pt_S214_geplannt_ursprungstk.AuftragsNr, $pt_S214_geplannt_kdminsoll.aufdat,$pt_S214_geplannt_ursprungstk.maxoffremdauftr, ";
$sql.=" $pt_S214_geplannt_ursprungstk.Teil,$pt_S214_geplannt_kdminsoll.abgnr, ";
$sql.=" dkopf.`Muster-Platz` musterplatz,";
$sql.=" Sum($pt_S214_geplannt_ursprungstk.sumursprung) AS stk_ursprung,";
$sql.=" Sum($pt_S214_ohneEx_minuten_aus_DRUECK.stk) AS stk_drueck, ";
$sql.=" sum($pt_S214_geplannt_kdminsoll.sumvzkdgeplannt) as vzkd_geplant, Sum($pt_S214_ohneEx_minuten_aus_DRUECK.sumvzkd) AS vzkd, ";
$sql.=" Sum($pt_S214_ohneEx_minuten_aus_DRUECK.sumvzaby) AS vzaby, ";
$sql.=" Sum($pt_S214_ohneEx_minuten_aus_DRUECK.sumverb) AS verb, ";
$sql.=" sum($pt_S214_ohneEx_minuten_aus_DRUECK.auss2) as auss2, sum($pt_S214_ohneEx_minuten_aus_DRUECK.auss4) as auss4, ";
$sql.=" sum($pt_S214_ohneEx_minuten_aus_DRUECK.auss6) as auss6, dkopf.teillang, dkopf.Gew ";
//$sql.=" $pt_S214_geplannt_ursprungstk.maxoffremdauftr, dkopf.`Muster-Platz` musterplatz";
$sql.=" FROM $pt_S214_geplannt_kdminsoll JOIN ";
$sql.= " $pt_S214_geplannt_ursprungstk on $pt_S214_geplannt_kdminsoll.auftragsnr=$pt_S214_geplannt_ursprungstk.auftragsnr";
$sql.= " and $pt_S214_geplannt_kdminsoll.teil=$pt_S214_geplannt_ursprungstk.teil";
$sql.= " and $pt_S214_geplannt_kdminsoll.`pos-pal-nr`=$pt_S214_geplannt_ursprungstk.`pos-pal-nr`";
$sql.=" JOIN dkopf ON $pt_S214_geplannt_ursprungstk.Teil = dkopf.Teil";
$sql.=" left join $pt_S214_ohneEx_minuten_aus_DRUECK on ";
$sql.=" ($pt_S214_ohneEx_minuten_aus_DRUECK.AuftragsNr = $pt_S214_geplannt_kdminsoll.AuftragsNr) ";
$sql.=" AND ($pt_S214_ohneEx_minuten_aus_DRUECK.Teil = $pt_S214_geplannt_kdminsoll.Teil) and ($pt_S214_ohneEx_minuten_aus_DRUECK.`pos-pal-nr` = $pt_S214_geplannt_kdminsoll.`pos-pal-nr`) AND ";
$sql.=" ($pt_S214_ohneEx_minuten_aus_DRUECK.TaetNr = $pt_S214_geplannt_kdminsoll.abgnr)";

$sql.=" GROUP BY $pt_S214_geplannt_kdminsoll.termin, $pt_S214_geplannt_ursprungstk.AuftragsNr,$pt_S214_geplannt_ursprungstk.maxoffremdauftr, ";
$sql.=" $pt_S214_geplannt_ursprungstk.Teil, $pt_S214_geplannt_kdminsoll.abgnr";

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

$options = array(
					'rootTag'=>'S214',
					'idColumn'=>'termin',
					'rowTag'=>'geplant',
					'elements'=>array(
						'termin',
						'ex_datum'=>'#get_ex_datum_soll();',
						'auftraege'=>array(
							'rootTag'=>'auftraege',
							'rowTag'=>'auftrag',
							'idColumn'=>'AuftragsNr',
							'elements'=>array(
								'AuftragsNr',
                                                                'aufdat',
                                                                'fremdauftraege'=>array(
                                                                    'rootTag'=>'fremdauftraege',
                                                                    'rowTag'=>'fremdauftrag',
                                                                    'idColumn'=>'maxoffremdauftr',
                                                                    'elements'=>array(
                                                                        'maxoffremdauftr',
                                                                        'teile'=>array(
                                                                            'rootTag'=>'teile',
                                                                            'rowTag'=>'teil',
                                                                            'idColumn'=>'Teil',
                                                                            'elements'=>array(
    										'teilnr'=>'Teil',
										'stk_ursprung',
										'teillang',
										'Gew',
										'abnr'=>'maxoffremdauftr',
										'gew_geplant'=>'#gew_geplant();',
										'musterplatz',
										'taetigkeiten'=>array(
											'rootTag'=>'taetigkeiten',
											'rowTag'=>'taetigkeit',
											'idColumn'=>'abgnr',
											'elements'=>array(
												'abgnr',
												'stk_drueck',
												'vzkd_geplant',
												'vzkd',
												'vzaby',
												'verb',
												'fac1'=>'#vypocti_fac1();',
												'auss2',
												'auss4',
												'auss6'
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
$domxml->save("S214.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
