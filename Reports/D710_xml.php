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
$views=array("D710_exportstk1","D710_exportstk","export_uebersicht_geliefert_teil","export_uebersicht_gew_gut");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dauftr.`auftragsnr-exp`, dauftr.teil, dauftr.auftragsnr, dauftr.abgnr, dauftr.`mehrarb-kz`,";
$pt.=" Sum(dauftr.`stk-exp`) AS `Summevonstk-exp`,";
$pt.=" Sum(dauftr.auss2_stk_exp) AS Summevonauss2_stk_exp,";
$pt.=" Sum(dauftr.auss4_stk_exp) AS Summevonauss4_stk_exp,";
$pt.=" Sum(dauftr.auss6_stk_exp) AS Summevonauss6_stk_exp,";
$pt.=" dauftr.KzGut";
$pt.=" FROM dauftr";
$pt.=" where (((dauftr.`auftragsnr-exp`)='$export'))";
$pt.=" GROUP BY dauftr.`auftragsnr-exp`, dauftr.teil, dauftr.auftragsnr, dauftr.abgnr, dauftr.`mehrarb-kz`, dauftr.KzGut";
$pt.=" ORDER BY dauftr.teil, dauftr.auftragsnr, dauftr.abgnr, dauftr.`mehrarb-kz`;";


//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");

$D710_exportstk1=$pcip.$views[0];

$pt="create view $viewname";
$pt.=" as SELECT $D710_exportstk1.`auftragsnr-exp`, $D710_exportstk1.Teil, $D710_exportstk1.AuftragsNr, Min($D710_exportstk1.abgnr) AS Minvonabgnr,";
$pt.=" $D710_exportstk1.`MehrArb-KZ`, $D710_exportstk1.`Summevonstk-exp`,";
$pt.=" Sum($D710_exportstk1.Summevonauss2_stk_exp) AS SummevonSummevonauss2_stk_exp, ";
$pt.=" Sum($D710_exportstk1.Summevonauss4_stk_exp) AS SummevonSummevonauss4_stk_exp, ";
$pt.=" Sum($D710_exportstk1.Summevonauss6_stk_exp) AS SummevonSummevonauss6_stk_exp, $D710_exportstk1.KzGut";
$pt.=" FROM $D710_exportstk1";
$pt.=" where ((($D710_exportstk1.`auftragsnr-exp`)='$export') AND (($D710_exportstk1.`MehrArb-KZ`)<>'I'))";
$pt.=" GROUP BY $D710_exportstk1.`auftragsnr-exp`, $D710_exportstk1.Teil, $D710_exportstk1.AuftragsNr, $D710_exportstk1.`MehrArb-KZ`,";
$pt.=" $D710_exportstk1.`Summevonstk-exp`, $D710_exportstk1.`KzGut`";
$pt.=" ORDER BY $D710_exportstk1.Teil, $D710_exportstk1.AuftragsNr, Minvonabgnr;";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT dauftr.auftragsnr, dkopf.Gew, dauftr.teil, Sum(dauftr.`stk-exp`) AS geliefert,";
$pt.=" Sum(dauftr.`stück`) AS angeliefert, `gew`* Sum(dauftr.`stk-exp`) AS gew_gut, dauftr.`auftragsnr-exp`,";
$pt.=" max(fremdauftr) AS fremdauftr, max(fremdpos) as fremdpos";
$pt.=" FROM dauftr INNER JOIN dkopf ON dauftr.teil = dkopf.Teil";
$pt.=" WHERE (((dauftr.KzGut)='G') and (dauftr.`auftragsnr-exp`='$export'))";
$pt.=" GROUP BY dauftr.auftragsnr, dkopf.Gew, dauftr.teil, dauftr.`auftragsnr-exp`";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[3];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT dauftr.`auftragsnr-exp`, Sum(`gew`*`stk-exp`) AS gew_gut, Count(dauftr.`pal-nr-exp`) AS positionen";
$pt.=" FROM dauftr INNER JOIN dkopf ON dauftr.teil = dkopf.Teil";
$pt.=" WHERE ((dauftr.KzGut='G') and (dauftr.`auftragsnr-exp`='$export'))";
$pt.=" GROUP BY dauftr.`auftragsnr-exp`";

//echo $pt."<br>";
$db->query($pt);


// provedu dotaz nad vytvorenymi pohledy

$D710_exportstk1=$pcip.$views[0];
$D710_exportstk=$pcip.$views[1];
$export_uebersicht_geliefert_teil=$pcip.$views[2];
$export_uebersicht_gew_gut=$pcip.$views[3];

$sql=" SELECT $D710_exportstk.`auftragsnr-exp` as ex,";
 $sql.=" '$datumvon' as termin,";
 $sql.=" $D710_exportstk.AuftragsNr as im,";
 $sql.=" $D710_exportstk.Teil,";
 $sql.=" dkopf.teillang, dkopf.gew,";
 $sql.=" $D710_exportstk.`MehrArb-KZ` as tatkz,";
 $sql.=" Sum($D710_exportstk.SummevonSummevonauss2_stk_exp) AS auss2_stk_exp,";
 $sql.=" Sum($D710_exportstk.SummevonSummevonauss4_stk_exp) AS auss4_stk_exp,";
 $sql.=" Sum($D710_exportstk.SummevonSummevonauss6_stk_exp) AS auss6_stk_exp,";
 $sql.=" $D710_exportstk.KzGut,";
 $sql.=" dtaetkz.text,";
 $sql.=" $export_uebersicht_geliefert_teil.geliefert,";
 $sql.=" $export_uebersicht_geliefert_teil.angeliefert,";
 $sql.=" Sum((`SummevonSummevonauss2_stk_exp`+`SummevonSummevonauss4_stk_exp`+`SummevonSummevonauss6_stk_exp`)*(dkopf.gew)) AS gew_auss,";
 $sql.=" $export_uebersicht_gew_gut.gew_gut,";
 $sql.=" $export_uebersicht_gew_gut.positionen,";
 $sql.=" dksd.Kunde, dksd.Name1, dksd.Name2, dksd.Straße as strasse, dksd.Plz, dksd.Ort, dksd.Tel, dksd.Fax, dksd.SachbearbeiterAby, dksd.TelAby, dksd.FaxAby, dksd.EmailAby,";
 $sql.=" $D710_exportstk.`Summevonstk-exp` as expstk,";
 $sql.=" $export_uebersicht_geliefert_teil.fremdauftr,";
 $sql.=" $export_uebersicht_geliefert_teil.fremdpos,";
 $sql.=" Min($D710_exportstk.Minvonabgnr) AS abgnr";
 $sql.=" FROM $export_uebersicht_gew_gut";
 $sql.=" INNER JOIN ($export_uebersicht_geliefert_teil";
 $sql.=" INNER JOIN (($D710_exportstk";
 $sql.=" INNER JOIN dtaetkz ON";
 $sql.=" $D710_exportstk.`MehrArb-KZ` = dtaetkz.dtaetkz)";
 $sql.=" INNER JOIN (dkopf INNER JOIN dksd ON dkopf.Kunde = dksd.Kunde)";
 $sql.=" ON $D710_exportstk.Teil = dkopf.Teil)";
 $sql.=" ON ($export_uebersicht_geliefert_teil.Teil = $D710_exportstk.Teil) AND ($export_uebersicht_geliefert_teil.AuftragsNr = $D710_exportstk.AuftragsNr))";
 $sql.=" ON $export_uebersicht_gew_gut.`auftragsnr-exp` = $D710_exportstk.`auftragsnr-exp`";
 $sql.=" where ((($D710_exportstk.`auftragsnr-exp`)='$export') AND (($D710_exportstk.`MehrArb-KZ`)<>'I') AND (($D710_exportstk.`MehrArb-KZ`)<>'Pk') AND (($D710_exportstk.`MehrArb-KZ`)<>'AR'))";
 $sql.=" GROUP BY $D710_exportstk.`auftragsnr-exp`, $D710_exportstk.AuftragsNr, $D710_exportstk.Teil, dkopf.teillang, dkopf.Gew, $D710_exportstk.`MehrArb-KZ`, $D710_exportstk.KzGut, dtaetkz.Leistung, dtaetkz.text, $export_uebersicht_geliefert_teil.geliefert, $export_uebersicht_geliefert_teil.angeliefert, $export_uebersicht_gew_gut.gew_gut, $export_uebersicht_gew_gut.positionen, dksd.Kunde, dksd.Name1, dksd.Name2, dksd.Straße, dksd.Plz, dksd.Ort, dksd.Tel, dksd.Fax, dksd.SachbearbeiterAby, dksd.TelAby, dksd.FaxAby, dksd.EmailAby, $D710_exportstk.`Summevonstk-exp`,";
 $sql.=" $export_uebersicht_geliefert_teil.fremdauftr,";
 $sql.=" $export_uebersicht_geliefert_teil.fremdpos";

$sql.=" ORDER BY $D710_exportstk.Teil,$D710_exportstk.AuftragsNr,abgnr;";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
//echo $sql."<br>";
// tady se budou tisknout parametry

function getTermin($d){
    return $d;
}

function geliefert_korr($record)
{
	return $record['geliefert']-auss_nach_Gtat($record);
}

function auss_nach_Gtat($record)
{
	$ex=$record['ex'];
	$im=$record['im'];
	$teil=$record['Teil'];
	$sql="select `auftragsnr-exp` as ex,auftragsnr as im, dauftr.teil, sum(if(auss2_stk_exp is null,0,auss2_stk_exp)) as auss2,";
	$sql.=" sum(if(auss4_stk_exp is null,0,auss4_stk_exp)) as auss4,sum(if(auss6_stk_exp is null,0,auss6_stk_exp)) as auss6";
	$sql.=" from dauftr join dpos on dauftr.teil=dpos.teil and dauftr.abgnr=dpos.`taetnr-aby`";
	$sql.=" where ((`auftragsnr-exp`='$ex') and (auftragsnr='$im') and (dauftr.teil='$teil') and (dpos.bedarf_typ='Ba'))";
	$sql.=" group by ex,im,dauftr.teil";
	//return $sql;
	$res=mysql_query($sql);
	$row=mysql_fetch_array($res);
	return $row['auss2']+$row['auss4']+$row['auss6'];
}


$options = array(
					'encoder'=>false,
					'rootTag'=>'D710',
					'idColumn'=>'ex',
					'rowTag'=>'export',
					'elements'=>array(
						'ex',
                        'termin',
						'gew_gut',
						'positionen',
						'Kunde',
						'Name1',
						'Name2',
						'strasse',
						'Plz',
						'Ort',
						'Tel',
						'Fax',
						'SachbearbeiterAby',
						'TelAby',
						'FaxAby',
						'EmailAby',
						'teile'=>array(
							'rootTag'=>'teile',
							'idColumn'=>'Teil',
							'rowTag'=>'teil',
							'elements'=>array(
								'teilnr'=>'Teil',
								'teillang',
								'gew',
								'importe'=>array(
									'rootTag'=>'importe',
									'rowTag'=>'import',
									'idColumn'=>'im',
									'elements'=>array(
										'im',
										'geliefert'=>'#geliefert_korr();',
										'angeliefert',
										'fremdauftr',
										'fremdpos',
										//'geliefert_korr'=>'#auss_nach_Gtat();',
										'taetigkeiten'=>array(
											'rootTag'=>'taetigkeiten',
											'rowTag'=>'tat',
											'idColumn'=>'tatkz',
											'elements'=>array(
												'tatkz',
												'text',
												'auss2_stk_exp',
												'auss4_stk_exp',
												'auss6_stk_exp',
												'KzGut',
												'gew_auss',
												'expstk',
												'abgnr'
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
$domxml->save("D710.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
