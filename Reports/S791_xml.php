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
$views=array("pt_S791_dauftr","pt_S791_drech","pt_S791_aufgew","pt_S791_aufgew_auss","pt_S791_importe","pt_S791_drueck");

$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select daufkopf.kunde,dauftr.`auftragsnr-exp`,daufkopf.aufdat, daufkopf.fertig,daufkopf.ausliefer_datum,";
$pt.=" dauftr.auftragsnr,dauftr.teil, dauftr.`pos-pal-nr` from dauftr";
$pt.=" join daufkopf on (dauftr.`auftragsnr-exp`=daufkopf.auftragsnr)";
$pt.=" where ((daufkopf.kunde between ".$kunde_von." and ".$kunde_bis.") ";
$pt.=" and ((daufkopf.ausliefer_datum between '".$ausliefer_von."' and '".$ausliefer_bis."')))";
$pt.=" group by daufkopf.kunde,dauftr.`auftragsnr-exp`,daufkopf.aufdat,daufkopf.fertig, daufkopf.ausliefer_datum,";
$pt.=" dauftr.auftragsnr,dauftr.teil,dauftr.`pos-pal-nr`";
//echo $pt."<br>";
$db->query($pt);
	
$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select daufkopf.kunde,daufkopf.auftragsnr,daufkopf.minpreis as preismin,daufkopf.waehr_kz as `waehr-kz`, ";
$pt.=" sum(if(leistung='j',(Stück+ausschuss)*dm/daufkopf.minpreis,0)) as sumpreismin_leistung, ";
$pt.=" sum(if(leistung='j',(Stück+ausschuss)*dm,0)) as sumpreis_leistung, ";
$pt.=" sum(if(leistung='n',(Stück+ausschuss)*dm,0)) as sumpreis_sonst ";
$pt.=" from drech join dtaetkz on (drech.`Taet-kz`=dtaetkz.dtaetkz) ";
$pt.=" join daufkopf on (drech.`auftragsnr`=daufkopf.auftragsnr) ";
$pt.=" join dksd on (dksd.kunde=daufkopf.kunde) ";
$pt.=" where ((daufkopf.kunde between ".$kunde_von." and ".$kunde_bis.") and ";
$pt.=" ((daufkopf.ausliefer_datum between '".$ausliefer_von."' and '".$ausliefer_bis."')))";
$pt.=" group by daufkopf.kunde,daufkopf.auftragsnr,daufkopf.minpreis,daufkopf.waehr_kz";
//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select daufkopf.auftragsnr as auftrex,dauftr.auftragsnr, sum(gew*`stk-exp`)/1000 as aufgew ";
$pt.=" from dauftr join daufkopf on (dauftr.`auftragsnr-exp`=daufkopf.auftragsnr) ";
$pt.=" join dkopf on (dauftr.teil=dkopf.teil) ";
$pt.=" where ((`kzgut`='G') and  (daufkopf.kunde between ".$kunde_von." and ".$kunde_bis.")  ";
$pt.=" and ((daufkopf.ausliefer_datum between '".$ausliefer_von."' and '".$ausliefer_bis."')))";
$pt.=" group by daufkopf.auftragsnr,dauftr.auftragsnr";
//echo $pt."<br>";
//echo $pt."<br>";
//echo $pt."<br>";
$db->query($pt);


$viewname=$pcip.$views[3];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select daufkopf.auftragsnr as auftrex,dauftr.auftragsnr, sum(gew*(auss2_stk_exp+auss4_stk_exp+auss6_stk_exp))/1000 as aufgew_auss ";
$pt.=" from dauftr join daufkopf on (dauftr.`auftragsnr-exp`=daufkopf.auftragsnr) ";
$pt.=" join dkopf on (dauftr.teil=dkopf.teil) ";
$pt.=" where ((daufkopf.kunde between ".$kunde_von." and ".$kunde_bis.")  ";
$pt.=" and ((daufkopf.ausliefer_datum between '".$ausliefer_von."' and '".$ausliefer_bis."')))";
$pt.=" group by daufkopf.auftragsnr,dauftr.auftragsnr";

//echo $pt."<br>";
//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[4];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select dauftr.auftragsnr from dauftr";
$pt.=" join daufkopf on (dauftr.`auftragsnr-exp`=daufkopf.auftragsnr)";
$pt.=" where (";
$pt.=" (daufkopf.ausliefer_datum between '".$ausliefer_von."' and '".$ausliefer_bis."')";
$pt.=" and";
$pt.=" (daufkopf.kunde between ".$kunde_von." and ".$kunde_bis.")";
$pt.=" )";
$pt.=" group by dauftr.auftragsnr";
//echo $pt."<br>";
$db->query($pt);


$pt_S791_importe=$pcip.$views[4];

$viewname=$pcip.$views[5];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select drueck.auftragsnr,drueck.teil,drueck.`pos-pal-nr`, ";
$pt.=" sum(if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-soll`,drueck.`stück`*`vz-soll`)) as sumvzkd, ";
$pt.=" sum(if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-ist`,drueck.`stück`*`vz-ist`)) as sumvzaby, ";
$pt.=" sum(`verb-zeit`) as sumverb, ";
$pt.=" sum(if(taetnr>1999 and taetnr<4000,if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-soll`,drueck.`stück`*`vz-soll`),0)) as vzkd1999, ";
$pt.=" sum(if(taetnr>1999 and taetnr<4000,if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-ist`,drueck.`stück`*`vz-ist`),0)) as vzaby1999, ";
$pt.=" sum(if(taetnr>3999,if(auss_typ=4,(drueck.`stück`+`auss-stück`)*`vz-ist`,drueck.`stück`*`vz-ist`),0)) as vzaby3999 ";
$pt.=" from drueck join $pt_S791_importe on drueck.auftragsnr=$pt_S791_importe.auftragsnr ";
//$pt.=" where ((daufkopf.kunde between ".$kunde_von." and ".$kunde_bis."))";
$pt.=" group by drueck.auftragsnr,drueck.teil,drueck.`pos-pal-nr`";

//echo $pt."<br>";
$db->query($pt);


// provedu dotaz nad vytvorenymi pohledy
$pt_S791_dauftr=$pcip.$views[0];
$pt_S791_drech=$pcip.$views[1];
$pt_S791_aufgew=$pcip.$views[2];
$pt_S791_aufgew_auss=$pcip.$views[3];
$pt_S791_importe=$pcip.$views[4];
$pt_S791_drueck=$pcip.$views[5];


$sql="SELECT $pt_S791_dauftr.kunde, MONTH($pt_S791_dauftr.ausliefer_datum) as mesic, $pt_S791_dauftr.`auftragsnr-exp`, $pt_S791_drech.preismin, $pt_S791_drech.`waehr-kz`,";
$sql.=" $pt_S791_drech.sumpreismin_leistung, $pt_S791_drech.sumpreis_leistung, $pt_S791_drech.sumpreis_sonst, ";
$sql.=" DATE_FORMAT(daufkopf.Aufdat,'%y-%m-%d') as Aufdat, DATE_FORMAT($pt_S791_dauftr.fertig,'%y-%m-%d') as fertig, DATE_FORMAT($pt_S791_dauftr.ausliefer_datum,'%y-%m-%d') as ausliefer_datum, $pt_S791_dauftr.auftragsnr, ";
$sql.=" Sum($pt_S791_drueck.sumvzkd) AS Summevonsumvzkd, Sum($pt_S791_drueck.sumvzaby) AS Summevonsumvzaby, ";
$sql.=" Sum($pt_S791_drueck.sumverb) AS Summevonsumverb, Sum($pt_S791_drueck.vzkd1999) AS Summevonvzkd1999, ";
$sql.=" Sum($pt_S791_drueck.vzaby1999) AS Summevonvzaby1999, Sum($pt_S791_drueck.vzaby3999) AS Summevonvzaby3999, ";
$sql.=" $pt_S791_aufgew.aufgew,$pt_S791_aufgew_auss.aufgew_auss";
$sql.=" FROM $pt_S791_aufgew_auss";
$sql.=" INNER JOIN ($pt_S791_aufgew ";
$sql.=" INNER JOIN ($pt_S791_drech";
$sql.=" INNER JOIN (($pt_S791_dauftr";
$sql.=" INNER JOIN $pt_S791_drueck";
$sql.=" ON ($pt_S791_dauftr.`pos-pal-nr` = $pt_S791_drueck.`pos-pal-nr`) ";
$sql.=" AND ($pt_S791_dauftr.teil = $pt_S791_drueck.teil) AND ($pt_S791_dauftr.auftragsnr = $pt_S791_drueck.auftragsnr)) ";
$sql.=" INNER JOIN daufkopf ON $pt_S791_dauftr.auftragsnr = daufkopf.AuftragsNr) ";
$sql.=" ON $pt_S791_drech.auftragsnr = $pt_S791_dauftr.`auftragsnr-exp`) ";
$sql.=" ON ($pt_S791_aufgew.auftragsnr = $pt_S791_dauftr.auftragsnr) ";
$sql.=" AND ($pt_S791_aufgew.auftrex = $pt_S791_dauftr.`auftragsnr-exp`)) ";
$sql.=" ON ($pt_S791_aufgew_auss.auftragsnr = $pt_S791_dauftr.auftragsnr) ";
$sql.=" AND ($pt_S791_aufgew_auss.auftrex = $pt_S791_dauftr.`auftragsnr-exp`) ";
$sql.=" GROUP BY $pt_S791_dauftr.kunde, MONTH($pt_S791_dauftr.ausliefer_datum), $pt_S791_dauftr.`auftragsnr-exp`, ";
$sql.=" $pt_S791_drech.preismin, $pt_S791_drech.`waehr-kz`, $pt_S791_drech.sumpreismin_leistung, ";
$sql.=" $pt_S791_drech.sumpreis_leistung, $pt_S791_drech.sumpreis_sonst, daufkopf.Aufdat, ";
$sql.=" $pt_S791_dauftr.fertig, $pt_S791_dauftr.auftragsnr, $pt_S791_aufgew.aufgew,$pt_S791_aufgew_auss.aufgew_auss";

//echo "sql=$sql"."<br>";


$query2xml = XML_Query2XML::factory($db);
	
	//echo $sql."<br>";
// tady se budou tisknout parametry

function fac1($record)
{
	if($record['Summevonsumvzaby']!=0)
		return $record['Summevonsumvzkd']/$record['Summevonsumvzaby'];
	else
		return 0;
}

function fac2($record)
{
	if($record['Summevonsumverb']!=0)
		return $record['Summevonsumvzkd']/$record['Summevonsumverb'];
	else
		return 0;
}

function eur_pro_tonne($record)
{
	$preismin=$record['preismin']/get_kurs($record['waehr-kz'],$record['ausliefer_datum']);
	
	if($record['aufgew']!=0)
		return $record['Summevonsumvzkd']*$preismin/$record['aufgew'];
	else
		return 0;
}

function popis_mesice($record)
{
	$mesice=array("Jan","Feb","Mrz","Apr","Mai","Jun","Jul","Aug","Sep","Oct","Nov","Dez");
	return($mesice[$record['mesic']-1]);
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

function preismin_in_EUR($record)
{
	$wahr=$record['waehr-kz'];
	$ausliefer_datum=$record['ausliefer_datum'];
	return $record['preismin']/get_kurs($wahr,$ausliefer_datum);
}

function sumpreis_leistung_EUR($record)
{
	$wahr=$record['waehr-kz'];
	$ausliefer_datum=$record['ausliefer_datum'];
	return $record['sumpreis_leistung']/get_kurs($wahr,$ausliefer_datum);
}

function sumpreis_sonst_EUR($record)
{
	$wahr=$record['waehr-kz'];
	$ausliefer_datum=$record['ausliefer_datum'];
	return $record['sumpreis_sonst']/get_kurs($wahr,$ausliefer_datum);
}

$options = array(
		'rootTag'=>'S791',
		'idColumn'=>'kunde',
		'rowTag'=>'kunden',
		'elements'=>array(
			'kunde',
			'mesice'=>array(
				'rootTag'=>'datum',
				'rowTag'=>'mesice',
				'idColumn'=>'mesic',
				'elements'=>array(
					'mesic'=>"#popis_mesice();",
					'auftragsnr-exp'=>array(
						'rootTag'=>'exports',
						'rowTag'=>'export',
						'idColumn'=>'auftragsnr-exp',
						'elements'=>array(
							'ex'=>'auftragsnr-exp',
							//'preismin',
							'preismin'=>"#preismin_in_EUR();",
							'waehr-kz',
							'sumpreismin_leistung',
							'sumpreis_leistung_EUR'=>"#sumpreis_leistung_EUR();",
							'sumpreis_sonst_EUR'=>"#sumpreis_sonst_EUR();",
							'fertig',
							'ausliefer_datum',
							'auftragsnr'=>array(
								'rootTag'=>'imports',
								'rowTag'=>'import',
								'idColumn'=>'auftragsnr',
								'elements'=>array(
									'im'=>'auftragsnr',
									'kdmin'=>'Summevonsumvzkd',
									'abymin'=>'Summevonsumvzaby',
									'verb'=>'Summevonsumverb',
									'vzkd1999'=>'Summevonvzkd1999',
									'vzaby1999'=>'Summevonvzaby1999',
									'vzaby3999'=>'Summevonvzaby3999',
									'aufgew',
									'aufgew_auss',
									'Aufdat',
									'fertig',
									'ausliefer_datum',
									'fac1'=>"#fac1();",
									'fac2'=>"#fac2();",
									'eur_pro_tonne'=>"#eur_pro_tonne();"
								)
							)
						)
					)
				)
			)
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
	$viewname=$pcip.$views[$i];
	$sql="drop view ". $viewname;
	$db->query($sql);
	//echo $sql."<br>";
}

$db->disconnect();
//============================================================+
// END OF FILE                                                 
//============================================================+
//$domxml->save("S791.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
