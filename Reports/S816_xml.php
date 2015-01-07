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
$views=array("pt_hotovosum");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT drueck.AuftragsNr, drueck.Teil, drueck.`pos-pal-nr`, Sum(if(dauftr.kzgut='G',drueck.`Stück`,0)) AS gutstk,";
$pt.=" Sum(drueck.`Auss-Stück`) AS auss,";
$pt.=" Sum(if(auss_typ=2,drueck.`Auss-Stück`,0)) AS auss_2,";
$pt.=" Sum(if(auss_typ=4,drueck.`Auss-Stück`,0)) AS auss_4,";
$pt.=" Sum(if(auss_typ=6,drueck.`Auss-Stück`,0)) AS auss_6";
$pt.=" FROM dauftr INNER JOIN drueck ON (dauftr.`pos-pal-nr` = drueck.`pos-pal-nr`) AND (dauftr.abgnr = drueck.TaetNr) AND (dauftr.Teil = drueck.Teil) AND (dauftr.AuftragsNr = drueck.AuftragsNr)";
$pt.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
//$pt.=" WHERE (((dauftr.AuftragsNr) Between '".$auftragsnr_von."' And '".$auftragsnr_bis."') AND ((dauftr.`auftragsnr-exp`) Is Null) AND ((dauftr.Teil) Like '".$teil."'))";
$pt.=" WHERE (((daufkopf.kunde) Between '".$kunde_von."' And '".$kunde_bis."') AND ((dauftr.`auftragsnr-exp`) Is Null) AND ((dauftr.Teil) Like '".$teil."'))";
$pt.=" GROUP BY drueck.AuftragsNr, drueck.Teil, drueck.`pos-pal-nr`";
//$pt.=" HAVING (((drueck.AuftragsNr) Between '".$auftragsnr_von."' And '".$auftragsnr_bis."') AND ((drueck.Teil) Like '".$teil."'))";
//echo $pt."<br>";
$db->query($pt);



// provedu dotaz nad vytvorenymi pohledy
$pt_hotovosum=$pcip.$views[0];

$sql="SELECT dauftr.AuftragsNr, dauftr.Teil, dauftr.Stück, dauftr.fremdpos,dauftr.`pos-pal-nr`, dkopf.Gew, `gew`*`stück` AS vahacelkem, dauftr.`auftragsnr-exp`, ".$pt_hotovosum.".gutstk, ".$pt_hotovosum.".auss, $pt_hotovosum.auss_2,$pt_hotovosum.auss_4,$pt_hotovosum.auss_6,dauftr.KzGut, daufkopf.bestellnr,DATE_FORMAT(daufkopf.Aufdat,'%Y-%m-%d') as Aufdat, dauftr.Termin";
$sql.=" FROM daufkopf INNER JOIN ((dkopf RIGHT JOIN dauftr ON dkopf.Teil = dauftr.Teil) LEFT JOIN ".$pt_hotovosum." ON (dauftr.AuftragsNr = ".$pt_hotovosum.".AuftragsNr) AND (dauftr.Teil = ".$pt_hotovosum.".Teil) AND (dauftr.`pos-pal-nr` = ".$pt_hotovosum.".`pos-pal-nr`)) ON daufkopf.AuftragsNr = dauftr.AuftragsNr";
//$sql.=" WHERE (((dauftr.AuftragsNr) Between '".$auftragsnr_von."' And '".$auftragsnr_bis."') AND ((dauftr.Teil) Like '".$teil."') AND ((dauftr.`pos-pal-nr`)>0) AND ((dauftr.`auftragsnr-exp`) Is Null) AND ((dauftr.KzGut)='G'))";
$sql.=" WHERE (((daufkopf.kunde) Between '".$kunde_von."' And '".$kunde_bis."') AND ((dauftr.Teil) Like '".$teil."') AND ((dauftr.`pos-pal-nr`)>0) AND ((dauftr.`auftragsnr-exp`) Is Null) AND ((dauftr.KzGut)='G'))";
$sql.=" ORDER BY dauftr.AuftragsNr, dauftr.Teil, dauftr.`pos-pal-nr`";

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
		'encoder'=>false,
		'rootTag'=>'S816',
		'idColumn'=>'AuftragsNr',
		'rowTag'=>'auftraege',
		'elements'=>array(
			'AuftragsNr',
			'Aufdat',
			'bestellnr',
			'positionen'=>array(
				'rootTag'=>'teile',
				'rowTag'=>'teil',
				'idColumn'=>'Teil',
				'elements'=>array(
					'paletten'=>array(
						'rootTag'=>'pal',
						'rowTag'=>'palette',
						'idColumn'=>'pos-pal-nr',
						'elements'=>array(
							'teilnr'=>'Teil',
							'stkauftrag'=>'Stück',
							'KzGut',
							'gutstk',
							'auss',
							'auss_2',
							'auss_4',
							'auss_6',
							'pal'=>'pos-pal-nr',
							'fremdpos',
							'Gew',
							'vahacelkem',
							'Termin'
						),
					),
				),
			),
		),
);


// vytahnu si parametry z XML souboru
// tady ziskam vystup dotazu ve forme XML					
$domxml = $query2xml->getXML($sql,$options);
$domxml->encoding="windows-1250";

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
//$domxml->save("S816.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());
?>
