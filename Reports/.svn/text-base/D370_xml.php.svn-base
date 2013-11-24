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
$views=array("pt_D370","pt_D370_summe_lieferung","pt_D370_importgew","pt_D370_importstk","pt_D370_gutstk");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT auftragsnr,teil,`pos-pal-nr` as pal,";
$pt.=" sum(if(`auss_typ`=2,`Auss-Stück`,0)) as auss_2, ";
$pt.=" sum(if(`auss_typ`=4,`Auss-Stück`,0)) as auss_4, ";
$pt.=" sum(if(`auss_typ`=6,`Auss-Stück`,0)) as auss_6, ";
$pt.=" sum(if(`auss_typ`=2,`Auss-Stück`,0))+sum(if(`auss_typ`=4,`Auss-Stück`,0))+sum(if(`auss_typ`=6,`Auss-Stück`,0)) ";
$pt.=" as auss_celkem ";
$pt.=" FROM `drueck` WHERE ((auftragsnr='$auftragsnr')) group by auftragsnr,teil,pal";

//echo $pt."<br>";
$db->query($pt);


$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT auftragsnr,drueck.teil,`pos-pal-nr` as pal,max(gew) as gew,sum(`Auss-Stück`) as summe_lieferung,sum(`Auss-Stück`*gew) as gew_auss_teil_pal";
$pt.=" FROM `drueck`";
$pt.=" join dkopf using(teil)";
$pt.=" WHERE ((auftragsnr='$auftragsnr')) group by auftragsnr,drueck.teil,pal";
 
//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT auftragsnr,sum(gew*`stück`) as import_gew FROM `dauftr` join dkopf ";
$pt.=" using(teil) WHERE ((auftragsnr='$auftragsnr') and (kzgut='G')) group by auftragsnr";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[3];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT auftragsnr,teil,`pos-pal-nr` as pal,sum(`stück`) as import_stk FROM `dauftr`";
$pt.=" WHERE ((auftragsnr='$auftragsnr') and (kzgut='G')) group by auftragsnr,teil,pal";

//echo $pt."<br>";
$db->query($pt);

$viewname=$pcip.$views[4];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT drueck.auftragsnr,drueck.teil,drueck.`pos-pal-nr` as pal,sum(drueck.`Stück`) as gut_stk";
$pt.=" FROM `drueck`";
$pt.=" join dauftr on (dauftr.auftragsnr=drueck.auftragsnr) and (dauftr.teil=drueck.teil) and (dauftr.`pos-pal-nr`=drueck.`pos-pal-nr`) and (dauftr.abgnr=drueck.taetnr)";
$pt.=" WHERE ((drueck.auftragsnr='$auftragsnr') and (dauftr.kzgut='G')) group by drueck.auftragsnr,drueck.teil,pal";

//echo $pt."<br>";
$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$pt_D370=$pcip.$views[0];
$pt_D370_summe_lieferung=$pcip.$views[1];
$pt_D370_importgew=$pcip.$views[2];
$pt_D370_importstk=$pcip.$views[3];
$pt_D370_gutstk=$pcip.$views[4];

$sql=" select $pt_D370.auftragsnr,import_gew,$pt_D370.teil,$pt_D370.pal,gew,import_stk,";
$sql.=" auss_2,auss_4,auss_6,";
$sql.=" gut_stk,auss_celkem,summe_lieferung,summe_lieferung-auss_celkem as delta_auss,gew_auss_teil_pal";
$sql.=" from $pt_D370 join $pt_D370_summe_lieferung";
$sql.=" on ($pt_D370.auftragsnr=$pt_D370_summe_lieferung.auftragsnr) and ($pt_D370.teil=$pt_D370_summe_lieferung.teil) and ($pt_D370.pal=$pt_D370_summe_lieferung.pal)";
$sql.=" join $pt_D370_importstk on ($pt_D370.auftragsnr=$pt_D370_importstk.auftragsnr) and ($pt_D370.teil=$pt_D370_importstk.teil) and ($pt_D370.pal=$pt_D370_importstk.pal)";
$sql.=" join $pt_D370_gutstk on ($pt_D370.auftragsnr=$pt_D370_gutstk.auftragsnr) and ($pt_D370.teil=$pt_D370_gutstk.teil) and ($pt_D370.pal=$pt_D370_gutstk.pal)";
$sql.=" join $pt_D370_importgew on ($pt_D370.auftragsnr=$pt_D370_importgew.auftragsnr)";

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
					'rootTag'=>'D370',
					'idColumn'=>'auftragsnr',
					'rowTag'=>'auftrag',
					'elements'=>array(
						'auftragsnr',
						'import_gew',
						'teile'=>array(
							'rootTag'=>'teile',
							'rowTag'=>'teil',
							'idColumn'=>'teil',
							'elements'=>array(
								'teilnr'=>'teil',
								'paletten'=>array(
									'rootTag'=>'paletten',
									'rowTag'=>'pal',
									'idColumn'=>'pal',
									'elements'=>array(
										'teilnr'=>'teil',
										'palnr'=>'pal',
										'gew',
										'auss_2',
										'auss_4',
										'auss_6',
										'summe_lieferung',
										'gut_stk',
										'import_stk',
										'sonst'=>'delta_auss',
										'gew_auss_teil_pal'
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
//$domxml->save("D370.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
