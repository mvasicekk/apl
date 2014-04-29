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
$views=array("pt_D360","pt_D360_summe_lieferung","pt_D360_importgew");


$viewname=$pcip.$views[0];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT auftragsnr,teil,";
$pt.=" sum(if(`auss-art`=1,`Auss-Stück`,0)) as auss_1, ";
$pt.=" sum(if(`auss-art`=10,`Auss-Stück`,0)) as auss_10, ";
$pt.=" sum(if(`auss-art`=20,`Auss-Stück`,0)) as auss_20, ";
$pt.=" sum(if(`auss-art`=28,`Auss-Stück`,0)) as auss_28, ";
$pt.=" sum(if(`auss-art`=34,`Auss-Stück`,0)) as auss_34, ";
$pt.=" sum(if(`auss-art`=43,`Auss-Stück`,0)) as auss_43, ";
$pt.=" sum(if(`auss-art`=50,`Auss-Stück`,0)) as auss_50, ";
$pt.=" sum(if(`auss-art`=52,`Auss-Stück`,0)) as auss_52, ";
$pt.=" sum(if(`auss-art`=60,`Auss-Stück`,0)) as auss_60, ";
$pt.=" sum(if(`auss-art`=71,`Auss-Stück`,0)) as auss_71, ";
$pt.=" sum(if(`auss-art`=75,`Auss-Stück`,0)) as auss_75, ";
$pt.=" sum(if(`auss-art`=78,`Auss-Stück`,0)) as auss_78, ";
$pt.=" sum(if(`auss-art`=83,`Auss-Stück`,0)) as auss_83, ";
$pt.=" sum(if(`auss-art`=731,`Auss-Stück`,0)) as auss_731, ";
$pt.=" sum(if(`auss-art`=735,`Auss-Stück`,0)) as auss_735, ";
$pt.=" sum(if(`auss-art`=745,`Auss-Stück`,0)) as auss_745, ";
$pt.=" sum(if(`auss-art`=991,`Auss-Stück`,0)) as auss_991, ";
$pt.=" sum(if(`auss-art`=995,`Auss-Stück`,0)) as auss_995, ";
$pt.=" sum(if(`auss-art`=1,`Auss-Stück`,0))+sum(if(`auss-art`=10,`Auss-Stück`,0))+sum(if(`auss-art`=20,`Auss-Stück`,0))+";
$pt.=" sum(if(`auss-art`=28,`Auss-Stück`,0))+sum(if(`auss-art`=34,`Auss-Stück`,0))+sum(if(`auss-art`=43,`Auss-Stück`,0))+";
$pt.=" sum(if(`auss-art`=50,`Auss-Stück`,0))+sum(if(`auss-art`=52,`Auss-Stück`,0))+sum(if(`auss-art`=60,`Auss-Stück`,0))+";
$pt.=" sum(if(`auss-art`=71,`Auss-Stück`,0))+sum(if(`auss-art`=75,`Auss-Stück`,0))+sum(if(`auss-art`=78,`Auss-Stück`,0))+";
$pt.=" sum(if(`auss-art`=83,`Auss-Stück`,0))+sum(if(`auss-art`=731,`Auss-Stück`,0))+sum(if(`auss-art`=735,`Auss-Stück`,0))+";
$pt.=" sum(if(`auss-art`=745,`Auss-Stück`,0))+sum(if(`auss-art`=991,`Auss-Stück`,0))+sum(if(`auss-art`=995,`Auss-Stück`,0)) ";
$pt.=" as auss_celkem ";
$pt.=" FROM `drueck` WHERE ((auftragsnr='$auftragsnr')) group by auftragsnr,teil";

//echo $pt."<br>";
$db->query($pt);


$viewname=$pcip.$views[1];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT auftragsnr,drueck.teil,sum(`Auss-Stück`) as summe_lieferung,sum(`Auss-Stück`*gew) as gew_auss_teil ";
$pt.=" FROM `drueck`";
$pt.=" join dkopf using(teil)";
$pt.=" WHERE ((auftragsnr='$auftragsnr')) group by auftragsnr,drueck.teil";
 
$db->query($pt);

$viewname=$pcip.$views[2];
$db->query("drop view $viewname");
$pt="create view $viewname";
$pt.=" as SELECT auftragsnr,sum(gew*`stück`) as import_gew FROM `dauftr` join dkopf ";
$pt.=" using(teil) WHERE ((auftragsnr='$auftragsnr') and (kzgut='G')) group by auftragsnr";

$db->query($pt);

// provedu dotaz nad vytvorenymi pohledy
$pt_D360=$pcip.$views[0];
$pt_D360_summe_lieferung=$pcip.$views[1];
$pt_D360_importgew=$pcip.$views[2];

$sql=" select auftragsnr,import_gew,teil,";
$sql.=" auss_1,auss_10,auss_20,auss_28,auss_34,auss_43,auss_50,auss_52,auss_60,";
$sql.=" auss_71,auss_75,auss_78,auss_83,auss_731,auss_735,auss_745,auss_991,auss_995,";
$sql.=" auss_celkem,summe_lieferung,summe_lieferung-auss_celkem as delta_auss,gew_auss_teil";
$sql.=" from $pt_D360 join $pt_D360_summe_lieferung";
$sql.=" using(auftragsnr,teil)  join $pt_D360_importgew using(auftragsnr)";

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
					'rootTag'=>'S370',
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
								'auss_1',
								'auss_10',
								'auss_20',
								'auss_28',
								'auss_34',
								'auss_43',
								'auss_50',
								'auss_52',
								'auss_60',
								'auss_71',
								'auss_75',
								'auss_78',
								'auss_83',
								'auss_731',
								'auss_735',
								'auss_745',
								'auss_991',
								'auss_995',
								'summe_lieferung',
								'sonst'=>'delta_auss',
								'gew_auss_teil'
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
//$domxml->save("D360.xml");

//header('Content-Type: application/xml');
//require_once('XML/Beautifier.php');
//$beautifier = new XML_Beautifier();
//print $beautifier->formatString($domxml->saveXML());

?>
