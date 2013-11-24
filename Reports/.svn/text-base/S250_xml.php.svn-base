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
$pt.=" as SELECT dauftr.auftragsnr,teil,minpreis,sum(`Stück`) as stkimport FROM `dauftr` ";
$pt.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$pt.=" WHERE ((dauftr.auftragsnr='".$auftragsnr."') and (kzgut='G')) group by dauftr.auftragsnr,teil,minpreis";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as select `AuftragsNr`,";
$pt.=" `Teil`,`TaetNr`,";
$pt.=" sum(`Stück`) as gutstk,";
$pt.=" sum(if(auss_typ=2,`Auss-Stück`,0)) as auss2,";
$pt.=" sum(if(auss_typ=4,`Auss-Stück`,0)) as auss4,";
$pt.=" sum(if(auss_typ=6,`Auss-Stück`,0)) as auss6,";
$pt.=" sum(if(auss_typ=4,(`Stück`+`Auss-Stück`)*`VZ-SOLL`,(`Stück`*`VZ-SOLL`)) ) as vzkd,";
$pt.=" sum(if(auss_typ=4,(`Stück`+`Auss-Stück`)*`VZ-IST`,(`Stück`*`VZ-IST`)) ) as vzaby,";
$pt.=" sum(`Verb-Zeit`) as verb,";
$pt.=" max(`VZ-SOLL`) as vzkd_stk,";
$pt.=" if(sum(`Verb-Zeit`)<>0,sum(if(auss_typ=4,(`Stück`+`Auss-Stück`)*`VZ-SOLL`,(`Stück`*`VZ-SOLL`)) )/sum(`Verb-Zeit`),0) as factor";
$pt.=" from drueck where ((auftragsnr='".$auftragsnr."')) group by auftragsnr,teil,taetnr";
 
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$v_dauftr=$pcip.$views[0];
$v_drueck=$pcip.$views[1];
*/

$sql=" select DATE_FORMAT(datum,'%Y-%m-%d') as datum,drueck.schicht,Schichtfuehrer,auftragsnr, ";
$sql.=" sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-soll`,`Stück`*`vz-soll`)) as vzkd, ";
$sql.=" sum(if(auss_typ=4,(Stück+`auss-Stück`)*`vz-ist`,`Stück`*`vz-ist`)) as vzaby, ";
$sql.=" sum(`verb-zeit`) as verb from drueck ";
$sql.=" join dschicht on (drueck.schicht=dschicht.schichtnr) ";
$sql.=" where ((datum between '".$datum_von."' and '".$datum_bis."') ";
$sql.=" and (schicht between '".$schicht_von."' and '".$schicht_bis."')) ";
$sql.=" group by datum,schicht,auftragsnr order by datum,schicht,Schichtfuehrer,auftragsnr";

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

$options = array(
					'rootTag'=>'S250',
					'idColumn'=>'datum',
					'rowTag'=>'datumy',
					'elements'=>array(
						'datum',
						'schichten'=>array(
							'rootTag'=>'schichten',
							'rowTag'=>'schicht',
							'idColumn'=>'schicht',
							'elements'=>array(
								'schichtnr'=>'schicht',
								'Schichtfuehrer',
								'auftraege'=>array(
									'rootTag'=>'auftraege',
									'rowTag'=>'auftrag',
									'idColumn'=>'auftragsnr',
									'elements'=>array(
										'auftragsnr',
										'vzkd',
										'vzaby',
										'verb'
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
//$domxml->save("S250.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
